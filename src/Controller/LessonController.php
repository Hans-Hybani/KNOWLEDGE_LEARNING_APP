<?php

namespace App\Controller;

use App\Entity\Lesson;
use App\Form\LessonType;
use App\Repository\LessonRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\File\Exception\FileException;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\String\Slugger\SluggerInterface;

class LessonController extends AbstractController
{
    #[Route('/lesson', name: 'app_lesson')]
    public function index(LessonRepository $lessonRepository): Response
    {
        $lessons = $lessonRepository->findAll();

        return $this->render('lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/lesson/new', name: 'app_lesson_new')]
    public function newLesson(Request $request, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $lesson = new Lesson();
        $form = $this->createForm(LessonType::class, $lesson);

        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->persist($lesson);
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_lesson');
        // }

        // return $this->render('lesson/new.html.twig', [
        //     'form' => $form->createView(),
        // ]);

        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('fiche')->getData();
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if needed
                }

                $lesson->setFiche($newFilename);
            }

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if needed
                }

                $lesson->setVideo($newFilename);
            }

            $entityManager->persist($lesson);
            $entityManager->flush();

            return $this->redirectToRoute('app_lesson');
        }

        return $this->render('lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/lesson/{id}/edit', name: 'app_lesson_edit')]
    public function editLesson(Request $request, Lesson $lesson, EntityManagerInterface $entityManager, SluggerInterface $slugger): Response
    {
        $form = $this->createForm(LessonType::class, $lesson);

        // $form->handleRequest($request);
        // if ($form->isSubmitted() && $form->isValid()) {
        //     $entityManager->flush();

        //     return $this->redirectToRoute('app_lesson');
        // }
        $form->handleRequest($request);
        if ($form->isSubmitted() && $form->isValid()) {
            /** @var UploadedFile $pdfFile */
            $pdfFile = $form->get('fiche')->getData();
            /** @var UploadedFile $videoFile */
            $videoFile = $form->get('video')->getData();

            if ($pdfFile) {
                $originalFilename = pathinfo($pdfFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$pdfFile->guessExtension();

                try {
                    $pdfFile->move(
                        $this->getParameter('pdf_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if needed
                }

                $lesson->setFiche($newFilename);
            }

            if ($videoFile) {
                $originalFilename = pathinfo($videoFile->getClientOriginalName(), PATHINFO_FILENAME);
                $safeFilename = $slugger->slug($originalFilename);
                $newFilename = $safeFilename.'-'.uniqid().'.'.$videoFile->guessExtension();

                try {
                    $videoFile->move(
                        $this->getParameter('video_directory'),
                        $newFilename
                    );
                } catch (FileException $e) {
                    // Handle exception if needed
                }

                $lesson->setVideo($newFilename);
            }

            $entityManager->flush();

            return $this->redirectToRoute('app_lesson');
        }

        return $this->render('lesson/edit.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/lesson/{id}/delete', name: 'app_lesson_delete')]
    public function deleteLesson(Request $request, Lesson $lesson, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$lesson->getId(), $request->request->get('_token'))) {
            $entityManager->remove($lesson);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_lesson');
    }

}
