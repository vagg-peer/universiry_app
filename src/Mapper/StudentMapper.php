<?php
namespace App\Mapper;

use App\DTO\StudentDTO;
use App\Entity\Student;
use App\Mapper\UserMapper;
use App\Repository\StudentRepository;

class StudentMapper
{
    private UserMapper $userMapper;
    private StudentRepository $studentRepository;

    public function __construct(UserMapper $userMapper, StudentRepository $studentRepository)
    {
        $this->userMapper = $userMapper;
        $this->studentRepository = $studentRepository;
    }

    public function toEntity(StudentDTO $dto): Student
    {
        $student =  $dto->getId() ? $this->studentRepository->find($dto->getId()) : new Student();
        $student->setStartOfStudies($dto->getStartOfStudies());
        $student->setUser($this->userMapper->toEntity($dto->getUser()));
        $student->setIsActive($dto->getIsActive());
        return $student;
    }

    public function toDTO(Student $student, array $gradeDTOs = []): StudentDTO
    {
        $dto = new StudentDTO();
        $dto->setId($student->getId());
        $dto->setStartOfStudies($student->getStartOfStudies());
        $dto->setUser($this->userMapper->toDTO($student->getUser()));
        $dto->setIsActive($student->isActive());
        $dto->setGrades($gradeDTOs);

        return $dto;
    }
}