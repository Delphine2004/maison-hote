<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\UserType;

use App\Repository\UserRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/client')]
final class ClientController extends AbstractController
{

    #[Route('/search', name: 'app_client_index', methods: ['GET'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function index(
        Request $request,
        UserRepository $userRepository
    ): Response {
        $criteria = array_filter($request->query->all());

        $users = [];

        if (!empty($criteria)) {
            $users = $userRepository->findUserByFilters($criteria);
        }

        return $this->render('user/index.html.twig', [
            'users' => $users,
        ]);
    }

    #[Route('/dashboard/{id}', name: 'app_client_dashboard', methods: ['GET'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function renderClientDashboard(User $user): Response
    {
        return $this->render('user/dashboard_client.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function showClient(User $user): Response
    {
        return $this->render('user/show_client.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(UserType::class, $user, ['mode' => 'updateClient']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
