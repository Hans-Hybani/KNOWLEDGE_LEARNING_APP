<?php

// namespace App\DataFixtures;

// use App\Entity\Cursus;
// use App\Entity\Lesson;
// use App\Entity\Theme;
// use App\Entity\User;
// use Doctrine\Bundle\FixturesBundle\Fixture;
// use Doctrine\Persistence\ObjectManager;
// use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

// class AppFixtures extends Fixture
// {
//     private UserPasswordHasherInterface $passwordHasher;

//     public function __construct(UserPasswordHasherInterface $passwordHasher)
//     {
//         $this->passwordHasher = $passwordHasher;
//     }

//     public function load(ObjectManager $manager): void
//     {
//         // Création et persistance de l'utilisateur
//         $user = new User();
//         $user->setEmail('hh@gmail.com');
//         $user->setRoles(['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER']);

//         $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
//         $user->setPassword($hashedPassword);

//         $user->setActivationToken(null);
//         $user->setIsActive(true);

//         $manager->persist($user);

//         // Liste des thèmes à ajouter
//         $themeNames = [
//             'Musique',
//             'Informatique',
//             'Jardinage',
//             'Cuisine',
//         ];

//         // Création des thèmes et persistance
//         $themes = [];
//         foreach ($themeNames as $themeName) {
//             $theme = new Theme();
//             $theme->setName($themeName);

//             $manager->persist($theme);
//             $themes[$themeName] = $theme;
//         }

//         $manager->flush();

//         // Liste des cursus à ajouter
//         $cursusData = [
//             ['title' => 'Cursus d’initiation à la guitare', 'price' => 50, 'theme' => $themes['Musique']],
//             ['title' => 'Cursus d’initiation au piano', 'price' => 50, 'theme' => $themes['Musique']],
//             ['title' => 'Cursus d’initiation au développement web', 'price' => 60, 'theme' => $themes['Informatique']],
//             ['title' => 'Cursus d’initiation au jardinage', 'price' => 30, 'theme' => $themes['Jardinage']],
//             ['title' => 'Cursus d’initiation à la cuisine', 'price' => 44, 'theme' => $themes['Cuisine']],
//             ['title' => 'Cursus d’initiation à l’art du dressage culinaire', 'price' => 48, 'theme' => $themes['Cuisine']],
//         ];

//         $cursusEntities = [];
//         foreach ($cursusData as $data) {
//             $cursus = new Cursus();
//             $cursus->setTitle($data['title']);
//             $cursus->setPrice($data['price']);
//             $cursus->setTheme($data['theme']);

//             $manager->persist($cursus);
//             $cursusEntities[$data['title']] = $cursus;
//         }

//         $manager->flush();

//         // Liste des leçons à ajouter
//         $lessonsData = [
//             'Cursus d’initiation à la guitare' => [
//                 ['title' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument.mp4'],
//                 ['title' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes.mp4'],
//             ],
//             'Cursus d’initiation au piano' => [
//                 ['title' => 'Leçon n°1 : Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument_piano.mp4'],
//                 ['title' => 'Leçon n°2 : Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes_piano.mp4'],
//             ],
//             'Cursus d’initiation au développement web' => [
//                 ['title' => 'Leçon n°1 : Les langages Html et CSS', 'price' => 32, 'video' => 'html_css.mp4'],
//                 ['title' => 'Leçon n°2 : Dynamiser votre site avec Javascript', 'price' => 32, 'video' => 'javascript.mp4'],
//             ],
//             'Cursus d’initiation au jardinage' => [
//                 ['title' => 'Leçon n°1 : Les outils du jardinier', 'price' => 16, 'video' => 'outils_jardinier.mp4'],
//                 ['title' => 'Leçon n°2 : Jardiner avec la lune', 'price' => 16, 'video' => 'jardiner_lune.mp4'],
//             ],
//             'Cursus d’initiation à la cuisine' => [
//                 ['title' => 'Leçon n°1 : Les modes de cuisson', 'price' => 23, 'video' => 'modes_cuisson.mp4'],
//                 ['title' => 'Leçon n°2 : Les saveurs', 'price' => 23, 'video' => 'saveurs.mp4'],
//             ],
//             'Cursus d’initiation à l’art du dressage culinaire' => [
//                 ['title' => 'Leçon n°1 : Mettre en œuvre le style dans l’assiette', 'price' => 26, 'video' => 'style_assiette.mp4'],
//                 ['title' => 'Leçon n°2 : Harmoniser un repas à quatre plats', 'price' => 26, 'video' => 'harmoniser_repas.mp4'],
//             ],
//         ];

//         foreach ($lessonsData as $cursusTitle => $lessons) {
//             if (isset($cursusEntities[$cursusTitle])) {
//                 $cursus = $cursusEntities[$cursusTitle];
//                 foreach ($lessons as $lessonData) {
//                     $lesson = new Lesson();
//                     $lesson->setTitle($lessonData['title']);
//                     $lesson->setPrice($lessonData['price']);
//                     $lesson->setCompleted(false);

//                     // Définir les chemins des fichiers
//                     $lesson->setFiche(''); // Si fiche n'est pas utilisé, laisser vide ou enlever cette ligne
//                     $lesson->setVideo('/assets/Vidéo/' . $lessonData['video']); // Chemin relatif

//                     $lesson->setCursus($cursus);

//                     $manager->persist($lesson);
//                 }
//             }
//         }

//         $manager->flush();
//     }
// }
namespace App\DataFixtures;

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
        // Create and persist a user
        $user = new User();
        $user->setEmail('hh@example.com');
        $user->setRoles(['ROLE_ADMIN', 'ROLE_EDITOR', 'ROLE_USER']);

        $hashedPassword = $this->passwordHasher->hashPassword($user, 'azerty');
        $user->setPassword($hashedPassword);
        $user->setNom('Admin User');
        $user->setDateNaissance(new \DateTime('1990-01-01'));
        $user->setMetier('Administrator');
        $user->setAdresse('123 Admin Street');
        $user->setPhoto(null); // Set to null or a valid URL/path
        $user->setActivationToken(null);
        $user->setIsActive(true);

        $manager->persist($user);

        // Themes to add
        $themeNames = [
            'Musique',
            'Informatique',
            'Jardinage',
            'Cuisine',
        ];

        $themes = [];
        foreach ($themeNames as $themeName) {
            $theme = new Theme();
            $theme->setName($themeName);

            $manager->persist($theme);
            $themes[$themeName] = $theme;
        }

        $manager->flush();

        // Cursus to add
        $cursusData = [
            ['title' => 'Initiation à la guitare', 'price' => 50, 'theme' => $themes['Musique']],
            ['title' => 'Initiation au piano', 'price' => 50, 'theme' => $themes['Musique']],
            ['title' => 'Développement web', 'price' => 60, 'theme' => $themes['Informatique']],
            ['title' => 'Initiation au jardinage', 'price' => 30, 'theme' => $themes['Jardinage']],
            ['title' => 'Initiation à la cuisine', 'price' => 44, 'theme' => $themes['Cuisine']],
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

        // Lessons to add
        $lessonsData = [
            'Initiation à la guitare' => [
                ['title' => 'Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument.mp4'],
                ['title' => 'Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes.mp4'],
            ],
            'Initiation au piano' => [
                ['title' => 'Découverte de l’instrument', 'price' => 26, 'video' => 'decouverte_instrument_piano.mp4'],
                ['title' => 'Les accords et les gammes', 'price' => 26, 'video' => 'accords_gammes_piano.mp4'],
            ],
            'Développement web' => [
                ['title' => 'HTML et CSS', 'price' => 32, 'video' => 'html_css.mp4'],
                ['title' => 'Javascript', 'price' => 32, 'video' => 'javascript.mp4'],
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
                    $lesson->setFiche(''); // Assuming fiche is nullable
                    $lesson->setVideo('/assets/videos/' . $lessonData['video']);
                    $lesson->setCursus($cursus);

                    $manager->persist($lesson);
                }
            }
        }

        $manager->flush();
    }
}
