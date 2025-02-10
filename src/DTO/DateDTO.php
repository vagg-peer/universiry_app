<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class DateDTO
{
    private ?int $id = null;

    #[Assert\NotBlank]
    #[Assert\Length(min: 3, max: 255)]
    private ?string $name = null;

    public function getId(): ?int
    {
        return $this->id;
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
}