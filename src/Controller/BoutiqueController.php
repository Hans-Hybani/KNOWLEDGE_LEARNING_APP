<?php

namespace App\Controller;

use App\Repository\CursusRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class BoutiqueController extends AbstractController
{
    #[Route('/boutique', name: 'app_boutique')]
    public function index(CursusRepository $cursusRepository): Response
    {
        $cursus = $cursusRepository->findAll();

        return $this->render('boutique/index.html.twig', [
            'cursus' => $cursus
        ]);
    }
}
