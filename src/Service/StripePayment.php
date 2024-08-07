<?php

namespace App\Service;

use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePayment
{
    private ?string $redirectUrl = null;

    public function __construct()
    {
        // Set the Stripe API key from environment variables
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);  
        // Set the API version to ensure compatibility with Stripe API features
        Stripe::setApiVersion('2024-06-20');
    }

    public function startPayment(array $items): void
    {
        $lineItems = [];

        // Prepare line items for Stripe Checkout session
        foreach ($items as $item) {
            $lineItems[] = [
                'quantity' => 1, // The quantity of the item
                'price_data' => [
                    'currency' => 'eur', // Currency for the transaction
                    'product_data' => [
                        'name' => $item['name'], // Item name for display in Stripe Checkout
                    ],
                    'unit_amount' => $item['price'] * 100, // Price in the smallest currency unit (cents for EUR)
                ],
            ];
        }
        
        // Create a new Stripe Checkout session
        $session = Session::create([
            'payment_method_types' => ['card'], // Allowed payment methods
            'line_items' => $lineItems, // Items to be included in the checkout
            'mode' => 'payment', // Mode of the checkout session, can be 'payment', 'setup', or 'subscription'
            'success_url' => $_ENV['APP_URL'] . '/pay/success?session_id={CHECKOUT_SESSION_ID}', // URL to redirect after successful payment
            'cancel_url' => $_ENV['APP_URL'] . '/pay/cancel', // URL to redirect if the payment is cancelled
        ]);

        // Store the URL to redirect the user to Stripe Checkout
        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl(): ?string
    {
        // Return the URL for redirecting to Stripe Checkout
        return $this->redirectUrl;
    }
}
