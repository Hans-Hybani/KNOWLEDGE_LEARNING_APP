<?php

namespace App\DataFixtures;

use App\Entity\Certification;
use App\Entity\Cursus;
use App\Entity\Lesson;
use App\Entity\Theme;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class AppFixtures extends Fixture
{
    private UserPasswordHasherInterface $passwordHasher;

    public function __construct(UserPasswordHasherInterface $passwordHasher)
    {
        $this->passwordHasher = $passwordHasher;
    }

    public function load(ObjectManager $manager): void
    {
        // Création et persistance de l'utilisateur
        $user = new User();
        $user->setEmail('hh@gmail.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
        $user->setPassword($hashedPassword);

        $user->setActivationToken(null);
        $user->setIsActive(true);

        $manager->persist($user);

        // Liste des thèmes à ajouter
        $themeNames = [
            'Musique',
            'Informatique',
            'Jardinage',
            'Cuisine',
        ];

        // Création des thèmes et persistance
        $themes = [];
        foreach ($themeNames as $themeName) {
            $theme = new Theme();
            $theme->setName($themeName);

            $manager->persist($theme);
            $themes[$themeName] = $theme;
        }

        $manager->flush();

        // Liste des cursus à ajouter
        $cursusData = [
            ['title' => 'Cursus d’initiation à la guitare', 'price' => 50, 'theme' => $themes['Musique']],
            ['title' => 'Cursus d’initiation au piano', 'price' => 50, 'theme' => $themes['Musique']],
            ['title' => 'Cursus d’initiation au développement web', 'price' => 60, 'theme' => $themes['Informatique']],
            ['title' => 'Cursus d’initiation au jardinage', 'price' => 30, 'theme' => $themes['Jardinage']],
            ['title' => 'Cursus d’initiation à la cuisine', 'price' => 44, 'theme' => $themes['Cuisine']],
            ['title' => 'Cursus d’initiation à l’art du dressage culinaire', 'price' => 48, 'theme' => $themes['Cuisine']],
        ];

        $cursusEntities = [];
        foreach ($cursusData as $data) {
            $cursus = new Cursus();
            $cursus->setTitle($data['title']);
            $cursus->setPrice($data['price']);
            $cursus->setTheme($data['theme']);

            $manager->persist($cursus);
            $cursusEntities[$data['title']] = $cursus;
        }

        $manager->flush();

        // Liste des leçons à ajouter
        $lessonsData = [
            'Cursus d’initiation à la guitare' => [
                ['title' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument.mp4'],
                ['title' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes.mp4'],
            ],
            'Cursus d’initiation au piano' => [
                ['title' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument_piano.mp4'],
                ['title' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes_piano.mp4'],
            ],
            'Cursus d’initiation au développement web' => [
                ['title' => 'Leçon n°1 : Les langages Html et CSS', 'price' => 32, 'video' => 'html_css.mp4'],
                ['title' => 'Leçon n°2 : Dynamiser votre site avec Javascript', 'price' => 32, 'video' => 'javascript.mp4'],
            ],
            'Cursus d’initiation au jardinage' => [
                ['title' => 'Leçon n°1 : Les outils du jardinier', 'price' => 16, 'video' => 'outils_jardinier.mp4'],
                ['title' => 'Leçon n°2 : Jardiner avec la lune', 'price' => 16, 'video' => 'jardiner_lune.mp4'],
            ],
            'Cursus d’initiation à la cuisine' => [
                ['title' => 'Leçon n°1 : Les modes de cuisson', 'price' => 23, 'video' => 'modes_cuisson.mp4'],
                ['title' => 'Leçon n°2 : Les saveurs', 'price' => 23, 'video' => 'saveurs.mp4'],
            ],
            'Cursus d’initiation à l’art du dressage culinaire' => [
                ['title' => 'Leçon n°1 : Mettre en œuvre le style dans l’assiette', 'price' => 26, 'video' => 'style_assiette.mp4'],
                ['title' => 'Leçon n°2 : Harmoniser un repas à quatre plats', 'price' => 26, 'video' => 'harmoniser_repas.mp4'],
            ],
        ];

        foreach ($lessonsData as $cursusTitle => $lessons) {
            if (isset($cursusEntities[$cursusTitle])) {
                $cursus = $cursusEntities[$cursusTitle];
                foreach ($lessons as $lessonData) {
                    $lesson = new Lesson();
                    $lesson->setTitle($lessonData['title']);
                    $lesson->setPrice($lessonData['price']);
                    $lesson->setCompleted(false);

                    // Définir les chemins des fichiers
                    $lesson->setFiche(''); // Si fiche n'est pas utilisé, laisser vide ou enlever cette ligne
                    $lesson->setVideo('/assets/Vidéo/' . $lessonData['video']); // Chemin relatif

                    $lesson->setCursus($cursus);

                    $manager->persist($lesson);
                }
            }
        }

        $manager->flush();

        // Liste des certificats à ajouter
        $certificationsData = [
            ['user' => $user, 'theme' => $themes['Musique'], 'certificationDate' => new \DateTime('2024-01-01'), 'cursus' => $cursusEntities['Cursus d’initiation à la guitare'], 'certificationDoc' => 'certificat_guitare.pdf', 'lesson' => $cursusEntities['Cursus d’initiation à la guitare']->getLessons()->first()],
            ['user' => $user, 'theme' => $themes['Musique'], 'certificationDate' => new \DateTime('2024-01-02'), 'cursus' => $cursusEntities['Cursus d’initiation au piano'], 'certificationDoc' => 'certificat_piano.pdf', 'lesson' => $cursusEntities['Cursus d’initiation au piano']->getLessons()->first()],
            // Ajoute d'autres certificats ici selon les besoins
        ];

        foreach ($certificationsData as $data) {
            $certification = new Certification();
            $certification->setUserCertification($data['user']);
            $certification->setThemeCertification($data['theme']);
            $certification->setCertificationDate($data['certificationDate']);
            $certification->setCursus($data['cursus']);

            // Assure-toi que la leçon existe avant de la définir
            if ($data['lesson'] instanceof Lesson) {
                $certification->setLesson($data['lesson']);
            } else {
                // Gérer le cas où la leçon n'existe pas ou ne peut pas être trouvée
                // Par exemple, tu peux soit ignorer cette entrée ou la marquer comme incomplète
            }

            $certification->setCertificationDoc($data['certificationDoc']);

            $manager->persist($certification);
        }

        $manager->flush();
    }
}
