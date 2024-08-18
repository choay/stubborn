<?php

namespace App\Service;

use Stripe\StripeClient;

class stripe_service
{
    private StripeClient $stripeClient;

    public function __construct(string $stripeSecretKey)
    {
        $this->stripeClient = new StripeClient($stripeSecretKey);
    }

    public function getStripeClient(): StripeClient
    {
        return $this->stripeClient;
    }
}
