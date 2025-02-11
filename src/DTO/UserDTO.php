<?php
namespace App\DTO;

use Symfony\Component\Validator\Constraints as Assert;

class UserDTO
{
    private ?int $id = null;

    #[Assert\NotBlank(message: "Email is required.")]
    #[Assert\Email (message: "Please enter a valid email address.")]
    private ?string $email = null;

    #[Assert\NotBlank(message: "First name is required.")]
    #[Assert\Length(
        min: 2, 
        max: 50, 
        minMessage: "Firstname must have at least 2 characters.",
        maxMessage: "Firstname cannot exceed 50 characters."
    )]
    private ?string $firstname = null;

    #[Assert\NotBlank(message: "Lastname is required.")]
    #[Assert\Length(
        min: 2, 
        max: 50, 
        minMessage: "Lastname must have at least 2 characters.",
        maxMessage: "Lastname cannot exceed 50 characters."
    )]
    private ?string $lastname = null;

    private array $roles = [];

    #[Assert\Type(type: 'bool', message: "Invalid value. This must be true or false.")]
    private ?bool $isActive = null;

    #[Assert\Length(
        min: 6, 
        max: 30, 
        minMessage: "Password must have at least 6 characters.",
        maxMessage: "Password cannot exceed 30 characters."
    )]
    private ?string $plainPassword = null;

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    public function getEmail()
    {
        return $this->email;
    }

    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    public function getFirstname()
    {
        return $this->firstname;
    }

    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    public function getLastname()
    {
        return $this->lastname;
    }

    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    public function getRoles()
    {
        return $this->roles;
    }

    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    public function getIsActive()
    {
        return $this->isActive;
    }

    public function setIsActive(?bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    public function setPlainPassword($plainPassword)
    {
        $this->plainPassword = $plainPassword;

        return $this;
    }

    public function __toString()
    {
        return $this->firstname . " " . $this->lastname;
    }
}