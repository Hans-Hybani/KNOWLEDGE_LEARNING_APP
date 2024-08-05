<?php

namespace App\Service;

// use Stripe\Checkout\Session as StripeSession;
use Stripe\Checkout\Session;
use Stripe\Stripe;

class StripePayment
{
    private ?string $redirectUrl = null;

    public function __construct()
    {
        // Utilisez les variables d'environnement pour la clé API Stripe
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);  
        Stripe::setApiVersion('2024-06-20');
    }

    public function startPayment(array $items): void
    {
        $lineItems = [];

        foreach ($items as $item) {
            $lineItems[] = [
                'quantity' => 1,
                'price_data' => [
                    'currency' => 'eur',
                    'product_data' => [
                        'name' => $item['name'],
                    ],
                    'unit_amount' => $item['price'] * 100, // Le prix doit être en centimes
                ],
            ];
        }

        // Créez la session de paiement avec Stripe
        // $session = StripeSession::create([
        //     'line_items' => $lineItems,
        //     'mode' => 'payment', // Le mode doit être 'payment'
        //     'cancel_url' => 'http://127.0.0.1:8000/pay/cancel',
        //     'success_url' => 'http://127.0.0.1:8000/pay/success',
        //     'metadata' => [
        //         'items' => json_encode($items),
        //     ],
        // ]);
            $session = Session::create([
                'payment_method_types' => ['card'],
                'line_items' => [$lineItems],
                'mode' => 'payment',
                'success_url' => $_ENV['APP_URL'] . '/pay/success?session_id={CHECKOUT_SESSION_ID}',
                'cancel_url' => $_ENV['APP_URL'] . '/pay/cancel',
            ]);

        $this->redirectUrl = $session->url;
    }

    public function getStripeRedirectUrl(): ?string
    {
        return $this->redirectUrl;
    }
}
// src/Service/StripePayment.php
// namespace App\Service;

// use Stripe\Checkout\Session;
// use Stripe\Stripe;

// class StripePayment
// {
//     private $stripeRedirectUrl;

//     public function __construct()
//     {
//         Stripe::setApiKey($_ENV['STRIPE_SECRET']);
//     }

//     public function startPayment(array $items): void
//     {
//         $lineItems = [];

//         foreach ($items as $item) {
//             $lineItems[] = [
//                 'price_data' => [
//                     'currency' => 'eur',
//                     'product_data' => [
//                         'name' => $item['name'],
//                     ],
//                     'unit_amount' => $item['price'],
//                 ],
//                 'quantity' => 1,
//             ];
//         }

//         $session = Session::create([
//             'payment_method_types' => ['card'],
//             'line_items' => [$lineItems],
//             'mode' => 'payment',
//             'success_url' => $_ENV['APP_URL'] . '/pay/success?session_id={CHECKOUT_SESSION_ID}',
//             'cancel_url' => $_ENV['APP_URL'] . '/pay/cancel',
//         ]);

//         $this->stripeRedirectUrl = $session->url;
//     }

//     public function getStripeRedirectUrl(): string
//     {
//         return $this->stripeRedirectUrl;
//     }
// }
