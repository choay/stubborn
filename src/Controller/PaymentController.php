<?php

namespace App\Controller;

use Stripe\StripeClient;
use Stripe\Exception\ApiErrorException;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class PaymentController extends AbstractController
{
    private StripeClient $stripeClient;

    public function __construct()
    {
        $stripeSecretKey = $_ENV['STRIPE_SECRET_KEY'] ?? null;

        if (!$stripeSecretKey) {
            throw new \RuntimeException('Stripe secret key not found in environment variables.');
        }

        $this->stripeClient = new StripeClient($stripeSecretKey);
    }

    #[Route('/create-checkout-session', name: 'create_checkout_session')]
    public function createCheckoutSession(): Response
    {
        try {
            $checkoutSession = $this->stripeClient->checkout->sessions->create([
                'payment_method_types' => ['card'],
                'line_items' => [
                    [
                        'price_data' => [
                            'currency' => 'usd',
                            'product_data' => [
                                'name' => 'T-shirt',
                            ],
                            'unit_amount' => 2000, // $20.00
                        ],
                        'quantity' => 1,
                    ],
                ],
                'mode' => 'payment',
                'success_url' => $this->generateUrl('payment_success', [], 0),
                'cancel_url' => $this->generateUrl('payment_cancel', [], 0),
            ]);

            return $this->redirect($checkoutSession->url);
        } catch (ApiErrorException $e) {
            return new Response('Erreur: ' . $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    #[Route('/payment-success', name: 'payment_success')]
    public function paymentSuccess(): Response
    {
        return new Response('Payment succeeded!', Response::HTTP_OK);
    }

    #[Route('/payment-cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        return new Response('Payment canceled.', Response::HTTP_OK);
    }
}
