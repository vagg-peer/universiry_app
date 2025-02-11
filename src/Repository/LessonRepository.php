<?php

namespace App\Repository;

use App\Entity\Lesson;
use Doctrine\Bundle\DoctrineBundle\Repository\ServiceEntityRepository;
use Doctrine\Persistence\ManagerRegistry;

/**
 * @extends ServiceEntityRepository<Lesson>
 */
class LessonRepository extends ServiceEntityRepository
{
    public function __construct(ManagerRegistry $registry)
    {
        parent::__construct($registry, Lesson::class);
    }
    
    public function findAllOrderedBySemester(): array
    {
        return $this->createQueryBuilder('l')
            ->orderBy('l.semester', 'ASC') // Order by semester (ascending)
            ->getQuery()
            ->getResult();
    }

    public function findAllByStudentSemester($semester): array
    {
        return $this->createQueryBuilder('l')
            ->andWhere('l.semester <= :val')
            ->setParameter('val', $semester)
            ->orderBy('l.semester', 'ASC') // Order by semester (ascending)
            ->getQuery()
            ->getResult();
    }
}
