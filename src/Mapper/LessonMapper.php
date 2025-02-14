<?php
namespace App\Mapper;

use App\DTO\LessonDTO;
use App\Entity\Lesson;
use App\Mapper\TeacherMapper;
use App\Repository\LessonRepository;



class LessonMapper
{
    private TeacherMapper $teacherMapper;
    private LessonRepository $lessonRepository;

    public function __construct(TeacherMapper $teacherMapper, LessonRepository $lessonRepository)
    {
        $this->teacherMapper = $teacherMapper;
        $this->lessonRepository = $lessonRepository;
    }
    public function toEntity(LessonDTO $dto): Lesson
    {
        $lesson = $dto->getId() ? $this->lessonRepository->find($dto->getId()) : new Lesson();
        $lesson->setName($dto->getName());
        $lesson->setSemester($dto->getSemester());
        if($dto->getTeacher()) $lesson->setTeacher($this->teacherMapper->toEntity($dto->getTeacher()));
        $dto->getTeacher() ? $lesson->setTeacher($this->teacherMapper->toEntity($dto->getTeacher())) : $lesson->setTeacher(null);
        return $lesson;
    }

    public function toDTO(Lesson $lesson): LessonDTO
    {
        $dto = new LessonDTO();
        $dto->setId($lesson->getId());
        $dto->setName($lesson->getName());
        $dto->setSemester($lesson->getSemester());
        if($lesson->getTeacher()) $dto->setTeacher($this->teacherMapper->toDTO($lesson->getTeacher()));
        return $dto;
    }
}