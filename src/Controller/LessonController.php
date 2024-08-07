<?php

namespace App\Controller;

use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use App\Repository\ThemeRepository;
use App\Service\AccessChecker;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class LessonController extends AbstractController
{
    #[Route('/admin/lesson', name: 'app_lesson')]
    public function index(LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();

        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/', name: 'app_home')]
    public function HomeRepos(ThemeRepository $themeRepository): Response
    {
        $themes = $themeRepository->findAll();
        
        return $this->render('home/index.html.twig', [
            'themes' => $themes,
        ]);
    }
    

    #[Route('/lesson/{id}/show', name: 'app_lesson_show')]
    public function showLesson(LessonRepository $lessonRepository, Cursus $cursus): Response
    {
        $lessons = $lessonRepository->findBy(['cursus' => $cursus]);

        return $this->render('lesson/show.html.twig', [
            'lessons' => $lessons,
            'cursus' => $cursus,
        ]);
    }

    #[Route('/admin/lesson/new', name: 'app_lesson_new')]
    public function newLesson(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Create a new Lesson entity instance
        $lesson = new Lesson();

        // Create a form for the Lesson entity
        $form = $this->createForm(LessonType::class, $lesson);

        // Handle the incoming request and populate the form
        $form->handleRequest($request);
        
        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('fiche')->getData();
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            // Process the PDF file if provided
            if ($pdfFile) {
                // Extract and sanitize the original filename
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    // Move the PDF file to the designated directory
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file exception (optional logging or user notification)
                }

                // Set the new filename for the PDF file in the Lesson entity
                $lesson->setFiche($newFilename);
            }

            // Process the video file if provided
            if ($videoFile) {
                // Extract and sanitize the original filename
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    // Move the video file to the designated directory
                    $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle file exception (optional logging or user notification)
                }

                // Set the new filename for the video file in the Lesson entity
                $lesson->setVideo($newFilename);
            }

            // Save the Lesson entity to the database
            $entityManager->persist($lesson);
            $entityManager->flush();

            // Redirect to the route that lists or displays lessons
            return $this->redirectToRoute('app_lesson');
        }

        // Render the form for creating a new Lesson
        return $this->render('lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/admin/lesson/{id}/edit', name: 'app_lesson_edit')]
    public function editLesson(Request $request, Lesson $lesson, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        // Create a form for editing a Lesson entity using LessonType form class
        $form = $this->createForm(LessonType::class, $lesson);

        // Handle the incoming request and populate the form with the request data
        $form->handleRequest($request);

        // Check if the form has been submitted and is valid
        if ($form->isSubmitted() && $form->isValid()) {
            
            // Retrieve the uploaded PDF file from the form
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('fiche')->getData();

            // Retrieve the uploaded video file from the form
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            // Process the PDF file if it exists
            if ($pdfFile) {
                // Extract the original filename without extension
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Generate a URL-safe version of the filename
                $safeFilename = $slugger->slug($originalFilename);
                // Create a new unique filename with the appropriate extension
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    // Move the uploaded PDF file to the designated directory
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle any exceptions during file upload (e.g., log error)
                }

                // Update the Lesson entity with the new filename for the PDF
                $lesson->setFiche($newFilename);
            }

            // Process the video file if it exists
            if ($videoFile) {
                // Extract the original filename without extension
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                // Generate a URL-safe version of the filename
                $safeFilename = $slugger->slug($originalFilename);
                // Create a new unique filename with the appropriate extension
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    // Move the uploaded video file to the designated directory
                    $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle any exceptions during file upload (e.g., log error)
                }

                // Update the Lesson entity with the new filename for the video
                $lesson->setVideo($newFilename);
            }

            // Persist the changes to the database
            $entityManager->flush();

            // Redirect the user to the 'app_lesson' route after successful update
            return $this->redirectToRoute('app_lesson');
        }

        // Render the form view if the form is not submitted or is invalid
        return $this->render('lesson/edit.html.twig', [
            'form' => $form->createView(),
        ]);

    }

    #[Route('/admin/lesson/{id}/delete', name: 'app_lesson_delete')]
    public function deleteLesson(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson');
    }

    #[Route('/lesson/{id}/toggle-completion', name: 'lesson_toggle_completion')]
    public function toggleCompletion(Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        // Toggle the completion status of the lesson
        $lesson->setCompleted(!$lesson->isCompleted());

        // Save the changes to the database
        $entityManager->flush();

        // Redirect the user to the home page
        return $this->redirectToRoute('app_home');

    }


}
