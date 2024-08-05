<?php

namespace App\Controller;

use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class VerificationController extends AbstractController
{
    #[Route('/verify/{token}', name: 'app_verify_email')]
    public function verify(string $token, UserRepository $userRepository, EntityManagerInterface $entityManager): Response
    {
        $user = $userRepository->findOneBy(['activationToken' => $token]);

        if (!$user) {
            throw $this->createNotFoundException('User not found');
        }

        $user->setIsActive(true);
        $user->setActivationToken(null);

        $entityManager->persist($user);
        $entityManager->flush();

        return new Response('Your email has been verified!');
    }
}
