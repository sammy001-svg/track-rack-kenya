<?php
namespace App\Core;

use RuntimeException;

/**
 * Minimal SMTP client — enough to send transactional mail reliably without
 * pulling in a dependency. Supports STARTTLS (587), implicit SSL (465),
 * plaintext, and AUTH LOGIN / AUTH PLAIN.
 */
class Smtp
{
    private $socket = null;
    private string $host;
    private int $port;
    private string $secure;
    private ?string $username;
    private ?string $password;
    private int $timeout;
    private array $transcript = [];

    public function __construct(array $config)
    {
        $this->host     = (string) ($config['host'] ?? '');
        $this->port     = (int) ($config['port'] ?? 587);
        $this->secure   = strtolower((string) ($config['secure'] ?? 'tls'));
        $this->username = ($config['username'] ?? '') !== '' ? (string) $config['username'] : null;
        $this->password = ($config['password'] ?? '') !== '' ? (string) $config['password'] : null;
        $this->timeout  = (int) ($config['timeout'] ?? 15);
    }

    /**
     * @param array $message from, from_name, to, subject, html, reply_to
     * @throws RuntimeException on any protocol failure
     */
    public function send(array $message): void
    {
        if ($this->host === '') {
            throw new RuntimeException('No SMTP host configured.');
        }

        $this->connect();

        try {
            $this->handshake();
            $this->authenticate();
            $this->transaction($message);
            $this->command('QUIT', [221]);
        } finally {
            $this->disconnect();
        }
    }

    // ---- Connection ----------------------------------------------------

    private function connect(): void
    {
        $transport = $this->secure === 'ssl' ? 'ssl://' : '';
        $context   = stream_context_create([
            'ssl' => ['verify_peer' => true, 'verify_peer_name' => true, 'allow_self_signed' => false],
        ]);

        $errno  = 0;
        $errstr = '';

        $socket = @stream_socket_client(
            $transport . $this->host . ':' . $this->port,
            $errno,
            $errstr,
            $this->timeout,
            STREAM_CLIENT_CONNECT,
            $context
        );

        if ($socket === false) {
            throw new RuntimeException("Could not connect to {$this->host}:{$this->port} — {$errstr} ({$errno})");
        }

        $this->socket = $socket;
        stream_set_timeout($this->socket, $this->timeout);

        $this->expect([220]);
    }

    private function disconnect(): void
    {
        if (is_resource($this->socket)) {
            @fclose($this->socket);
        }
        $this->socket = null;
    }

    private function handshake(): void
    {
        $domain = $this->clientDomain();

        $this->command('EHLO ' . $domain, [250]);

        if ($this->secure === 'tls') {
            $this->command('STARTTLS', [220]);

            $ok = @stream_socket_enable_crypto(
                $this->socket,
                true,
                STREAM_CRYPTO_METHOD_TLS_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
            );

            if ($ok !== true) {
                throw new RuntimeException('STARTTLS negotiation failed.');
            }

            // The session resets after TLS; greet again.
            $this->command('EHLO ' . $domain, [250]);
        }
    }

    private function authenticate(): void
    {
        if ($this->username === null || $this->password === null) {
            return;
        }

        $this->command('AUTH LOGIN', [334]);
        $this->command(base64_encode($this->username), [334]);
        $this->command(base64_encode($this->password), [235]);
    }

    private function transaction(array $message): void
    {
        $from = $message['from'];
        $to   = $message['to'];

        $this->command('MAIL FROM:<' . $from . '>', [250]);
        $this->command('RCPT TO:<' . $to . '>', [250, 251]);
        $this->command('DATA', [354]);

        $this->write($this->buildMessage($message) . "\r\n.\r\n");
        $this->expect([250]);
    }

    // ---- Message building ----------------------------------------------

    private function buildMessage(array $message): string
    {
        $boundary = 'tr_' . bin2hex(random_bytes(12));
        $html     = (string) $message['html'];
        $text     = trim(html_entity_decode(strip_tags(preg_replace('#<br\s*/?>#i', "\n", $html) ?? $html), ENT_QUOTES, 'UTF-8'));

        $headers = [
            'Date: ' . date('r'),
            'Message-ID: <' . bin2hex(random_bytes(10)) . '@' . $this->clientDomain() . '>',
            'From: ' . $this->encodeHeader((string) ($message['from_name'] ?? '')) . ' <' . $message['from'] . '>',
            'To: <' . $message['to'] . '>',
            'Subject: ' . $this->encodeHeader((string) $message['subject']),
            'MIME-Version: 1.0',
            'Content-Type: multipart/alternative; boundary="' . $boundary . '"',
        ];

        if (!empty($message['reply_to'])) {
            $headers[] = 'Reply-To: <' . $message['reply_to'] . '>';
        }

        $body = "--{$boundary}\r\n"
            . "Content-Type: text/plain; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
            . quoted_printable_encode($text) . "\r\n"
            . "--{$boundary}\r\n"
            . "Content-Type: text/html; charset=UTF-8\r\n"
            . "Content-Transfer-Encoding: quoted-printable\r\n\r\n"
            . quoted_printable_encode($html) . "\r\n"
            . "--{$boundary}--";

        // Dot-stuffing: a line that begins with "." must be escaped.
        $body = preg_replace('/^\./m', '..', $body) ?? $body;

        return implode("\r\n", $headers) . "\r\n\r\n" . $body;
    }

    private function encodeHeader(string $value): string
    {
        if ($value === '') {
            return '';
        }

        // Strip CR/LF so a header can never be injected.
        $value = str_replace(["\r", "\n"], '', $value);

        return preg_match('/[\x80-\xFF]/', $value)
            ? '=?UTF-8?B?' . base64_encode($value) . '?='
            : '"' . addcslashes($value, '"\\') . '"';
    }

    private function clientDomain(): string
    {
        $host = $_SERVER['SERVER_NAME'] ?? gethostname() ?: 'localhost';
        return preg_match('/^[a-z0-9.-]+$/i', $host) ? $host : 'localhost';
    }

    // ---- Protocol primitives -------------------------------------------

    private function command(string $command, array $expected): string
    {
        $this->write($command . "\r\n");
        return $this->expect($expected);
    }

    private function write(string $data): void
    {
        if (!is_resource($this->socket) || fwrite($this->socket, $data) === false) {
            throw new RuntimeException('Lost connection to the SMTP server.');
        }
    }

    private function expect(array $codes): string
    {
        $response = '';

        while (is_resource($this->socket)) {
            $line = fgets($this->socket, 1024);

            if ($line === false) {
                $meta = stream_get_meta_data($this->socket);
                throw new RuntimeException($meta['timed_out'] ? 'SMTP server timed out.' : 'SMTP server closed the connection.');
            }

            $response .= $line;
            $this->transcript[] = rtrim($line);

            // Multi-line replies use "250-" and end with "250 ".
            if (strlen($line) < 4 || $line[3] !== '-') {
                break;
            }
        }

        $code = (int) substr($response, 0, 3);

        if (!in_array($code, $codes, true)) {
            throw new RuntimeException('Unexpected SMTP reply: ' . trim($response));
        }

        return $response;
    }

    /** The protocol exchange, for logging a failure. */
    public function transcript(): array
    {
        return $this->transcript;
    }
}
