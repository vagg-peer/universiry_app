<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use App\Service\StudentService;
use App\Utils\StudentHelper;

final class StudentController extends AbstractController
{
    private StudentService $studentService;

    public function __construct(StudentService $studentService)
    {
        $this->studentService = $studentService;
    }

    #[Route('/student', name: 'student_dashboard')]
    public function index(): Response
    {
        $studentDTO = $this->studentService->getStudentUserById($this->getUser()->getId());//ignore error from php Intelephense 
        $gradesDTO = $studentDTO->getGrades();
        $semester = StudentHelper::calculateStudentSemester($studentDTO->getStartOfStudies());
        // dd($semester);
        return $this->render('student/index.html.twig', [
            'student' => $studentDTO,
            'grades' => $gradesDTO,
            'semester' => $semester,
            'homePath' => '/student'
        ]);
    }
}
