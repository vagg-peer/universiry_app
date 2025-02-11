<?php
namespace App\Security;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Symfony\Component\Security\Http\Authorization\AccessDeniedHandlerInterface;
use Twig\Environment;

class AccessDeniedHandler implements AccessDeniedHandlerInterface
{
    private Environment $twig;

    public function __construct(Environment $twig)
    {
        $this->twig = $twig;
    }

    public function handle(Request $request, AccessDeniedException $accessDeniedException): ?Response
    {
        try {
            // Render the custom Access Denied page
            $content = $this->twig->render('security/access_denied.html.twig');
            return new Response($content, Response::HTTP_FORBIDDEN);
        } catch (\Exception $e) {
            // Fallback to a simple plain text response if Twig fails
            return new Response('Access Denied', Response::HTTP_FORBIDDEN);
        }
    }
}