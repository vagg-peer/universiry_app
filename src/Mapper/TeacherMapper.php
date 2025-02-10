<?php
namespace App\Mapper;

use App\DTO\TeacherDTO;
use App\Entity\Teacher;
use App\Repository\TeacherRepository;
use App\Mapper\UserMapper;
use App\DTO\LessonDTO;

class TeacherMapper
{
    private UserMapper $userMapper;
    private TeacherRepository $teacherRepository;

    public function __construct(UserMapper $userMapper, TeacherRepository $teacherRepository)
    {
        $this->userMapper = $userMapper;
        $this->teacherRepository = $teacherRepository;
    }

    public function toEntity(TeacherDTO $dto): Teacher
    {
        $teacher = $dto->getId() ? $this->teacherRepository->find($dto->getId()) : new Teacher();
        $teacher->setUser($this->userMapper->toEntity($dto->getUser()));
        $teacher->setIsActive($dto->getIsActive());
        return $teacher;
    }

    public function toDTO(Teacher $teacher, array $lessonDTO = []): TeacherDTO
    {
        $dto = new TeacherDTO();
        $dto->setId($teacher->getId());
        $dto->setUser($this->userMapper->toDTO($teacher->getUser()));
        $dto->setIsActive($teacher->isActive());
        $dto->setLessons($lessonDTO);
        return $dto;
    }
}