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

    #[Assert\NotBlank(message: "Lastname is required.")]
    private array $roles = [];

    #[Assert\Type(type: 'bool', message: "Invalid value. This must be true or false.")]
    private ?bool $isActive = null;

    #[Assert\Length(
        min: 2, 
        max: 30, 
        minMessage: "Password must have at least 2 characters.",
        maxMessage: "Password cannot exceed 30 characters."
    )]
    private ?string $plainPassword = null; // Store the plain password for form handling

    public function getId(): ?int
    {
        return $this->id;
    }

    public function setId(int $id): self
    {
        $this->id = $id;
        return $this;
    }

    /**
     * Get the value of email
     */ 
    public function getEmail()
    {
        return $this->email;
    }

    /**
     * Set the value of email
     *
     * @return  self
     */ 
    public function setEmail($email)
    {
        $this->email = $email;

        return $this;
    }

    /**
     * Get the value of firstname
     */ 
    public function getFirstname()
    {
        return $this->firstname;
    }

    /**
     * Set the value of firstname
     *
     * @return  self
     */ 
    public function setFirstname($firstname)
    {
        $this->firstname = $firstname;

        return $this;
    }

    /**
     * Get the value of lastname
     */ 
    public function getLastname()
    {
        return $this->lastname;
    }

    /**
     * Set the value of lastname
     *
     * @return  self
     */ 
    public function setLastname($lastname)
    {
        $this->lastname = $lastname;

        return $this;
    }

    /**
     * Get the value of roles
     */ 
    public function getRoles()
    {
        return $this->roles;
    }

    /**
     * Set the value of roles
     *
     * @return  self
     */ 
    public function setRoles(array $roles)
    {
        $this->roles = $roles;

        return $this;
    }

    /**
     * Get the value of isActive
     */ 
    public function getIsActive()
    {
        return $this->isActive;
    }

    /**
     * Set the value of isActive
     *
     * @return  self
     */ 
    public function setIsActive(?bool $isActive)
    {
        $this->isActive = $isActive;

        return $this;
    }

    /**
     * Get the value of plainPassword
     */ 
    public function getPlainPassword()
    {
        return $this->plainPassword;
    }

    /**
     * Set the value of plainPassword
     *
     * @return  self
     */ 
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