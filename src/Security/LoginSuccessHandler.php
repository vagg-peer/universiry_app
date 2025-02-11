<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    private RouterInterface $router;

    public function __construct(RouterInterface $router)
    {
        $this->router = $router;
    }

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $user = $token->getUser();

        if (in_array('ROLE_ADMIN', $user->getRoles(), true)) {
            return new RedirectResponse($this->router->generate('admin_dashboard'));
        }

        if (in_array('ROLE_TEACHER', $user->getRoles(), true)) {
            return new RedirectResponse($this->router->generate('teacher_dashboard'));
        }

        if (in_array('ROLE_STUDENT', $user->getRoles(), true)) {
            return new RedirectResponse($this->router->generate('student_dashboard'));
        }

        // Default redirect (fallback)
        return new RedirectResponse($this->router->generate('homepage'));
    }
}