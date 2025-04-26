<?php

namespace App\Services\Emails;

use Exception;
use Illuminate\Notifications\Notification;
use Psr\Http\Client\ClientExceptionInterface;

class MailgunChannel
{
    protected MailgunService $mailgun;

    public function __construct(MailgunService $mailgun)
    {
        $this->mailgun = $mailgun;
    }

    /**
     * @throws Exception
     * @throws \Exception
     * @throws ClientExceptionInterface
     */
    public function send($notifiable, Notification $notification): void
    {
        if (! $to = $notifiable->routeNotificationFor('mailgun')) {
            throw new Exception('User does not have an email address.');
        }

        $template = $notification->toMailGun($notifiable);

        if (is_string($template)) {
            $template = new MailgunMessage($template);
        }

        if (! $template->subject) {
            throw new \Exception('Please set a subject for your email.');
        }

        if (! config('mail.from.address')) {
            throw new \Exception('Please set a from address for your email.');
        }

        try {
            $this->mailgun->sendTemplateEmail(
                $template->template,
                $to,
                $template->subject,
                $template->data,
                empty($template->cc) ? null : $template->cc,
                empty($template->reply_to) ? null : $template->reply_to
            );
        } catch (Exception $e) {
            throw new \Exception($e->getMessage());
        }
    }
}
