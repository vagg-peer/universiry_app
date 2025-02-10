<?php
namespace App\Mapper;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Repository\UserRepository;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class UserMapper{

    private UserPasswordHasherInterface $passwordHasher;
    private UserRepository $userRepository;

    public function __construct(UserPasswordHasherInterface $passwordHasher, UserRepository $userRepository)
    {
        $this->passwordHasher = $passwordHasher;
        $this->userRepository = $userRepository;
    }

    public function toDTO(User $user) : UserDTO
    {
        $dto = new UserDTO();
        $dto->setId($user->getId());
        $dto->setEmail($user->getEmail());
        $dto->setFirstname($user->getFirstname());
        $dto->setLastname($user->getLastname());
        $user->getRoles() ? $dto->setRoles($user->getRoles()) : null;
        $dto->setIsActive($user->isActive());
        
        return $dto;
    }

    public function toEntity (UserDTO $dto) : User
    {
        $user = $dto->getId() ? $this->userRepository->find($dto->getId()) : new User();
        $user->setEmail($dto->getEmail());
        $user->setFirstname($dto->getFirstname());
        $user->setLastname($dto->getLastname());
        $user->setRoles($dto->getRoles());
        $user->setIsActive($dto->getIsActive());

        if ($dto->getPlainPassword()) {
            $user->setPassword($this->passwordHasher->hashPassword($user, $dto->getPlainPassword()));
        }else{
            if($user->getPassword()) $user->setPassword($user->getPassword());
        }

        return $user;
    }
}