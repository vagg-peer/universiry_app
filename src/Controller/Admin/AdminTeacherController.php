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
use Symfony\Component\Validator\Validator\ValidatorInterface;
use Knp\Component\Pager\PaginatorInterface;
use Doctrine\DBAL\Exception\UniqueConstraintViolationException;


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
    public function index(Request $request, PaginatorInterface $paginator): Response
    {
        $teachers = $this->teacherService->getAll();

        $teachers = $paginator->paginate($teachers, $request->query->getInt('page', 1), 5);

        return $this->render('admin/teacher/index.html.twig', [
            'teachers' => $teachers           
        ]);
    }

    #[Route('/new', name: 'new')]
    public function new(Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];
        $teacherDTO = new TeacherDTO();

        $form = $this->createForm(TeacherDTOType::class, $teacherDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $errors = $validator->validate($form);

            if(count($errors) === 0){
                try{    
                    $teacherDTO->getUser()->setRoles(['ROLE_TEACHER']);
                    $this->teacherService->save($teacherDTO);
                    $this->addFlash('success', 'Saved successfully!');
                    return $this->redirectToRoute('admin_teacher_list');
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already registered. Please use a different email.');
                } 
            }
        }

        return $this->render('admin/teacher/new.html.twig', [
            'form' => $form->createView(),
            'errors' => $errors
        ]);
    }

    #[Route('/edit/{id}', name: 'edit')]
    public function edit(int $id, Request $request, ValidatorInterface $validator): Response
    {
        $errors = [];
        $teacherDTO = $this->teacherService->getTeacherById($id);
        

        $form = $this->createForm(TeacherDTOType::class, $teacherDTO);
        $form->handleRequest($request);

        if ($form->isSubmitted()) {

            $errors = $validator->validate($form);

            if(count($errors) === 0){
                try{    
                    $teacherDTO->getUser()->setRoles(['ROLE_TEACHER']);
                    $this->teacherService->save($teacherDTO, $id);
                    $this->addFlash('success', 'Saved successfully!');
                    return $this->redirectToRoute('admin_teacher_list');
                } catch (UniqueConstraintViolationException $e) {
                    $this->addFlash('error', 'This email is already registered. Please use a different email.');
                } 
            }
        }
        $teacherDTO = $this->teacherService->getTeacherById($id);
        
        return $this->render('admin/teacher/edit.html.twig', [
            'form' => $form->createView(),
            'teacher' => $teacherDTO,
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

        $teacherDTO = $this->teacherService->getTeacherById($id);
        

        if (!$teacherDTO) {
            throw $this->createNotFoundException('Teacher not found');
        }
        
        $this->teacherService->delete($teacherDTO->getId());

        $this->addFlash('success', 'Teacher deleted successfully.');

        return $this->redirectToRoute('admin_teacher_list');
    }

}
