<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class GradeDTO
{
    private ?int $id = null;

    #[Assert\NotBlank(message: "Score is required")]
    #[Assert\Type(type: "float", message: "Score must be a valid float.")]
    #[Assert\PositiveOrZero(message: "Score must be 0 or higher.")] // Prevents negative values
    #[Assert\LessThanOrEqual(value: 10, message: "Score cannot be greater than 10.")]
    private ?float $score = null;

    #[Assert\NotNull]
    private ?StudentDTO $student = null;

    #[Assert\NotNull]
    private ?LessonDTO $lesson = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function getScore(): ?float
    {
        return $this->score;
    }

    public function setScore(float $score): self
    {
        $this->score = $score;
        return $this;
    }

    public function getStudent(): ?StudentDTO
    {
        return $this->student;
    }

    public function setStudent(StudentDTO $student): self
    {
        $this->student = $student;
        return $this;
    }

    public function getLesson(): ?LessonDTO
    {
        return $this->lesson;
    }

    public function setLesson(LessonDTO $lesson): self
    {
        $this->lesson = $lesson;
        return $this;
    }
}