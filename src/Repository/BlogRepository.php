<?php

namespace App\Repository;

use App\Entity\Blog;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Blog>
 */
class BlogRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Blog::class);
    }

//    /**
//     * @return Blog[] Returns an array of Blog objects
//     */
//    public function findByExampleField($value): array
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->orderBy('b.id', 'ASC')
//            ->setMaxResults(10)
//            ->getQuery()
//            ->getResult()
//        ;
//    }

//    public function findOneBySomeField($value): ?Blog
//    {
//        return $this->createQueryBuilder('b')
//            ->andWhere('b.exampleField = :val')
//            ->setParameter('val', $value)
//            ->getQuery()
//            ->getOneOrNullResult()
//        ;
//    }

    public function findByTitleSorted(bool $ascending = true): array
    {
        $qb = $this->createQueryBuilder('b')
            ->orderBy('b.title', $ascending ? 'ASC' : 'DESC'); // Utilise ASC ou DESC selon l'argument

        return $qb->getQuery()->getResult();
    }

    public function searchBlogs($title = '', $content = '', $categoryId = null, $startDate = null)
    {
        $queryBuilder = $this->createQueryBuilder('b');

        // Filtre sur le titre
        if ($title) {
            $queryBuilder->andWhere('b.title LIKE :title')
                         ->setParameter('title', '%' . $title . '%');
        }

        // Filtre sur le contenu
        if ($content) {
            $queryBuilder->andWhere('b.content LIKE :content')
                         ->setParameter('content', '%' . $content . '%');
        }

        // Filtre sur la date de début
        if ($startDate) {
            $queryBuilder->andWhere('b.createdAt >= :startDate')
                         ->setParameter('startDate', $startDate);
        }

        return $queryBuilder->getQuery()->getResult();
    }



    public function add(Blog $blog, bool $flush = true): void
    {
        if (!$blog->getCreatedAt()) {
            $blog->setCreatedAt(new \DateTime());
        }

        $this->getEntityManager()->persist($blog);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }



        

    }