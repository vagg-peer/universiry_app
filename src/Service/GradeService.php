<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\DTO\GradeDTO;
use App\Entity\Grade;
use App\Mapper\GradeMapper;
use App\Repository\GradeRepository;
use App\Mapper\LessonMapper;
use App\Mapper\StudentMapper;


class GradeService
{
    private EntityManagerInterface $em;
    private GradeRepository $gradeRepository;
    private GradeMapper $gradeMapper;

    public function __construct(EntityManagerInterface $em, GradeRepository $gradeRepository, GradeMapper $gradeMapper)
    {
        $this->em = $em;
        $this->gradeRepository = $gradeRepository;
        $this->gradeMapper = $gradeMapper;
    }

    public function toDTO(Grade $grade) : GradeDTO
    {
        return $this->gradeMapper->toDTO($grade);
    }

    public function toEntity(GradeDTO $gradeDTO) : Grade
    {
        return $this->gradeMapper->toEntity($gradeDTO);
    }

    public function getAll() : array
    {
        $grades = $this->gradeRepository->findAll();
        return array_map([$this, 'toDTO'], $grades);
    }

    public function getGradeById(int $id): ?GradeDTO
    {
        $grade = $this->gradeRepository->find($id);
        if (!$grade) {
            throw new \RuntimeException("Lesson with ID $id not found.");
        }
        return $this->toDTO($grade);
    }   
    
    public function save(GradeDTO $gradeDTO) : GradeDTO
    {
        try {
            $grade = $gradeDTO->getId() ? $this->gradeRepository->find($gradeDTO->getId()) : new Grade();
            $grade = $this->toEntity($gradeDTO);
            $this->em->persist($grade);
            $this->em->flush();
            return $this->toDTO($grade);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to save student: " . $e->getMessage());
        }
    }
}