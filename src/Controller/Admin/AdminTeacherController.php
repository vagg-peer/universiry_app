<?php

namespace App\Controller\Admin;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use App\DTO\TeacherDTO;
use App\Form\TeacherDTOType;
use App\Service\TeacherService;
use Symfony\Component\Security\Csrf\CsrfTokenManagerInterface;

#[Route('/admin/teacher', name: 'admin_teacher_')]
class AdminTeacherController extends AbstractController
{
    private TeacherService $teacherService;
    private CsrfTokenManagerInterface $csrfTokenManager;

    public function __construct(TeacherService $teacherService, CsrfTokenManagerInterface $csrfTokenManager)
    {
        $this->teacherService = $teacherService;
        $this->csrfTokenManager = $csrfTokenManager;
        
    }

    #[Route('/', name: 'list')]
    public function index(): Response
    {
        // Fetch all teachers from the repository
        $teachers = $this->teacherService->getAll();

        // Render the Twig template and pass the list of teachers
        return $this->render('admin/teacher/index.html.twig', [
            'teachers' => $teachers,
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request): Response
    {
        $teacherDTO = new TeacherDTO();

        $form = $this->createForm(TeacherDTOType::class, $teacherDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()){
                $this->addFlash('error', 'There were errors in the form. Please fix them.');
            }

            $teacherDTO->getUser()->setRoles(['ROLE_TEACHER']);

            $this->teacherService->save($teacherDTO);

            return $this->redirectToRoute('admin_teacher_list'); // Adjust the route as needed
        }

        return $this->render('admin/teacher/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request): Response
    {

        $teacherDTO = $this->teacherService->getTeacherById($id);
        // dd($teacherDTO);

        $form = $this->createForm(TeacherDTOType::class, $teacherDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            if (!$form->isValid()){
                $this->addFlash('error', 'There were errors in the form. Please fix them.');
            }

            $teacherDTO->getUser()->setRoles(['ROLE_TEACHER']);

            // dd($teacherDTO);

            $this->teacherService->save($teacherDTO, $id);

            return $this->redirectToRoute('admin_teacher_list');
        }
        $teacherDTO = $this->teacherService->getTeacherById($id);
        
        return $this->render('admin/teacher/edit.html.twig', [
            'form' => $form->createView(),
            'teacher' => $teacherDTO,
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

        $teacherDTO = $this->teacherService->getTeacherById($id);
        

        if (!$teacherDTO) {
            throw $this->createNotFoundException('Teacher not found');
        }
        
        // dd($teacherDTO);
        $this->teacherService->delete($teacherDTO->getId());

        $this->addFlash('success', 'Teacher deleted successfully.');

        return $this->redirectToRoute('admin_teacher_list');
    }
    //check εδω να βάλω να παίρναι αυτόματα τον ρολο

}
