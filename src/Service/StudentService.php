<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\DTO\StudentDTO;
use App\Entity\Student;
use App\Mapper\StudentMapper;
use App\Repository\StudentRepository;
use App\Service\GradeService;

class StudentService
{
    private EntityManagerInterface $em;
    private StudentRepository $studentRepository;
    private StudentMapper $studentMapper;
    private GradeService $gradeService;

    public function __construct(EntityManagerInterface $em, StudentRepository $studentRepository, StudentMapper $studentMapper, GradeService $gradeService)
    {
        $this->em = $em;
        $this->studentRepository = $studentRepository;
        $this->studentMapper = $studentMapper;
        $this->gradeService = $gradeService;
    }

    public function toDTO(Student $student) : StudentDTO
    {
        $gradeDTOs = array_map(fn($grade) => $this->gradeService->toDTO($grade), $student->getGrades()->toArray());
        return $this->studentMapper->toDTO($student, $gradeDTOs);
    }

    public function toEntity (StudentDTO $studentDTO) : Student
    {
        return $this->studentMapper->toEntity($studentDTO);
    }

    public function getAll() : array
    {
        $students = $this->studentRepository->findAll();
        return array_map([$this, 'toDTO'], $students);
    }

    public function getStudentById(int $id): ?StudentDTO
    {
        $student = $this->studentRepository->find($id);
        if ($student) {
            return $this->toDTO($student);
        }
        return null;
    }
    
    public function getStudentUserById(int $id): ?StudentDTO
    {
        $student = $this->studentRepository->findStudentByUserId($id);
        if ($student) {
            return $this->toDTO($student);
        }
        return null;
    }
    
    public function save(StudentDTO $studentDTO) : StudentDTO
    {
        $student = $this->toEntity($studentDTO);
        $this->em->persist($student);
        $this->em->flush();
        return $this->toDTO($student);
    }

    public function delete(int $id) : void
    {
        $student = $this->studentRepository->find($id);
        $this->em->remove($student);
        $this->em->flush();
    }
}