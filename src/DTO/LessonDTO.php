<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LessonDTO
{
    private ?int $id = null;

    #[Assert\NotBlank(message: "Lesson name is required.")]
    #[Assert\Length(min: 3, max: 255, message: "Name must have 3-255 characters")]
    private ?string $name = null;

    #[Assert\NotBlank]
    #[Assert\Range(min: 0, max: 10, message: "Semester must be between 0-10")]
    private ?int $semester = null;

    
    private ?TeacherDTO $teacher = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getName(): ?string
    {
        return $this->name;
    }

    public function setName(string $name): self
    {
        $this->name = $name;
        return $this;
    }

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(int $semester): self
    {
        $this->semester = $semester;
        return $this;
    }

    public function getTeacher(): ?TeacherDTO
    {
        return $this->teacher;
    }

    public function setTeacher(?TeacherDTO $teacher): self
    {
        $this->teacher = $teacher;
        return $this;
    }
}