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

        /**
     * @var GradeDTO[]|null
     */
    private ?array $grades = [];

    /**
     * @var LessonDTO[]|null
     */
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

        /**
     * @return GradeDTO[]|null
     */
    public function getGrades(): ?array
    {
        usort($this->grades, function ($a, $b) {
            return $a->getLesson()->getSemester() <=> $b->getLesson()->getSemester();
        });
        
        return $this->grades;
    }

    /**
     * @param GradeDTO[] $grades
     */
    public function setGrades(array $grades): self
    {
        $this->grades = $grades;
        return $this;
    }

    /**
     * @return LessonDTO[]|null
     */
    public function getLessons(): ?array
    {
        return $this->lessons;
    }

    /**
     * @param LessonDTO[] $lessons
     */
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
        $this->semester = StudentHelper::calculateStudentSemester($this->getStartOfStudies());
        return $this;
    }

    public function __toString()
    {
        return $this->getUser()->getFirstname() . " " . $this->getUser()->getLastname();
    }
}