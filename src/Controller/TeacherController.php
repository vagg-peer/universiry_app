<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TeacherService;

final class TeacherController extends AbstractController
{
    private TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    #[Route('/teacher', name: 'teacher_dashboard')]
    public function index(): Response
    {
        $teacherDTO = $this->teacherService->getTeacherUserById($this->getUser()->getId());//ignore error from php Intelephense 
        $lessonDTOs = $teacherDTO->getLessons();
        // dd($lessonDTOs);
        return $this->render('teacher/index.html.twig', [
            'teacher' => $teacherDTO,
            'lessons' => $lessonDTOs,
            'homePath' => '/teacher'
        ]);
    }
}
