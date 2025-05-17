<?php

namespace App\Repository;

use App\Entity\Ending;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Ending>
 *
 * @method Ending|null find($id, $lockMode = null, $lockVersion = null)
 * @method Ending|null findOneBy(array $criteria, array $orderBy = null)
 * @method Ending[]    findAll()
 * @method Ending[]    findBy(array $criteria, array $orderBy = null, $limit = null, $offset = null)
 */
class EndingRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Ending::class);
    }

    /**
     * Find all approved endings ordered by creation date (newest first)
     *
     * @return Ending[]
     */
    public function findAllOrderedByNewest(): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.status = :status')
            ->setParameter('status', 'approved')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find approved endings by a specific emotion
     *
     * @param string $emotion
     * @return Ending[]
     */
    public function findByEmotion(string $emotion): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.emotion = :emotion')
            ->andWhere('e.status = :status')
            ->setParameter('emotion', $emotion)
            ->setParameter('status', 'approved')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }

    /**
     * Find approved endings for a specific user
     *
     * @param int $userId
     * @return Ending[]
     */
    public function findByUser(int $userId): array
    {
        return $this->createQueryBuilder('e')
            ->andWhere('e.user = :userId')
            ->andWhere('e.status = :status')
            ->setParameter('userId', $userId)
            ->setParameter('status', 'approved')
            ->orderBy('e.createdAt', 'DESC')
            ->getQuery()
            ->getResult();
    }
}
