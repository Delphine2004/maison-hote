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
        $user = $token->getUser();

        if (!$user instanceof \App\Entity\User) {
            throw new \LogicException('User is not an instance of App\Entity\User');
        }

        if (
            in_array(UserRole::ADMIN->value, $roles, true)
            || in_array(UserRole::EMPLOYE->value, $roles, true)
        ) {
            return new RedirectResponse(
                $this->router->generate('app_user_dashboard')
            );
        }

        return new RedirectResponse(
            $this->router->generate('app_client_dashboard', [
                'id' => $user->getId()
            ])
        );
    }
}
