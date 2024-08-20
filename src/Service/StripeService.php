<?php

namespace App\Service;

use Stripe\StripeClient;

class StripeService
{
    private StripeClient $stripeClient;

    /**
     * StripeService constructor.
     *
     * @param string $stripeSecretKey The secret key for the Stripe API.
     */
    public function __construct(string $stripeSecretKey)
    {
        $this->stripeClient = new StripeClient($stripeSecretKey);
    }

    /**
     * Get the Stripe client instance.
     *
     * @return StripeClient
     */
    public function getStripeClient(): StripeClient
    {
        return $this->stripeClient;
    }
}
