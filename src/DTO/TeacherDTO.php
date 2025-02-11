<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class TeacherDTO
{
    private ?int $id = null;

    #[Assert\NotNull]
    #[Assert\Valid]
    private ?UserDTO $user = null;

    #[Assert\Type(type: 'bool', message: "Invalid value. This must be true or false.")]
    private ?bool $isActive = null;

    private ?array $lessons = [];


    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
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

    public function getLessons(): ?array
    {
        usort($this->lessons, function ($a, $b) {
            return $a->getSemester() <=> $b->getSemester();
        });
        return $this->lessons;
    }

    public function setLessons(array $lessons): self
    {
        $this->lessons = $lessons;
        return $this;
    }

    public function __toString()
    {
        return $this->getUser()->getFirstname() . " " . $this->getUser()->getLastname();
    }
}