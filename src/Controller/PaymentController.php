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
use Stripe\Checkout\Session as StripeSession; // Importez correctement StripeSession

class PaymentController extends AbstractController
{
    private StripePayment $stripePayment;
    private EntityManagerInterface $entityManager;

    public function __construct(StripePayment $stripePayment, EntityManagerInterface $entityManager)
    {
        $this->stripePayment = $stripePayment;
        $this->entityManager = $entityManager;
    }

    #[Route('/purchase/{type}/{id}', name: 'purchase_item')]
    public function purchaseItem(string $type, int $id): Response
    {
        $items = [];

        if ($type === 'cursus') {
            $cursus = $this->entityManager->getRepository(Cursus::class)->find($id);
            if (!$cursus) {
                throw $this->createNotFoundException('Cursus not found.');
            }
            $items[] = [
                'name' => $cursus->getTitle(),
                'price' => $cursus->getPrice(), // Le prix doit être en centimes
            ];
        } elseif ($type === 'lesson') {
            $lesson = $this->entityManager->getRepository(Lesson::class)->find($id);
            if (!$lesson) {
                throw $this->createNotFoundException('Lesson not found.');
            }
            $items[] = [
                'name' => $lesson->getTitle(),
                'price' => $lesson->getPrice(), // Le prix doit être en centimes
            ];
        } else {
            throw $this->createNotFoundException('Invalid type.');
        }

        $this->stripePayment->startPayment($items);

        return $this->redirect($this->stripePayment->getStripeRedirectUrl());
    }
    
    #[Route('/pay/success', name: 'payment_success')]
    public function paymentSuccess(Request $request): Response
    {
        $sessionId = $request->query->get('session_id');
        
        if (!$sessionId) {
            $this->addFlash('error', 'Session ID missing.');
            return $this->redirectToRoute('app_home');
        }

        Stripe::setApiKey($_ENV['STRIPE_SECRET']);

        try {
            $session = StripeSession::retrieve($sessionId);

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

            $this->entityManager->flush();

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
        $this->addFlash('error', 'Payment cancelled.');
        return $this->redirectToRoute('app_home');
    }
}
