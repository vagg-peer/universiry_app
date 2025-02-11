<?php

namespace App\Repository;

use App\Entity\Student;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Student>
 */
class StudentRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Student::class);
    }

    public function findStudentByUserId($userId): ?Student
    {
        return $this->createQueryBuilder('s')
            ->andWhere('s.user = :val')
            ->setParameter('val', $userId)
            ->getQuery()
            ->getOneOrNullResult(); 
    }
}
