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
        if (!$student) {
            throw new \RuntimeException("Student with ID $id not found.");
        }
        return $this->toDTO($student);
    }
    
    public function getStudentUserById(int $id): ?StudentDTO
    {
        $student = $this->studentRepository->findStudentByUserId($id);
        if (!$student) {
            throw new \RuntimeException("No student found for user ID $id.");
        }
        return $this->toDTO($student);
    }
    
    public function save(StudentDTO $studentDTO) : StudentDTO
    {
        if (!$studentDTO->getUser()) {
            throw new \InvalidArgumentException("Student must have an associated user.");
        }
        try {
            $studentDTO->getUser()->setRoles(['ROLE_STUDENT']);
            $student = $this->toEntity($studentDTO);
            $student->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($student);
            $this->em->flush();
            return $this->toDTO($student);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to save student: " . $e->getMessage());
        }
    }

    public function delete(int $id) : void
    {
        $student = $this->studentRepository->find($id);
        if (!$student) {
            throw new \RuntimeException("Student with ID $id not found.");
        }
        try {
            $this->em->remove($student);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to delete student: " . $e->getMessage());
        }
    }
}