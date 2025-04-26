<?php

namespace App\Services\Newsletter;

use App\Services\Newsletter\Contracts\NewsletterContract;
use Exception;
use GuzzleHttp\Exception\RequestException;
use MailchimpMarketing\ApiClient;

class MailChimpNewsletter implements NewsletterContract
{
    protected ApiClient $mailchimp;

    /**
     * MailChimpNewsletter constructor.
     */
    public function __construct(ApiClient $mailchimp)
    {
        $this->mailchimp = $mailchimp;
    }

    /**
     * @throws Exception
     */
    public function subscribe($listId, $email, array $mergeVars = [])
    {
        try {
            $this->mailchimp->lists->setListMember($listId, md5(strtolower($email)), [
                'email_address' => $email,
                'status_if_new' => 'subscribed',
                'merge_fields' => $mergeVars,
            ]);
        } catch (RequestException $requestException) {
            \Log::info($requestException->getMessage());
            throw new Exception($requestException->getMessage());
        }
    }

    /**
     * @throws Exception
     */
    public function unsubscribe($listId, $email, array $mergeVars = [])
    {
        try {
            $this->mailchimp->lists->deleteListMember($listId, md5(strtolower($email)));
        } catch (RequestException $requestException) {
            \Log::info($requestException->getMessage());
            throw new Exception($requestException->getMessage());
        }
    }

    public function updateMember($listId, $email, array $mergeVars = [])
    {
        $this->mailchimp->lists->updateListMember($listId, md5(strtolower($email)), [
            'merge_fields' => $mergeVars,
        ]);
    }

    public function addTags($listId, $email, array $mergeVars = [])
    {
        $this->mailchimp->lists->updateListMemberTags($listId, md5(strtolower($email)), [
            'tags' => $mergeVars,
        ]);
    }

    public function addEvent($listId, $email, $name)
    {
        $this->mailchimp->lists->createListMemberEvent($listId, md5(strtolower($email)), [
            'name' => $name,
        ]);
    }
}
