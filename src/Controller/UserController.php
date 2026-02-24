<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\UserRole;
use App\Form\UserType;
use App\Repository\BookingRepository;
use App\Repository\UserRepository;

use DateTimeImmutable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/user')]
#[IsGranted(UserRole::EMPLOYE->value)]
final class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
    public function renderUserDashboard(
        BookingRepository $bookingRepository
    ): Response {

        // Récupération des séjours en cours
        $inHouse =  $bookingRepository->findInHouse();

        $today = new DateTimeImmutable('today');

        // Récupération des départs
        $checkOut = $bookingRepository->findCheckOutsForDay($today);

        // Récupération des arrivées
        $checkIn = $bookingRepository->findCheckInsForDay($today);

        return $this->render('booking/index.html.twig', [
            'inhouses' => $inHouse,
            'checkouts' => $checkOut,
            'checkins' => $checkIn,
        ]);
    }

    #[Route('/search', name: 'app_user_index', methods: ['GET'])]
    public function index(
        UserRepository $userRepository
    ): Response {
        return $this->render('user/index.html.twig', [
            'users' => $userRepository->findUserByFilters([]),
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function showUser(
        User $user
    ): Response {
        return $this->render('user/show_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager,
        UserPasswordHasherInterface $passwordHasher
    ): Response {
        $user = new User();
        $user->setRoles([UserRole::CLIENT]);
        $form = $this->createForm(UserType::class, $user, ['mode' => 'createClient']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $plainPassword = $form->get('password')->getData();
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(UserType::class, $user, ['mode' => 'updateUser']);
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
