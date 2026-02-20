<?php

namespace App\Security;

use App\Enum\UserRole;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\AuthenticationSuccessHandlerInterface;
use Symfony\Component\Routing\RouterInterface;

class LoginSuccessHandler implements AuthenticationSuccessHandlerInterface
{
    public function __construct(private RouterInterface $router) {}

    public function onAuthenticationSuccess(Request $request, TokenInterface $token): RedirectResponse
    {
        $roles = $token->getRoleNames();

        if (
            in_array(UserRole::ADMIN->value, $roles, true)
            || in_array(UserRole::EMPLOYE->value, $roles, true)
        ) {
            // Redirection pour les gestionnaires
            return new RedirectResponse($this->router->generate('app_user_index'));
        }

        if (in_array(UserRole::CLIENT->value, $roles, true)) {
            // Redirection pour les clients
            return new RedirectResponse($this->router->generate('app_client_index'));
        }

        // Redirection par défaut si pas de rôle
        return new RedirectResponse($this->router->generate('home'));
    }
}
