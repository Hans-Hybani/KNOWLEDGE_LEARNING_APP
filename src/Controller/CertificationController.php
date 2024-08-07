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

    #[Route('/admin/certifications', name: 'certification')]
    public function index(CertificationRepository $certification): Response
    {
        $certification = $certification->findAll();

        return $this->render('certification/index.html.twig', [
            'certifications' => $certification,
        ]);
    }

    #[Route('/admin/certification/create', name: 'app_certification_new')]
    public function CreateCertification(Request $request): Response
    {
        // Create a new instance of the Certification entity
        $certification = new Certification();

        // Fetch all Cursus and Lesson entities to use as choices in the form
        $cursusChoices = $this->entityManager->getRepository(Cursus::class)->findAll();
        $lessonChoices = $this->entityManager->getRepository(Lesson::class)->findAll();

        // Create the Certification form with choices for Cursus and Lesson
        $form = $this->createForm(CertificationType::class, $certification, [
            'cursus_choices' => $cursusChoices,
            'lesson_choices' => $lessonChoices,
        ]);

        // Handle the form submission
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Retrieve the uploaded PDF file from the form
            $pdfFile = $form->get('certificationDoc')->getData();

            // If a file is provided, process the file upload
            if ($pdfFile) {
                // Generate a safe and unique filename for the PDF
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    // Move the uploaded file to the designated directory
                    $pdfFile->move(
                        $this->getParameter('certification_directory'),
                        $newFilename
                    );
                    // Set the file path in the Certification entity
                    $certification->setCertificationDoc('uploads/certifications/' . $newFilename);
                } catch (IOExceptionInterface $exception) {
                    // Display an error message if the file upload fails
                    $this->addFlash('error', 'Failed to upload PDF file.');
                }
            }

            // Persist the Certification entity and save it to the database
            $this->entityManager->persist($certification);
            $this->entityManager->flush();

            // Display a success message and redirect to the certification listing page
            $this->addFlash('success', 'Certification saved successfully!');
            return $this->redirectToRoute('certification');
        }

        // Render the form view if the form is not submitted or invalid
        return $this->render('certification/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }
    
    #[Route('/admin/certification/edit/{id}', name: 'certification_edit')]
    public function edit(Request $request, Certification $certification, EntityManagerInterface $em): Response
    {
        // Create and handle the Certification form
        $form = $this->createForm(CertificationType::class, $certification);
        $form->handleRequest($request);

        // Check if the form is submitted and valid
        if ($form->isSubmitted() && $form->isValid()) {
            // Get the uploaded PDF file from the form
            $pdfFile = $form->get('certificationDoc')->getData();
            
            // Process the file if it's provided
            if ($pdfFile) {
                // Generate a safe and unique filename for the PDF
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = preg_replace('/[^a-zA-Z0-9]/', '_', $originalFilename);
                $newFilename = $safeFilename . '-' . uniqid() . '.' . $pdfFile->guessExtension();

                try {
                    // Move the file to the designated directory
                    $pdfFile->move(
                        $this->getParameter('certification_directory'),
                        $newFilename
                    );
                } catch (IOExceptionInterface $exception) {
                    // Optionally, handle the exception if file upload fails
                }

                // Update the Certification entity with the new file path
                $certification->setCertificationDoc('uploads/certifications/' . $newFilename);
            }

            // Save changes to the database
            $em->flush();

            // Add a success message and redirect to the certification listing page
            $this->addFlash('success', 'Certification updated successfully!');
            return $this->redirectToRoute('certification');
        }

        // Render the form view if the form is not submitted or invalid
        return $this->render('certification/edit.html.twig', [
            'form' => $form->createView(),
            'certification' => $certification,
        ]);
    }

    #[Route('/admin/certification/delete/{id}', name: 'certification_delete', methods: ['POST'])]
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
