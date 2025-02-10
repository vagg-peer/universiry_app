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
    private LessonMapper $lessonMapper;
    private StudentMapper $studentMapper;

    public function __construct(EntityManagerInterface $em, GradeRepository $gradeRepository, GradeMapper $gradeMapper, LessonMapper $lessonMapper, StudentMapper $studentMapper)
    {
        $this->em = $em;
        $this->gradeRepository = $gradeRepository;
        $this->gradeMapper = $gradeMapper;
        $this->lessonMapper = $lessonMapper;
        $this->studentMapper = $studentMapper;
    }

    public function toDTO(Grade $grade) : GradeDTO
    {
        $studentDTO = $this->studentMapper->toDTO($grade->getStudent(), $grade->getStudent()->getGrades()->toArray(), $grade->getStudent()->getLessons()->toArray());
        $lessonDTO = $this->lessonMapper->toDTO($grade->getLesson());
        return $this->gradeMapper->toDTO($grade, $studentDTO, $lessonDTO);
    }

    public function toEntity(GradeDTO $gradeDTO) : Grade
    {
        $student = $this->studentMapper->toEntity($gradeDTO->getStudent());
        $lesson = $this->lessonMapper->toEntity($gradeDTO->getLesson());
        return $this->gradeMapper->toEntity($gradeDTO, $student, $lesson);
    }

    public function getAll() : array
    {
        $grades = $this->gradeRepository->findAll();
        return array_map([$this, 'toDTO'], $grades);
    }

    public function getGradeById(int $id): ?GradeDTO
    {
        $grade = $this->gradeRepository->find($id);
        if ($grade) {
            return $this->toDTO($grade);
        }
        return null;
    }   
    
    public function save(GradeDTO $gradeDTO, int $id = null) : GradeDTO
    {
        $grade = ($id) ? $this->gradeRepository->find($id) : new Grade();
        $grade = $this->toEntity($gradeDTO, $grade);
        // dd($grade);
        $this->em->persist($grade);
        $this->em->flush();
        return $this->toDTO($grade);
    }

    public function delete(int $id) : void
    {
        $grade = $this->gradeRepository->find($id);
        $this->em->remove($grade);
        $this->em->flush();
    }
}