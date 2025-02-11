<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class LessonDTO
{
    private ?int $id = null;

    #[Assert\NotBlank(message: "Lesson is required.")]
    #[Assert\Length(
        min: 2, 
        max: 50, 
        minMessage: "Lesson must have at least 2 characters.",
        maxMessage: "Lesson cannot exceed 50 characters."
    )]
    private ?string $name = null;

    #[Assert\NotBlank(message: "Semester is required")]
    #[Assert\Type(type: "int", message: "Semester must be a valid float.")]
    #[Assert\Positive(message: "Semester must be 1 or higher.")] // Prevents negative values
    #[Assert\LessThanOrEqual(value: 10, message: "Semester cannot be greater than 10.")]
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