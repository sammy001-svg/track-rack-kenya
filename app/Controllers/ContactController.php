<?php
namespace App\Controllers;

use App\Core\Controller;
use App\Core\Mailer;
use App\Core\Session;
use App\Core\Validator;
use App\Models\Message;

class ContactController extends Controller
{
    public function index(): void
    {
        $this->view('site.contact', [
            'pageTitle' => 'Contact Us',
            'metaDesc'  => 'Visit Tack Rack at the MacNaughton Business Centre on Ngong Road, Nairobi, or call, email or WhatsApp us.',
            'bodyClass' => 'page-contact',
            'errors'    => Session::errors(),
        ]);

        Session::clearOld();
    }

    public function submit(): void
    {
        $validator = new Validator($_POST);
        $validator->honeypot('website')
            ->require('name', 'Your name')->max('name', 150, 'Your name')
            ->require('email', 'Email address')->email('email')->max('email', 190, 'Email address')
            ->phone('phone')->max('phone', 60, 'Phone number')
            ->max('subject', 200, 'Subject')
            ->require('body', 'Message')->min('body', 10, 'Message')->max('body', 5000, 'Message');

        if ($validator->fails()) {
            Session::flashErrors($validator->errors());
            Session::flashInput($_POST);
            Session::flash('error', 'Please correct the highlighted fields and try again.');
            $this->redirect('/contact');
        }

        (new Message())->create([
            'name'       => $validator->value('name'),
            'email'      => $validator->value('email'),
            'phone'      => $validator->value('phone') ?: null,
            'subject'    => $validator->value('subject') ?: 'Website enquiry',
            'body'       => $validator->value('body'),
            'ip_address' => $_SERVER['REMOTE_ADDR'] ?? null,
        ]);

        $recipient = (string) setting('contact_email', '');
        if ($recipient !== '') {
            Mailer::send(
                $recipient,
                'Website enquiry: ' . ($validator->value('subject') ?: 'No subject'),
                '<div style="font:14px/1.6 Helvetica,Arial,sans-serif">'
                    . '<p><strong>' . e($validator->value('name')) . '</strong><br>'
                    . e($validator->value('email')) . '<br>' . e($validator->value('phone')) . '</p>'
                    . '<p>' . nl2br(e($validator->value('body'))) . '</p></div>',
                $validator->value('email')
            );
        }

        Session::clearOld();
        Session::flash('success', 'Thank you. Your message has reached us and we will reply shortly.');
        $this->redirect('/contact');
    }
}
