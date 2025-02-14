<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\TeacherService;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class TeacherController extends AbstractController
{
    private TeacherService $teacherService;

    public function __construct(TeacherService $teacherService)
    {
        $this->teacherService = $teacherService;
    }

    #[Route('/teacher', name: 'teacher_dashboard')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        try{
            $teacherDTO = $this->teacherService->getTeacherUserById($this->getUser()->getId());//ignore error from php Intelephense 
            $lessonDTOs = $teacherDTO->getLessons();
            $lessonDTOs = $paginator->paginate($lessonDTOs, $request->query->getInt('page', 1), 5);
        }  catch (\Exception $e) {
            $this->addFlash('error', 'An unexpected error occurred.');
            $referer = $request->headers->get('referer');
            return $referer ? new RedirectResponse($referer) : $this->redirectToRoute('app_login');
        }
        return $this->render('teacher/index.html.twig', [
            'teacher' => $teacherDTO,
            'lessons' => $lessonDTOs,
            'homePath' => '/teacher'
        ]);
    }
}
