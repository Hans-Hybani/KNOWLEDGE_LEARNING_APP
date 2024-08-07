<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Purchase;
use App\Service\StripePayment;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Stripe\Stripe;
use Stripe\Checkout\Session as StripeSession; // Correctly import StripeSession
use Symfony\Bundle\SecurityBundle\Security;

class PaymentController extends AbstractController
{
    private StripePayment $stripePayment;
    private EntityManagerInterface $entityManager;
    private Security $security;

    // Inject dependencies via constructor
    public function __construct(StripePayment $stripePayment, EntityManagerInterface $entityManager, Security $security)
    {
        $this->stripePayment = $stripePayment;
        $this->entityManager = $entityManager;
        $this->security = $security;
    }

    #[Route('/purchase/{type}/{id}', name: 'purchase_item')]
    public function purchaseItem(string $type, int $id): Response
    {
        // Check if the user is fully authenticated
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('notice', 'Please log in or create an account to make a purchase.');
            return $this->redirectToRoute('app_home'); // Redirect to homepage if not authenticated
        }

        $items = [];

        // Handle purchase for 'cursus'
        if ($type === 'cursus') {
            $cursus = $this->entityManager->getRepository(Cursus::class)->find($id);
            if (!$cursus) {
                throw $this->createNotFoundException('Cursus not found.');
            }
            $items[] = [
                'name' => $cursus->getTitle(),
                'price' => $cursus->getPrice(), // Price should be in cents
            ];
        } 
        // Handle purchase for 'lesson'
        elseif ($type === 'lesson') {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
            if (!$lesson) {
                throw $this->createNotFoundException('Lesson not found.');
            }
            $items[] = [
                'name' => $lesson->getTitle(),
                'price' => $lesson->getPrice(), // Price should be in cents
            ];
        } 
        // Handle invalid type
        else {
            throw $this->createNotFoundException('Invalid type.');
        }

        // Start the payment process
        $this->stripePayment->startPayment($items);

        // Redirect to Stripe payment page
        return $this->redirect($this->stripePayment->getStripeRedirectUrl());
    }
    
    #[Route('/pay/success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        // Check if the user is fully authenticated
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('notice', 'Please log in or create an account to make a purchase.');
            return $this->redirectToRoute('app_home'); // Redirect to homepage if not authenticated
        }

        $sessionId = $request->query->get('session_id');
        
        // Check if the session ID is provided
        if (!$sessionId) {
            $this->addFlash('error', 'Session ID missing.');
            return $this->redirectToRoute('app_home');
        }

        // Set the Stripe API key
        Stripe::setApiKey($_ENV['STRIPE_SECRET']);

        try {
            // Retrieve the Stripe Checkout session
            $session = StripeSession::retrieve($sessionId);

            // Verify that the session is valid and payment was successful
            if (!$session || $session->payment_status !== 'paid') {
                $this->addFlash('error', 'Payment failed.');
                return $this->redirectToRoute('app_home');
            }

            $items = json_decode($session->metadata->items, true);

            $user = $this->getUser(); // Get the current authenticated user

            if (!$user) {
                $this->addFlash('error', 'User not found.');
                return $this->redirectToRoute('app_home');
            }

            // Process each item in the payment session
            foreach ($items as $item) {
                if (isset($item['name'], $item['type'])) {
                    $purchase = new Purchase();
                    $purchase->setUserPurchase($user);

                    if ($item['type'] === 'lesson') {
                        $lesson = $this->entityManager->getRepository(Lesson::class)->findOneBy(['title' => $item['name']]);
                        if ($lesson) {
                            $purchase->setLesson($lesson);
                        }
                    } elseif ($item['type'] === 'cursus') {
                        $cursus = $this->entityManager->getRepository(Cursus::class)->findOneBy(['title' => $item['name']]);
                        if ($cursus) {
                            $purchase->setCursus($cursus);
                        }
                    }

                    $this->entityManager->persist($purchase);
                }
            }

            $this->entityManager->flush(); // Save all purchases to the database

            $this->addFlash('success', 'Payment successful!');
            return $this->redirectToRoute('app_home');

        } catch (\Exception $e) {
            $this->addFlash('error', 'An error occurred: ' . $e->getMessage());
            return $this->redirectToRoute('app_home');
        }
    }

    #[Route('/pay/cancel', name: 'payment_cancel')]
    public function paymentCancel(): Response
    {
        // Check if the user is fully authenticated
        if (!$this->security->isGranted('IS_AUTHENTICATED_FULLY')) {
            $this->addFlash('notice', 'Please log in or create an account to make a purchase.');
            return $this->redirectToRoute('app_home'); // Redirect to homepage if not authenticated
        }
        
        $this->addFlash('error', 'Payment cancelled.');
        return $this->redirectToRoute('app_home');
    }
}
