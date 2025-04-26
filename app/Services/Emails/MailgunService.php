<?php

namespace App\Services\Emails;

use Mailgun\Mailgun;
use Mailgun\Model\Message\SendResponse;
use Psr\Http\Client\ClientExceptionInterface;
use Psr\Http\Message\ResponseInterface;

class MailgunService
{
    public Mailgun $mailgun;

    public function __construct()
    {

        $this->mailgun = Mailgun::create(
            config('services.mailgun.secret')
        );
    }

    /**
     * @throws ClientExceptionInterface
     */
    public function sendTemplateEmail($template, $email, $subject, $data, $cc = null, $reply_to = null): SendResponse|ResponseInterface
    {
        $email = config('app.env') == 'production' ? $email : 'app-releases-aaaae373o5raizcjmyl6zah5km@flickwheel.slack.com';

        return $this->mailgun->messages()->send(config('services.mailgun.domain'), [
            'from' => config('mail.from.name').' <'.config('mail.from.address').'>',
            'to' => $email,
            empty($cc) ? null : 'cc' => $cc,
            empty($reply_to) ? null : 'h:Reply-To' => $reply_to,
            'subject' => $subject,
            'template' => $template,
            'h:X-Mailgun-Variables' => json_encode($data),
        ]);
    }
}
