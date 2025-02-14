<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\LessonDTO;
use App\Form\LessonDTOType;
use App\Service\LessonService;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;
use App\Service\TeacherService;
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;

#[Route('/admin/lesson', name: 'admin_lesson_')]
class AdminLessonController extends AbstractController
{
    private LessonService $lessonService;
    private TeacherService $teacherService;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(LessonService $lessonService, TeacherService $teacherService,CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->lessonService = $lessonService;
        $this->csrfTokenManager = $csrfTokenManager;
        $this->teacherService = $teacherService;
    }

    #[Route('/', name: 'list')]
    public function index(Request $request, PaginatorInterface $paginator) : Response
    {
        // fetch all lessons
        $lessons = $this->lessonService->getAll();
        // paginate
        $lessons = $paginator->paginate($lessons, $request->query->getInt('page', 1), 5);
        return $this->render('admin/lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ValidatorInterface $validator) : Response
    {
        //set errors empty array
        $errors = [];
        $lessonDTO = new LessonDTO();
        //get all active teachers
        $activeTeacherDTOs = $this->teacherService->getActiveTeachers();

        //lesson form create form
        $form = $this->createForm(LessonDTOType::class, $lessonDTO, [
            'teachers' => $activeTeacherDTOs, 
        ]);

        //handle teacher form
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $errors = $validator->validate($form);
            if(count($errors) === 0){
                $form->get('teacher')->getData() ? $lessonDTO->setTeacher($form->get('teacher')->getData()) : $lessonDTO->setTeacher(null);
                try {
                    $form->get('teacher')->getData() ? $lessonDTO->setTeacher($form->get('teacher')->getData()) : $lessonDTO->setTeacher(null);
                    $this->lessonService->save($lessonDTO);
                    $this->addFlash('success', 'Lesson saved successfully!');
                    return $this->redirectToRoute('admin_lesson_list');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An unexpected error occurred.');
                }
            }
        }

        return $this->render('admin/lesson/new.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request, ValidatorInterface $validator) : Response
    {
        $errors = [];
        $lessonDTO = $this->lessonService->getLessonById($id);
        //get all active teachers
        $activeTeacherDTOs = $this->teacherService->getActiveTeachers();

        //create teacher form
        $form = $this->createForm(LessonDTOType::class, $lessonDTO,[
            'teachers' => $activeTeacherDTOs
        ]);

        //handle teacher form
        $form->handleRequest($request);
        if ($form->isSubmitted()) {
            $errors = $validator->validate($form);
            if(count($errors) === 0){
                try {
                    if($form->get('teacher')->getData()) $lessonDTO->setTeacher($form->get('teacher')->getData());
                    $this->lessonService->save($lessonDTO, $id);
                    $this->addFlash('success', 'Lesson updated successfully!');
                    return $this->redirectToRoute('admin_lesson_list');
                } catch (\Exception $e) {
                    $this->addFlash('error', 'An unexpected error occurred.');
                }
            }
        }
        
        return $this->render('admin/lesson/edit.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lessonDTO,
            'errors' => $errors
        ]);
    }

    #[Route('/delete/{id}', name: 'delete', methods: ['POST'])]
    public function delete(int $id, Request $request) : Response
    {
        // CSRF Protection
        $submittedToken = $request->request->get('_token');
        if (!$this->csrfTokenManager->isTokenValid(new \Symfony\Component\Security\Csrf\CsrfToken('delete' . $id, $submittedToken))) {
            throw $this->createAccessDeniedException('Invalid CSRF token');
        }

        try {
            $this->lessonService->delete($id);
            $this->addFlash('success', 'Lesson deleted successfully.');
        } catch (\RuntimeException $e) {
            $this->addFlash('error', $e->getMessage());
        }

        return $this->redirectToRoute('admin_lesson_list');
    }
}
