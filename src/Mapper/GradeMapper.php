<?php
namespace App\Mapper;

use App\DTO\GradeDTO;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Lesson;
use App\DTO\StudentDTO;
use App\DTO\LessonDTO;
use App\Repository\GradeRepository;
use App\Mapper\LessonMapper;
use App\Mapper\StudentMapper;

class GradeMapper
{
    private GradeRepository $gradeRepository;
    private LessonMapper $lessonMapper;
    private StudentMapper $studentMapper;

    public function __construct(GradeRepository $gradeRepository, LessonMapper $lessonMapper, StudentMapper $studentMapper)
    {
        $this->gradeRepository = $gradeRepository;
        $this->lessonMapper = $lessonMapper;
        $this->studentMapper = $studentMapper;
    }
 
    public function toDTO(Grade $grade): GradeDTO
    {
        $dto = new GradeDTO();
        $dto->setScore($grade->getScore());
        // $dto->setStudent($studentDTO);
        $dto->setStudent($this->studentMapper->toDTO($grade->getStudent()));
        // $dto->setLesson($lessonDTO);
        $dto->setLesson($this->lessonMapper->toDTO($grade->getLesson()));
        return $dto;
    }

    public function toEntity(GradeDTO $dto): Grade
    {
        $grade = $dto->getId() ? $this->gradeRepository->find($dto->getId()) : new Grade();
        $grade->setScore($dto->getScore());
        // $grade->setStudent($student);
        $grade->setStudent($this->studentMapper->toEntity($dto->getStudent()));

        // $grade->setLesson($lesson);
        $grade->setLesson($this->lessonMapper->toEntity($dto->getLesson())); 
 
        return $grade;
    }
}