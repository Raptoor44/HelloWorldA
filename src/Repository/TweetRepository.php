<?php

namespace App\Repository;

use App\Entity\Tweet;
use App\Entity\UserAccount;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Tweet>
 *
 * @method Tweet|null find($id, $lockMode = null, $lockVersion = null)
 * @method Tweet|null findOneBy(array $criteria, array $orderBy = null)
 * @method Tweet[]    findAll()
 * @method Tweet[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class TweetRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Tweet::class);
    }

    public function findOneByIdWithResponses($idTweetParam): ?Tweet
    {
        return $this->createQueryBuilder('t')
            ->leftJoin('t.responses', 'r')
            ->addSelect('r')
            ->andWhere('t.id = :idTweetParam')
            ->setParameter('idTweetParam', $idTweetParam)
            ->getQuery()
            ->getOneOrNullResult();
    }
}
