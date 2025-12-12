<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\UserType;
use App\Repository\UserRepository;
use App\Repository\RoomRepository;
use App\Repository\PeriodRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

#[Route('/admin')]
final class AdminController extends AbstractController
{
    #[Route('', name: 'app_settings_index', methods: ['GET'])]
    public function index(
        PeriodRepository $periodRepository,
        RoomRepository $roomRepository,
        UserRepository $userRepository
    ): Response {

        // Récupération des périodes
        $periods = $periodRepository->findActivePeriod();

        // Récupération des chambres
        $rooms = $roomRepository->findAllRoom();

        // Récupération des utilisateurs
        $users = $userRepository->findUsersWithRole(UserRole::EMPLOYE->value);

        return $this->render('admin/settings.html.twig', [
            'periods' => $periods,
            'rooms' => $rooms,
            'users' => $users,
        ]);
    }

    #[Route('/new', name: 'app_user_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $form = $this->createForm(UserType::class, $user, ['mode' => 'create']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $role = $form->get('role')->getData();

            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);
            $user->setRoles($role ? [$role->value] : []);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_settings_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_user_delete', methods: ['POST'])]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($user);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
    }
}
