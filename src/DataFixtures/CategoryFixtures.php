<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Category;
class CategoryFixtures extends Fixture
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        for ($i = 0; $i < 5; $i++) {
            $category = new Category();
            $category->setName($faker->word());

            $manager->persist($category);

            // Ajouter une référence pour l'utiliser dans BlogFixtures
            $this->addReference('category_' . $i, $category);
        }

        $manager->flush();
    }
}