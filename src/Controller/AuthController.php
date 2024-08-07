<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;

class AuthController extends AbstractController
{
    #[Route(path: '/login', name: 'app_login')]
    public function login(AuthenticationUtils $authenticationUtils): Response
    {
        // Get the last authentication error, if any
        $error = $authenticationUtils->getLastAuthenticationError();

        // Retrieve the last username entered in the login form
        $lastUsername = $authenticationUtils->getLastUsername();

        // Render the login form view, passing the last username and any authentication errors
        return $this->render('security/login.html.twig', [
            'last_username' => $lastUsername, 
            'error' => $error
        ]);
    }

    #[Route(path: '/logout', name: 'app_logout')]
    public function logout(): void
    {
        // This method is intentionally left blank. The logout functionality is handled by the firewall configuration.
        throw new \LogicException('This method can be blank - it will be intercepted by the logout key on your firewall.');
    }

}
