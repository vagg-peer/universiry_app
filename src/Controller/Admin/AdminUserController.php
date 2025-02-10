<?php
namespace App\Controller\Admin;

use App\DTO\UserDTO;
use App\Entity\User;
use App\Form\UserDTOType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;
use App\Service\UserService;

class AdminUserController extends AbstractController
{
    #[Route('admin/user/new', name: 'user_new')]
    public function new(Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userDTO = new UserDTO();

        $form = $this->createForm(UserDTOType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            // Convert DTO to Entity
            $userService = new UserService($passwordHasher, $em);
            $userService->save($userDTO);

            return $this->redirectToRoute('user_list'); // Adjust the route as needed
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('admin/user/edit/{id}', name: 'user_edit')]
    public function edit(User $user, Request $request, EntityManagerInterface $em, UserPasswordHasherInterface $passwordHasher): Response
    {
        $userService = new UserService($passwordHasher, $em);
        $userDTO = $userService->toDTO($user);

        $form = $this->createForm(UserDTOType::class, $userDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $userService->save($userDTO);

            return $this->redirectToRoute('user_list');
        }

        return $this->render('user/edit.html.twig', [
            'form' => $form->createView(),
            'user' => $user,
        ]);

        return $this->render('user/edit.html.twig');
    }
}
