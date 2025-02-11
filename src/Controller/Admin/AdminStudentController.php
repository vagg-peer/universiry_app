<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\StudentDTO;
use App\DTO\GradeDTO;
use App\Form\StudentDTOType;
use App\Form\GradeDTOType;
use App\Service\StudentService;
use App\Service\LessonService;
use App\Service\GradeService;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;

#[Route('/admin/student', name: 'admin_student_')]
class AdminStudentController extends AbstractController
{
    private StudentService $studentService;
    private CsrfTokenManagerInterface $csrfTokenManager;
    private LessonService $lessonService;
    private GradeService $gradeService;

    public function __construct(StudentService $studentService, CsrfTokenManagerInterface $csrfTokenManager, LessonService $lessonService, GradeService $gradeService)
    {
        $this->studentService = $studentService;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->lessonService = $lessonService;
        $this->gradeService = $gradeService;
        
    }

    #[Route('/', name: 'list')]
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        // Fetch all students from the repository
        $students = $this->studentService->getAll();

        $students = $paginator->paginate($students, $request->query->getInt('page', 1), 5);

        // Render the Twig template and pass the list of students
        return $this->render('admin/student/index.html.twig', [
            'students' => $students,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];
        $studentDTO = new StudentDTO();

        $studentForm = $this->createForm(StudentDTOType::class, $studentDTO);
        $studentForm->handleRequest($request);

        if ($studentForm->isSubmitted()) {

            $errors = $validator->validate($studentForm);

            if(count($errors) === 0){
                try{
                    $studentDTO->getUser()->setRoles(['ROLE_STUDENT']);
                    $this->studentService->save($studentDTO);
                    $this->addFlash('success', 'Saved successfully!');
                    return $this->redirectToRoute('admin_student_list');
                }catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already registered. Please use a different email.');
                }
            }
        }

        return $this->render('admin/student/new.html.twig', [
            'studentForm' => $studentForm->createView(),
            'errors' => $errors
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request, ValidatorInterface $validator, PaginatorInterface $paginator): Response
    {
        $errors = [];
        $studentDTO = $this->studentService->getStudentById($id);
        $studentGrades = $studentDTO->getGrades();
        
        $lessonDTOs = $this->lessonService->getStudentAvailableLessonsBySemester($studentDTO->getSemester());
        // dd($lessonDTOs);
        
        
        //student form
        $studentForm = $this->createForm(StudentDTOType::class, $studentDTO);
        
        if($studentGrades){
            $ScoredLessons = array_map(fn($item) => $item->getLesson()->getId(), $studentGrades);
            $remainingLessons = array_filter($lessonDTOs, fn($lesson) => !in_array($lesson->getId(), $ScoredLessons));
        }else{
            $remainingLessons = $lessonDTOs;
        }

        $studentGrades = $paginator->paginate($studentGrades, $request->query->getInt('page', 1), 5);
        
        $gradeDTO = new GradeDTO();
        $gradeDTO->setStudent($studentDTO); // Automatically assign the student
        $gradeForm = $this->createForm(GradeDTOType::class, $gradeDTO, [
            'lessons' => $remainingLessons,
        ]);
        
        $studentForm->handleRequest($request);
        if ($studentForm->isSubmitted()) {

            $errors = $validator->validate($studentForm);

            if(count($errors) === 0){
                try{
                    $studentDTO->getUser()->setRoles(['ROLE_STUDENT']);
                    $this->studentService->save($studentDTO, $id);
                    $this->addFlash('success', 'Saved successfully!');
                    return $this->redirectToRoute('admin_student_list');
                }catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already registered. Please use a different email.');
                }
            }
        }

        // Handle Grade Form Submission
        $gradeForm->handleRequest($request);

        if ($gradeForm->isSubmitted()) {
            $errors = $validator->validate($gradeDTO);
            
            if(count($errors) === 0){
                $this->gradeService->save($gradeDTO);
                $this->addFlash('success', 'Saved successfully!');
                return $this->redirectToRoute('admin_student_edit', ['id' => $id]);
            }

        }
        // $studentDTO = $this->studentService->getStudentById($id);
        
        return $this->render('admin/student/edit.html.twig', [
            'studentForm' => $studentForm->createView(),
            'student' => $studentDTO,
            'gradeForm' => $gradeForm->createView(),
            'grades' => $studentGrades,
            'errors' => $errors
        ]);

        // return $this->render('user/edit.html.twig');
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        // CSRF Protection
        $submittedToken = $request->request->get('_token');
        if (!$this->csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('delete' . $id, $submittedToken))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        $studentDTO = $this->studentService->getStudentById($id);
        

        if (!$studentDTO) {
            throw $this->createNotFoundException('Student not found');
        }
        
        // dd($studentDTO);
        $this->studentService->delete($studentDTO->getId());

        $this->addFlash('success', 'Student deleted successfully.');

        return $this->redirectToRoute('admin_student_list');
    }

}