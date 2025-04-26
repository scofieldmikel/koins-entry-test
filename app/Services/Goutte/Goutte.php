<?php

namespace App\Services\Goutte;

use Goutte\Client;

class Goutte implements GoutteContract
{
    protected Client $client;

    protected $crawler;

    public function __construct(Client $client)
    {
        $this->client = $client;
    }

    public function setUrl($url): static
    {
        $this->crawler = $this->client->request('GET', strtolower($url));

        return $this;
    }

    public function selectImage($alt)
    {
        try {
            return $this->crawler->selectImage(strtolower($alt))->image()->getUri();
        } catch (\Exception $e) {
            return null;
        }
    }

    public function extractData($data)
    {
        return $this->crawler->filter($data)->each(function ($node) {
            return $node->text();
        });
    }
}
