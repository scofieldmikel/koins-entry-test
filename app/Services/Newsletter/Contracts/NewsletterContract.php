<?php

namespace App\Services\Newsletter\Contracts;

interface NewsletterContract
{
    public function subscribe($listId, $email, array $mergeVars = []);

    public function unsubscribe($listId, $email, array $mergeVars = []);

    public function addTags($listId, $email, array $mergeVars = []);

    public function addEvent($listId, $email, $name);
}
