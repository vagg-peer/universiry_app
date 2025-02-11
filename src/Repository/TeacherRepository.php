<?php

namespace App\Repository;

use App\Entity\Teacher;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Teacher>
 */
class TeacherRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Teacher::class);
    }

    public function findTeacherByUserId($userId): ?Teacher
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getOneOrNullResult(); 
    }

    public function findActiveTeachers(): ?array
    {
        return $this->createQueryBuilder('t')
            ->andWhere('t.isActive = :val')
            ->setParameter('val', 1)
            ->getQuery()
            ->getResult(); 
    }
}
