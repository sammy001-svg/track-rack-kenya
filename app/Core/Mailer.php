<?php
namespace App\Core;

use App\Models\Setting;
use Throwable;

/**
 * Transactional mail. Prefers SMTP (configured in Admin → Settings → mail),
 * falls back to PHP mail(), and never throws: every enquiry is written to the
 * database before mail is attempted, so a delivery failure loses nothing.
 */
class Mailer
{
    public static function send(string $to, string $subject, string $htmlBody, ?string $replyTo = null): bool
    {
        if (!filter_var($to, FILTER_VALIDATE_EMAIL)) {
            self::log("Invalid recipient '{$to}' for '{$subject}'");
            return false;
        }

        $subject = str_replace(["\r", "\n"], '', $subject);
        $html    = self::wrap($subject, $htmlBody);

        if (Setting::get('smtp_enabled', '0') === '1') {
            return self::viaSmtp($to, $subject, $html, $replyTo);
        }

        if (!empty(config('mail.enabled'))) {
            return self::viaMailFunction($to, $subject, $html, $replyTo);
        }

        self::log("Mail disabled — skipped '{$subject}' to {$to}");
        return false;
    }

    /** Send the same message to several recipients; true if any succeeded. */
    public static function sendMany(array $recipients, string $subject, string $htmlBody, ?string $replyTo = null): bool
    {
        $sent = false;

        foreach (array_unique(array_filter($recipients)) as $recipient) {
            $sent = self::send($recipient, $subject, $htmlBody, $replyTo) || $sent;
        }

        return $sent;
    }

    private static function viaSmtp(string $to, string $subject, string $html, ?string $replyTo): bool
    {
        $from = Setting::get('smtp_from', config('mail.from_email', 'no-reply@tackrack.co.ke'));

        try {
            (new Smtp([
                'host'     => Setting::get('smtp_host', ''),
                'port'     => (int) Setting::get('smtp_port', '587'),
                'secure'   => Setting::get('smtp_secure', 'tls'),
                'username' => Setting::get('smtp_user', ''),
                'password' => Setting::get('smtp_pass', ''),
            ]))->send([
                'from'      => $from,
                'from_name' => config('mail.from_name', 'Tack Rack'),
                'to'        => $to,
                'subject'   => $subject,
                'html'      => $html,
                'reply_to'  => ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) ? $replyTo : null,
            ]);

            self::log("SMTP sent '{$subject}' to {$to}");
            return true;
        } catch (Throwable $e) {
            self::log("SMTP FAILED '{$subject}' to {$to} — " . $e->getMessage());
            return false;
        }
    }

    private static function viaMailFunction(string $to, string $subject, string $html, ?string $replyTo): bool
    {
        $headers = [
            'MIME-Version: 1.0',
            'Content-Type: text/html; charset=UTF-8',
            sprintf('From: %s <%s>', config('mail.from_name'), config('mail.from_email')),
        ];

        if ($replyTo !== null && filter_var($replyTo, FILTER_VALIDATE_EMAIL)) {
            $headers[] = 'Reply-To: ' . $replyTo;
        }

        $sent = @mail($to, $subject, $html, implode("\r\n", $headers));

        self::log(($sent ? "mail() sent" : "mail() FAILED") . " '{$subject}' to {$to}");

        return $sent;
    }

    /**
     * Wrap body content in a branded, email-client-safe shell.
     * Table-based and inline-styled, because that is what survives Outlook.
     */
    public static function wrap(string $title, string $content): string
    {
        $siteName = e(Setting::get('site_name', 'Tack Rack'));
        $address  = e(Setting::get('contact_address', ''));
        $phone    = e(Setting::get('contact_phone', ''));
        $email    = e(Setting::get('contact_email', ''));
        $year     = date('Y');

        return <<<HTML
<!doctype html>
<html><head><meta charset="utf-8"><meta name="viewport" content="width=device-width,initial-scale=1">
<title>{$title}</title></head>
<body style="margin:0;padding:0;background:#F7F4EF;">
<table role="presentation" width="100%" cellpadding="0" cellspacing="0" style="background:#F7F4EF;padding:28px 12px;">
<tr><td align="center">
  <table role="presentation" width="600" cellpadding="0" cellspacing="0" style="max-width:600px;width:100%;background:#ffffff;border:1px solid #E3DFD7;border-radius:6px;overflow:hidden;">

    <tr><td style="background:#14110E;padding:22px 28px;">
      <span style="font-family:Georgia,serif;font-size:22px;color:#F7F4EF;letter-spacing:-.5px;">{$siteName}</span>
      <span style="font-family:Helvetica,Arial,sans-serif;font-size:10px;letter-spacing:2px;color:#B99149;text-transform:uppercase;padding-left:8px;">Equine Supplies</span>
    </td></tr>

    <tr><td style="padding:28px;font-family:Helvetica,Arial,sans-serif;font-size:14px;line-height:1.65;color:#1B1815;">
      {$content}
    </td></tr>

    <tr><td style="background:#FAF9F7;border-top:1px solid #E3DFD7;padding:20px 28px;font-family:Helvetica,Arial,sans-serif;font-size:12px;line-height:1.6;color:#6B655C;">
      {$address}<br>
      {$phone} &nbsp;&middot;&nbsp; <a href="mailto:{$email}" style="color:#8A5A2B;">{$email}</a>
      <div style="margin-top:10px;color:#9A9288;">&copy; {$year} {$siteName} Limited</div>
    </td></tr>

  </table>
</td></tr>
</table>
</body></html>
HTML;
    }

    /** Standard button for use inside email bodies. */
    public static function button(string $label, string $url): string
    {
        return '<table role="presentation" cellpadding="0" cellspacing="0" style="margin:22px 0;"><tr>'
            . '<td style="background:#14110E;border-radius:4px;">'
            . '<a href="' . e($url) . '" style="display:inline-block;padding:12px 24px;font-family:Helvetica,Arial,sans-serif;'
            . 'font-size:13px;font-weight:bold;letter-spacing:1px;text-transform:uppercase;color:#F7F4EF;text-decoration:none;">'
            . e($label) . '</a></td></tr></table>';
    }

    private static function log(string $line): void
    {
        $file = dirname(__DIR__, 2) . '/storage/logs/mail.log';
        $dir  = dirname($file);

        if (!is_dir($dir)) {
            @mkdir($dir, 0775, true);
        }

        @file_put_contents($file, '[' . date('Y-m-d H:i:s') . '] ' . $line . PHP_EOL, FILE_APPEND);
    }
}
