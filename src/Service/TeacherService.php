<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\DTO\TeacherDTO;
use App\Entity\Teacher;
use App\Mapper\TeacherMapper;
use App\Repository\TeacherRepository;
use App\Service\LessonService;



class TeacherService
{
    private EntityManagerInterface $em;
    private TeacherRepository $teacherRepository;
    private TeacherMapper $teacherMapper;
    private LessonService $lessonService;

    public function __construct(EntityManagerInterface $em, TeacherRepository $teacherRepository, TeacherMapper $teacherMapper, LessonService $lessonService)
    {
        $this->em = $em;
        $this->teacherRepository = $teacherRepository;
        $this->teacherMapper = $teacherMapper;
        $this->lessonService = $lessonService;
    }

    public function toDTO(Teacher $teacher) : TeacherDTO
    {
        $lessonDTOs = array_map(fn($grade) => $this->lessonService->toDTO($grade), $teacher->getLessons()->toArray());
        return $this->teacherMapper->toDTO($teacher, $lessonDTOs);
    }

    public function toEntity (TeacherDTO $teacherDTO) : Teacher
    {
        return $this->teacherMapper->toEntity($teacherDTO);
    }

    public function getAll() : array
    {
        $teachers = $this->teacherRepository->findAll();
        return array_map([$this, 'toDTO'], $teachers);
    }

    public function getTeacherById(int $id): ?TeacherDTO
    {
        $teacher = $this->teacherRepository->find($id);
        return $teacher ? $this->toDTO($teacher) : null;
    }

    public function getTeacherUserById(int $id): ?TeacherDTO
    {
        $teacher = $this->teacherRepository->findTeacherByUserId($id);
        if ($teacher) {
            return $this->toDTO($teacher);
        }
        return null;
    }

    public function getActiveTeachers(): ?array
    {
        $teachers = $this->teacherRepository->findActiveTeachers();
        return array_map([$this, 'toDTO'], $teachers);
    } 
    
    public function save(TeacherDTO $teacherDTO) : TeacherDTO
    {
        // $teacher = $id ? $this->teacherRepository->find($id) : new Teacher();
        $teacher = $this->toEntity($teacherDTO);
        $this->em->persist($teacher);
        $this->em->flush();
        return $this->toDTO($teacher);
    }

    public function delete(int $id) : void
    {
        $teacher = $this->teacherRepository->find($id);
        $this->em->remove($teacher);
        $this->em->flush();
    }
}