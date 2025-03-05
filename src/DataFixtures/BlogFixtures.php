<?php

namespace App\DataFixtures;

use App\Entity\Blog;
use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
class BlogFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create(); // Initialise Faker

        for ($i = 0; $i < 10; $i++) { // Générer 10 blogs fake
            $blog = new Blog();
            $blog->setTitle($faker->sentence(6)); // Titre de 6 mots
            $blog->setContent($faker->paragraph(10)); // Contenu avec 10 phrases
            $blog->setImage($faker->imageUrl(640, 480, 'nature', true)); // Image aléatoire
            $blog->setCreatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-1 years', 'now')));
            $blog->setUpdatedAt(\DateTimeImmutable::createFromMutable($faker->dateTimeBetween('-6 months', 'now')));

            $manager->persist($blog);

            // Ajouter une référence pour les commentaires
            $this->addReference('blog_' . $i, $blog);
        }

        $manager->flush(); // Sauvegarde en base de données
    }

}