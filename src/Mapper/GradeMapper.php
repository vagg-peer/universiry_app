<?php
namespace App\Mapper;

use App\DTO\GradeDTO;
use App\Entity\Grade;
use App\Entity\Student;
use App\Entity\Lesson;
use App\DTO\StudentDTO;
use App\DTO\LessonDTO;

class GradeMapper
{
    public function toDTO(Grade $grade, StudentDTO $studentDTO, LessonDTO $lessonDTO): GradeDTO
    {
        $dto = new GradeDTO();
        $dto->setScore($grade->getScore());
        $dto->setStudent($studentDTO);
        $dto->setLesson($lessonDTO);
        return $dto;
    }

    public function toEntity(GradeDTO $dto, Student $student, Lesson $lesson): Grade
    {
        $grade = $grade ?? new Grade();
        $grade->setScore($dto->getScore());
        $grade->setStudent($student);
        $grade->setLesson($lesson);  
 
        return $grade;
    }
}