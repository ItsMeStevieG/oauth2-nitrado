<?php

declare(strict_types=1);

namespace ItsMeStevieG\OAuth2\Client\Provider;

use League\OAuth2\Client\Provider\ResourceOwnerInterface;

class NitradoResourceOwner implements ResourceOwnerInterface
{
    /**
     * @var array
     */
    protected $data;

    public function __construct(array $response)
    {
        $this->data = $response['data'];
        //die("<pre>".print_r($response,true)."</pre>");
    }

    public function getId(): ?string
    {
        return $this->data['user']['user_id'] ?? null;
    }

    public function getUserName(): ?string
    {
        return $this->data['user']['username'] ?? null;
    }

    public function getTimeZone(): string
    {
        return $this->data['user']['timezone'] ?? null;
    }

    public function getEmail(): ?string
    {
        return $this->data['user']['email'] ?? null;
    }

    public function getAvatar(): string
    {
        return $this->data['user']['avatar'] ?? null;
    }

    public function getCurrency(): string
    {
        return $this->data['user']['currency'] ?? null;
    }

    public function getCredit(): string
    {
        return $this->data['user']['credit'] ?? null;
    }

    public function getEmployee(): string
    {
        return $this->data['user']['employee'] ?? null;
    }

    public function getPartnerId(): string
    {
        return $this->data['user']['partner_id'] ?? null;
    }

    public function getName(): string
    {
        return $this->data['user']['profile']['name'] ?? null;
    }

    public function getStreet(): string
    {
        return $this->data['user']['profile']['street'] ?? null;
    }

    public function getCity(): string
    {
        return $this->data['user']['profile']['city'] ?? null;
    }

    public function getState(): string
    {
        return $this->data['user']['profile']['state'] ?? null;
    }

    public function getPostCode(): string
    {
        return $this->data['user']['profile']['postcode'] ?? null;
    }

    public function getCountry(): ?string
    {
        return $this->data['user']['profile']['country'] ?? null;
    }

    /**
     * Return all of the owner details available as an array.
     */
    public function toArray(): array
    {
        return $this->data;
    }
}
