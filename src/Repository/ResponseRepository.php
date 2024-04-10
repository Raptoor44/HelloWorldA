<?php

namespace App\Repository;

use App\Entity\Response;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Response>
 *
 * @method Response|null find($id, $lockMode = null, $lockVersion = null)
 * @method Response|null findOneBy(array $criteria, array $orderBy = null)
 * @method Response[]    findAll()
 * @method Response[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class ResponseRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Response::class);
    }

    public function findAllWithoutOtherAttributes(): array
    {
        return $this->createQueryBuilder('r')
            ->select('r.id, r.content, r.numberLikes, u.id AS user_id, t.id AS tweet_id')
            ->innerJoin('r.userAccount', 'u')
            ->innerJoin('r.tweet', 't')
            ->getQuery()
            ->getResult();
    }
}
