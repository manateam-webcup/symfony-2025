<?php

namespace App\Repository;

use App\Entity\UserEndingInteraction;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<UserEndingInteraction>
 *
 * @method UserEndingInteraction|null find($id, $lockMode = null, $lockVersion = null)
 * @method UserEndingInteraction|null findOneBy(array $criteria, array $orderBy = null)
 * @method UserEndingInteraction[]    findAll()
 * @method UserEndingInteraction[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class UserEndingInteractionRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, UserEndingInteraction::class);
    }

    /**
     * Find a user's interaction with a specific ending
     *
     * @param int $userId
     * @param int $endingId
     * @return UserEndingInteraction|null
     */
    public function findUserInteraction(int $userId, int $endingId): ?UserEndingInteraction
    {
        return $this->createQueryBuilder('ui')
            ->andWhere('ui.user = :userId')
            ->andWhere('ui.ending = :endingId')
            ->setParameter('userId', $userId)
            ->setParameter('endingId', $endingId)
            ->getQuery()
            ->getOneOrNullResult();
    }
}