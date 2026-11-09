<?php

namespace App\DataFixtures;

use App\Entity\Artiste;
use App\Entity\Benevole;
use App\Entity\Lieu;
use App\Entity\Oeuvre;
use App\Entity\Technique;
use App\Entity\User;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

final class AppFixtures extends Fixture
{
    public const EMAIL = 'prof@example.com';
    public const PASSWORD = 'DemoSymfony2026!';

    public function __construct(private readonly UserPasswordHasherInterface $passwordHasher)
    {
    }

    public function load(ObjectManager $manager): void
    {
        $user = (new User())->setEmail(self::EMAIL)->setRoles(['ROLE_BENEVOLE']);
        $user->setPassword($this->passwordHasher->hashPassword($user, self::PASSWORD));
        $manager->persist($user);

        foreach ([['Professeur', 'Demo', self::EMAIL], ['Martin', 'Alice', 'alice@example.com']] as [$nom, $prenom, $email]) {
            $manager->persist((new Benevole())->setNom($nom)->setPrenom($prenom)->setEmail($email));
        }

        $techniques = [];
        foreach (['Pochoir', 'Bombe aérosol', 'Collage'] as $nom) {
            $technique = (new Technique())->setNom($nom);
            $manager->persist($technique);
            $techniques[] = $technique;
        }

        $artistes = [];
        foreach ([['Nova', 'Camille Martin'], ['Echo', 'Alex Bernard'], ['Lune', 'Lou Petit']] as [$pseudo, $nom]) {
            $artiste = (new Artiste())->setPseudonyme($pseudo)->setNomReel($nom)
                ->setNationalite('Française')->setBio('Artiste fictif créé pour la démonstration.');
            $manager->persist($artiste);
            $artistes[] = $artiste;
        }

        $lieux = [];
        foreach ([
            ['Mur des couleurs', '10 rue Exemple', 'Centre', '48.8566000', '2.3522000'],
            ['Passage des artistes', '20 avenue Démo', 'Nord', '48.8666000', '2.3622000'],
            ['Place des fresques', '30 boulevard des Arts', 'Sud', '48.8466000', '2.3422000'],
        ] as [$nom, $adresse, $quartier, $latitude, $longitude]) {
            $lieu = (new Lieu())->setNom($nom)->setAdresse($adresse)->setQuartier($quartier)
                ->setLatitude($latitude)->setLongitude($longitude);
            $manager->persist($lieu);
            $lieux[] = $lieu;
        }

        $oeuvres = [];
        foreach (['Éclosion', 'Regards croisés', 'Ville en couleurs', 'Envol', 'Reflets', 'Nouveau départ'] as $index => $titre) {
            $oeuvre = (new Oeuvre())->setTitre($titre)
                ->setDescription('Œuvre fictive du jeu de démonstration.')
                ->setDateCreation(new \DateTime(sprintf('2025-%02d-01', $index + 1)))
                // Le modèle actuel impose une date de destruction pour chaque œuvre.
                ->setDateDestruction(new \DateTime(sprintf('2025-%02d-28', $index + 1)));
            $lieux[$index % 3]->addOeuvre($oeuvre);
            $oeuvre->addArtiste($artistes[$index % 3]);
            $techniques[$index % 3]->addOeuvre($oeuvre);
            $manager->persist($oeuvre);
            $oeuvres[] = $oeuvre;
        }
        $oeuvres[1]->addArtiste($artistes[0]);
        $techniques[0]->addOeuvre($oeuvres[1]);
        $oeuvres[0]->setRecouvertePar($oeuvres[3]);

        $manager->flush();
    }
}
