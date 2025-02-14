<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\StudentService;
use App\Utils\StudentHelper;
use Symfony\Component\HttpFoundation\Request;
use Knp\Component\Pager\PaginatorInterface;
use Symfony\Component\HttpFoundation\RedirectResponse;

class StudentController extends AbstractController
{
    private StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    #[Route('/student', name: 'student_dashboard')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        try{
            //get student dto
            $studentDTO = $this->studentService->getStudentUserById($this->getUser()->getId());//ignore error from php Intelephense 
            //get student grades
            $gradesDTOs = $studentDTO->getGrades();
            //paginate
            $gradesDTOs = $paginator->paginate($gradesDTOs, $request->query->getInt('page', 1), 5);
            //calculate semester
            $semester = StudentHelper::calculateStudentSemester($studentDTO->getStartOfStudies());

        }  catch (\Exception $e) {
            $this->addFlash('error', 'An unexpected error occurred.');
            $referer = $request->headers->get('referer');
            return $referer ? new RedirectResponse($referer) : $this->redirectToRoute('app_login');
        }

        return $this->render('student/index.html.twig', [
            'student' => $studentDTO,
            'grades' => $gradesDTOs,
            'semester' => $semester,
            'homePath' => '/student'
        ]);

    }
}
