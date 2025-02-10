<?php
namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use App\DTO\UserDTO;
use App\Entity\User;
use App\Mapper\UserMapper;

class UserService
{
    private EntityManagerInterface $em;
    private UserMapper $userMapper;

    public function __construct(EntityManagerInterface $em, UserMapper $userMapper)
    {
        $this->em = $em;
        $this->userMapper = $userMapper;
    }

    public function toDTO(User $user) : UserDTO
    {
        return $this->userMapper->toDTO($user);
    }

    public function toEntity (UserDTO $userDTO) : User
    {
        return $this->userMapper->toEntity($userDTO);
    }

    public function save(UserDTO $userDTO) : UserDTO
    {   
        $user = $this->toEntity($userDTO);
        $this->em->persist($user);
        $this->em->flush();
        return $this->toDTO($user);
    }
}