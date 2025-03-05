<?php

namespace App\DataFixtures;

use Doctrine\Bundle\FixturesBundle\Fixture;
use Doctrine\Persistence\ObjectManager;
use Faker\Factory;
use App\Entity\Comment;
use App\Entity\Blog;
use App\Entity\User;
use Doctrine\Common\DataFixtures\DependentFixtureInterface;

class CommentFixtures extends Fixture implements DependentFixtureInterface
{
    public function load(ObjectManager $manager): void
    {
        $faker = Factory::create();

        // 🔹 Récupérer un utilisateur existant
        $user = $manager->getRepository(User::class)->find(3);
        if (!$user) {
            throw new \Exception("L'utilisateur avec l'ID 1 n'existe pas en base de données.");
        }

        for ($i = 0; $i < 20; $i++) {
            $comment = new Comment();
            $comment->setContent($faker->sentence(10));
            $comment->setCreatedAt(new \DateTimeImmutable());
            $comment->setUpdatedAt(new \DateTime());

            // 🔹 Correction de getReference()
            $randomBlog = $this->getReference('blog_' . rand(0, 9), Blog::class);
            $comment->setBlog($randomBlog);

            // 🔹 Associer un utilisateur statique
            $comment->setUser($user);

            $manager->persist($comment);
        }

        $manager->flush();
    }

    public function getDependencies(): array
    {
        return [
            BlogFixtures::class,
        ];
    }
}