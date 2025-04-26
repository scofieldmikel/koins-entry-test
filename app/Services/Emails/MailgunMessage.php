<?php

namespace App\Services\Emails;

class MailgunMessage
{
    public string $template;

    public string $subject;

    public string $from;

    public string $cc = '';

    public string $reply_to = '';

    public array $data;

    public function __construct(string $template = '')
    {
        $this->template = $template;
    }

    public function template($template): self
    {
        $this->template = $template;

        return $this;
    }

    public function subject($subject): self
    {
        $this->subject = $subject;

        return $this;
    }

    public function cc($cc): self
    {
        $this->cc = $cc;

        return $this;
    }

    public function reply_to($reply_to): self
    {
        $this->reply_to = $reply_to;

        return $this;
    }

    public function data($data): self
    {
        $this->data = $data;

        return $this;
    }
}
