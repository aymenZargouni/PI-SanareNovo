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
            ->orderBy('b.title', $ascending ? 'ASC' : 'DESC'); 

        return $qb->getQuery()->getResult();
    }


    public function searchByTitleAjax(string $query): array
    {
        return $this->createQueryBuilder('b')
            ->where('LOWER(b.title) LIKE LOWER(:query)')
            ->setParameter('query', '%' . strtolower($query) . '%')
            ->getQuery()
            ->getResult();
    }
    

    /*public function add(Blog $blog, bool $flush = true): void
    {
        if (!$blog->getCreatedAt()) {
            $blog->setCreatedAt(new \DateTime());
        }

        $this->getEntityManager()->persist($blog);
        if ($flush) {
            $this->getEntityManager()->flush();
        }
    }*/

    // src/Repository/BlogRepository.php

    public function findAllSorted(): array
{
    return $this->createQueryBuilder('b')
        ->orderBy('b.createdAt', 'DESC') // Ou tout autre champ valide
        ->getQuery()
        ->getResult();
}

    
    
    

    // Récupérer les blogs avec le plus de catégories
    public function findBlogsWithMostCategories($limit = 5)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.Category', 'c') // Assure-toi que la relation existe
            ->groupBy('b.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

    // Récupérer les blogs avec le plus de commentaires
    public function findBlogsWithMostComments($limit = 5)
    {
        return $this->createQueryBuilder('b')
            ->leftJoin('b.comments', 'c') // Assure-toi que la relation existe
            ->groupBy('b.id')
            ->orderBy('COUNT(c.id)', 'DESC')
            ->setMaxResults($limit)
            ->getQuery()
            ->getResult();
    }

        

    }