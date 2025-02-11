<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;
use App\Utils\StudentHelper;
use DateTimeImmutable;

class StudentDTO
{
    private ?int $id = null;

    #[Assert\NotBlank(message: "Start date of studies is required.")]
    private ?\DateTimeInterface $startOfStudies = null;

    #[Assert\NotNull]
    #[Assert\Valid]
    private ?UserDTO $user = null;

    #[Assert\Type(type: 'bool', message: "Invalid value. This must be true or false.")]
    private ?bool $isActive = null;

    private ?array $grades = [];

    private ?array $lessons = [];

    private ?int $semester = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getStartOfStudies(): ?\DateTimeInterface
    {
        return $this->startOfStudies;
    }

    public function setStartOfStudies(\DateTimeInterface $startOfStudies): self
    {
        $this->startOfStudies = $startOfStudies;
        $this->setSemester();
        return $this;
    }

    public function getUser(): ?UserDTO
    {
        return $this->user;
    }

    public function setUser(UserDTO $user): self
    {
        $this->user = $user;
        return $this;
    }

    public function getIsActive(): ?bool
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive): self
    {
        $this->isActive = $isActive;
        return $this;
    }

    public function getGrades(): ?array
    {
        usort($this->grades, function ($a, $b) {
            return $a->getLesson()->getSemester() <=> $b->getLesson()->getSemester();
        });
        
        return $this->grades;
    }

    public function setGrades(array $grades): self
    {
        $this->grades = $grades;
        return $this;
    }

    public function getLessons(): ?array
    {
        return $this->lessons;
    }

    public function setLessons(array $lessons): self
    {
        $this->lessons = $lessons;
        return $this;
    }

    public function getSemester(): ?int
    {
        return $this->semester;
    }

    public function setSemester(): self
    {
        //use helper to calculate student's semester by startOfStudies
        $this->semester = StudentHelper::calculateStudentSemester($this->getStartOfStudies());
        return $this;
    }

    public function __toString()
    {
        return $this->getUser()->getFirstname() . " " . $this->getUser()->getLastname();
    }
}