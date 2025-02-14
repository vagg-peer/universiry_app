<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\DTO\LessonDTO;
use App\Entity\Lesson;
use App\Mapper\LessonMapper;
use App\Repository\LessonRepository;


class LessonService
{
    private LessonMapper $lessonMapper;
    private EntityManagerInterface $em;
    private LessonRepository $lessonRepository;

    public function __construct(EntityManagerInterface $em, LessonRepository $lessonRepository, LessonMapper $lessonMapper)
    {
        $this->em = $em;
        $this->lessonRepository = $lessonRepository;
        $this->lessonMapper = $lessonMapper;
    }

    public function toDTO(Lesson $lesson) : LessonDTO
    {
        return $this->lessonMapper->toDTO($lesson);
    }

    public function toEntity (LessonDTO $lessonDTO) : Lesson
    {
        return $this->lessonMapper->toEntity($lessonDTO);
    }

    public function getAll() : array
    {
        $lessons = $this->lessonRepository->findAllOrderedBySemester();
        return array_map([$this, 'toDTO'], $lessons);
    }

    public function getLessonById(int $id): ?LessonDTO
    {
        $lesson = $this->lessonRepository->find($id);
        if (!$lesson) {
            throw new \RuntimeException("Lesson with ID $id not found.");
        }
        return $this->toDTO($lesson);
    }

    public function getStudentAvailableLessonsBySemester($semester): ?array
    {
        $lessons = $this->lessonRepository->findAllByStudentSemester($semester);
        if ($lessons) {
            return array_map([$this, 'toDTO'], $lessons);
        }
        return null;
    }
    
    public function save(LessonDTO $lessonDTO) : LessonDTO
    {
        try {
            $lesson = $this->toEntity($lessonDTO);
            $lesson->setUpdatedAt(new \DateTimeImmutable());
            $this->em->persist($lesson);
            $this->em->flush();
            return $this->toDTO($lesson);
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to save lesson: " . $e->getMessage());
        }
    }

    public function delete(int $id) : void
    {
         $lesson = $this->lessonRepository->find($id);
        if (!$lesson) {
            throw new \RuntimeException("Lesson with ID $id not found.");
        }

        try {
            $this->em->remove($lesson);
            $this->em->flush();
        } catch (\Exception $e) {
            throw new \RuntimeException("Failed to delete lesson: " . $e->getMessage());
        }
    }
}