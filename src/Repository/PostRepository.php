<?php

namespace App\Repository;

use App\Entity\Post;
use App\Entity\User;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @method Post|null find($id, $lockMode = null, $lockVersion = null)
 * @method Post|null findOneBy(array $criteria, array $orderBy = null)
 * @method Post[]    findAll()
 * @method Post[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class PostRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Post::class);
    }

    /**
     * @param string $sort
     * @param string $order
     * @return Post[] Returns an array of Post objects
     */

    public function findAllBy($sort='id',$order='ASC'): array
    {
        $valid_orders=['ASC','DESC'];
        if(!in_array($order,$valid_orders)) $order='ASC';

        $valid_sorts=['id','post_text','createdOn'];
        if(!in_array($sort,$valid_sorts)) $sort='id';
        $sort='p.'.$sort;

        return $this->createQueryBuilder('p')
            //->andWhere('p.exampleField = :val')
            //->setParameter('val', $value)
            ->orderBy($sort,$order)
            //->orderBy('p.createdOn', 'DESC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
        ;
    }


    /*
    public function findOneBySomeField($value): ?Post
    {
        return $this->createQueryBuilder('p')
            ->andWhere('p.exampleField = :val')
            ->setParameter('val', $value)
            ->getQuery()
            ->getOneOrNullResult()
        ;
    }
    */
    public function findUserPostsBy(User $user, string $sort, string $order)
    {
        $valid_orders=['ASC','DESC'];
        if(!in_array($order,$valid_orders)) $order='ASC';

        $valid_sorts=['id','post_text','createdOn'];
        if(!in_array($sort,$valid_sorts)) $sort='id';
        $sort='p.'.$sort;

        return $this->createQueryBuilder('p')
             ->andWhere('p.user = :val')
             ->setParameter('val', $user)
            ->orderBy($sort,$order)
            //->orderBy('p.createdOn', 'DESC')
            //->setMaxResults(10)
            ->getQuery()
            ->getResult()
            ;
    }
}
