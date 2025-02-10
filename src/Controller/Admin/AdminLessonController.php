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
    public function index() : Response
    {
        // Fetch all lessons from the repository
        $lessons = $this->lessonService->getAll();

        // Render the Twig template and pass the list of lessons
        return $this->render('admin/lesson/index.html.twig', [
            'lessons' => $lessons,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request) : Response
    {
        $lessonDTO = new LessonDTO();

        $activeTeacherDTOs = $this->teacherService->getActiveTeachers();

        $form = $this->createForm(LessonDTOType::class, $lessonDTO, [
            'teachers' => $activeTeacherDTOs, // Pass DTOs to the form
        ]);

        // $form = $this->createForm(LessonDTOType::class, $lessonDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()){
                $this->addFlash('error', 'There were errors in the form. Please fix them.');
            }

            $form->get('teacher')->getData() ?? $lessonDTO->setTeacher($form->get('teacher')->getData());

            $this->lessonService->save($lessonDTO);

            return $this->redirectToRoute('admin_lesson_list'); // Adjust the route as needed
        }

        return $this->render('admin/lesson/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request) : Response
    {
        $lessonDTO = $this->lessonService->getLessonById($id);

        $activeTeacherDTOs = $this->teacherService->getActiveTeachers();

        $form = $this->createForm(LessonDTOType::class, $lessonDTO,[
            'teachers' => $activeTeacherDTOs
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()){
                $this->addFlash('error', 'There were errors in the form. Please fix them.');
            }

            $this->lessonService->save($lessonDTO, $id);

            return $this->redirectToRoute('admin_lesson_list');
        }
        $lessonDTO = $this->lessonService->getLessonById($id);
        
        return $this->render('admin/lesson/edit.html.twig', [
            'form' => $form->createView(),
            'lesson' => $lessonDTO
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

        $lessonDTO = $this->lessonService->getLessonById($id);
        

        if (!$lessonDTO) {
            throw $this->createNotFoundException('Lesson not found');
        }
        
        // dd($lessonDTO);
        $this->lessonService->delete($lessonDTO->getId());

        $this->addFlash('success', 'Lesson deleted successfully.');

        return $this->redirectToRoute('admin_lesson_list');
    }
}
