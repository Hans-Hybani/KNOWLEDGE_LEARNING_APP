<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\Theme;
use App\Form\CursusType;
use App\Repository\CursusRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CursusController extends AbstractController
{
    #[Route('/admin/cursus', name: 'app_cursus')]
    public function index(CursusRepository $cursusRepository): Response
    {
        $cursus = $cursusRepository->findAll();

        return $this->render('cursus/index.html.twig', [
            'cursus' => $cursus,
        ]);
    }

    #[Route('/cursus/{id}/show', name: 'app_cursus_show')]
    public function showCursus(Theme $theme, CursusRepository $cursusRepository): Response
    {
        $cursus = $cursusRepository->findBy(['theme' => $theme]);

        return $this->render('cursus/show.html.twig', [
            'cursus' => $cursus,
            'theme' => $theme,
        ]);
    }

    #[Route('/cursus/new', name: 'app_cursus_new')]
    public function newCursus(Request $request, EntityManagerInterface $entityManager): Response
    {
        $cursus = new Cursus();
        $form = $this->createForm(CursusType::class, $cursus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($cursus);
            $entityManager->flush();

            return $this->redirectToRoute('app_cursus');
        }

        return $this->render('cursus/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/{id}/edit', name: 'app_cursus_edit')]
    public function editCursus(Request $request, Cursus $cursus, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(CursusType::class, $cursus);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_cursus');
        }

        return $this->render('cursus/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/{id}/delete', name: 'app_cursus_delete')]
    public function deleteCursus(Request $request, Cursus $cursus, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$cursus->getId(), $request->request->get('_token'))) {
            $entityManager->remove($cursus);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_cursus');
    }
}
