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
        $students = $this->studentService->getAll();

        //pagination
        $students = $paginator->paginate($students, $request->query->getInt('page', 1), 5);

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
                }catch (\Exception $e) {
                    $this->addFlash('error', 'An unexpected error occurred.');
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
        try {
            $studentDTO = $this->studentService->getStudentById($id);
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
            return $this->redirectToRoute('admin_student_list');
        }
        
        $studentGrades = $studentDTO->getGrades();
        //get lessons available to the student. lessons.semmester <= student->semester
        $lessonDTOs = $this->lessonService->getStudentAvailableLessonsBySemester($studentDTO->getSemester());
        //create student form
        $studentForm = $this->createForm(StudentDTOType::class, $studentDTO);
        
        //get lessons with no score
        if($studentGrades){
            //get all ids from every lesson has a score 
            $ScoredLessons = array_map(fn($item) => $item->getLesson()->getId(), $studentGrades);
            //exclude lessons tha has grade from remaining lessons
            $remainingLessons = array_filter($lessonDTOs, fn($lesson) => !in_array($lesson->getId(), $ScoredLessons));
        }else{
            $remainingLessons = $lessonDTOs;
        }
        //grade pegination
        $studentGrades = $paginator->paginate($studentGrades, $request->query->getInt('page', 1), 5);
        
        //create grades form
        $gradeDTO = new GradeDTO();
        $gradeDTO->setStudent($studentDTO); 
        $gradeForm = $this->createForm(GradeDTOType::class, $gradeDTO, [
            'lessons' => $remainingLessons,
        ]);
        
        //handle student form
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
                }catch (\Exception $e) {
                    $this->addFlash('error', 'An unexpected error occurred.');
                }
            }
        }

        //handle grade form
        $gradeForm->handleRequest($request);
        if ($gradeForm->isSubmitted()) {
            $errors = $validator->validate($gradeDTO);
            if(count($errors) === 0){
                try {
                    $this->gradeService->save($gradeDTO);
                    $this->addFlash('success', 'Grade added successfully!');
                    return $this->redirectToRoute('admin_student_edit', ['id' => $id]);
                } catch (\Exception $e) {
                    $this->addFlash('error', 'Failed to save grade.');
                }
            }    
        }
        
        return $this->render('admin/student/edit.html.twig', [
            'studentForm' => $studentForm->createView(),
            'student' => $studentDTO,
            'gradeForm' => $gradeForm->createView(),
            'grades' => $studentGrades,
            'errors' => $errors
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, Request $request): Response
    {
        // CSRF Protection
        $submittedToken = $request->request->get('_token');
        if (!$this->csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('delete' . $id, $submittedToken))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        try {
            $this->studentService->delete($id);
            $this->addFlash('success', 'Student deleted successfully.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }
        return $this->redirectToRoute('admin_student_list');
    }

}