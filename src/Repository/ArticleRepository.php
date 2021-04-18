<?php

namespace App\Repository;

use App\Entity\Article;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Article|null find($id, $lockMode = null, $lockVersion = null)
 * @method Article|null findOneBy(array $criteria, array $orderBy = null)
 * @method Article[]    findAll()
 * @method Article[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ArticleRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Article::class);
    }

    /**
     * Search article according to the words received
     * @return void 
     */
    public function searchVisible($words = null){
        // Create query builder of article visible
        $query = $this->createQueryBuilder('a');
        $query->where('a.isVisible = 1');
        if($words != null){
            $query->andWhere('MATCH_AGAINST(a.title, a.content) AGAINST (:words boolean)>0')
                ->setParameter('words', $words);
        }
        return $query->getQuery()->getResult();
    }

    /**
     * Search article according to the words received
     * @return void 
     */
    public function searchAllArticle($words = null){
        // Create query builder of article visible
        $query = $this->createQueryBuilder('a');
        if($words != null){
            $query->Where('MATCH_AGAINST(a.title, a.content) AGAINST (:words boolean)>0')
                ->setParameter('words', $words);
        }
        return $query->getQuery()->getResult();
    }

    // /**
    //  * @return Article[] Returns an array of Article objects
    //  */
    /*
    public function findByExampleField($value)
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->orderBy('a.id', 'ASC')
            ->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }
    */

    /*
    public function findOneBySomeField($value): ?Article
    {
        return $this->createQueryBuilder('a')
            ->andWhere('a.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
}
