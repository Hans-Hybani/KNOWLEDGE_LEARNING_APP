<?php

namespace App\Controller;

use App\Entity\Certification;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Form\CertificationType;
use App\Repository\CertificationRepository;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Filesystem\Exception\IOExceptionInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

class CertificationController extends AbstractController
{
    private $entityManager;

    public function __construct(EntityManagerInterface $entityManager)
    {
        $this->entityManager = $entityManager;
    }

    #[Route('/certifications', name: 'certification')]
    public function index(CertificationRepository $certification): Response
    {
        $certification = $certification->findAll();

        return $this->render('certification/index.html.twig', [
            'certifications' => $certification,
        ]);
    }

    #[Route('/certification/create', name: 'app_certification_new')]
    public function CreateCertification(Request $request): Response
    {
        $certification = new Certification();

        $cursusChoices = $this->entityManager->getRepository(Cursus::class)->findAll();
        $lessonChoices = $this->entityManager->getRepository(Lesson::class)->findAll();

        $form = $this->createForm(CertificationType::class, $certification, [
            'cursus_choices' => $cursusChoices,
            'lesson_choices' => $lessonChoices,
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('certificationDoc')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('certification_directory'),
                        $newFilename
                    );
                    $certification->setCertificationDoc('uploads/certifications/' . $newFilename);
                } catch (IOExceptionInterface $exception) {
                    $this->addFlash('error', 'Failed to upload PDF file.');
                }
            }

            $this->entityManager->persist($certification);
            $this->entityManager->flush();

            $this->addFlash('success', 'Certification saved successfully!');

            return $this->redirectToRoute('certification');
        }

        return $this->render('certification/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    #[Route('/certification/edit/{id}', name: 'certification_edit')]
    public function edit(Request $request, Certification $certification, EntityManagerInterface $em): Response
    {
        $form = $this->createForm(CertificationType::class, $certification);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $pdfFile = $form->get('certificationDoc')->getData();
            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('certification_directory'),
                        $newFilename
                    );
                } catch (IOExceptionInterface $exception) {
                }

                $certification->setCertificationDoc('uploads/certifications/' . $newFilename);
            }
            
            $em->flush();

            $this->addFlash('success', 'Certification updated successfully!');

            return $this->redirectToRoute('certification');
        }

        return $this->render('certification/edit.html.twig', [
            'form' => $form->createView(),
            'certification' => $certification,
        ]);
    }

    #[Route('/certification/delete/{id}', name: 'certification_delete', methods: ['POST'])]
    public function delete(Request $request, Certification $certification, EntityManagerInterface $em): Response
    {
        if ($this->isCsrfTokenValid('delete' . $certification->getId(), $request->request->get('_token'))) {
            $em->remove($certification);
            $em->flush();

            $this->addFlash('success', 'Certification deleted successfully!');
        }

        return $this->redirectToRoute('certification');
    }
}
