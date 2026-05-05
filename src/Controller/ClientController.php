<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Room;

use App\Enum\UserRole;

use App\Form\UserType;
use App\Form\SearchClientType;

use App\Repository\UserRepository;
use App\Repository\BookingRepository;

use DateTimeImmutable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Http\Attribute\IsGranted;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;

#[Route('/client')]
final class ClientController extends AbstractController
{

    #[Route('/search', name: 'app_search_client', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function index(
        Request $request,
        UserRepository $userRepository
    ): Response {
        $form = $this->createForm(SearchClientType::class);
        $form->handleRequest($request);

        $users = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $users = $userRepository->findClientByFilters(
                $data
            );
        }

        return $this->render('user/index.html.twig', [
            'form' => $form->createView(),
            'users' => $users
        ]);
    }

    #[Route('/addClient/{roomId}', name: 'app_search_client_to_booking', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function addClientToBooking(
        int $roomId,
        Request $request,
        UserRepository $userRepository,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {

        $period = $session->get('period');

        // sécurité
        if (
            !$period ||
            !$period->getStartingDate() ||
            !$period->getEndingDate()
        ) {
            $session->remove('period');
            return $this->redirectToRoute('app_search_room');
        }

        // récupérer la room depuis la BDD
        $room = $entityManager->getRepository(Room::class)->find($roomId);

        if (!$room) {
            throw $this->createNotFoundException('Chambre introuvable.');
        }


        $form = $this->createForm(SearchClientType::class);
        $form->handleRequest($request);

        $users = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $users = $userRepository->findClientByFilters(
                $data
            );

            $session->set('user', $data);
        }

        return $this->render('user/search_client.html.twig', [
            'form' => $form->createView(),
            'period' => $period,
            'room' => $room,
            'users' => $users
        ]);
    }

    #[Route('/new', name: 'app_client_new', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
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
            $plainPassword = "PasswordToChange123";
            $hashedPassword = $passwordHasher->hashPassword($user, $plainPassword);

            $user->setPassword($hashedPassword);

            $entityManager->persist($user);
            $entityManager->flush();

            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/new_client.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}', name: 'app_client_show', methods: ['GET'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function showClient(
        User $user,
        BookingRepository $bookingRepository
    ): Response {

        $today = new DateTimeImmutable('today');

        $bookings = $bookingRepository->findUpcomingBookingsByClient($user->getId(), $today);

        return $this->render('user/show_client.html.twig', [
            'user' => $user,
            'bookings' => $bookings
        ]);
    }

    #[Route('/{id}/history', name: 'app_client_booking', methods: ['GET'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function renderClientHistory(
        User $user,
        BookingRepository $bookingRepository
    ): Response {
        $today = new DateTimeImmutable('today');
        $bookings = $bookingRepository->findPastBookingsByClient($user->getId(), $today);

        return $this->render('user/user_history.html.twig', [
            'user' => $user,
            'bookings' => $bookings
        ]);
    }

    #[Route('/{id}/edit', name: 'app_client_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {

        if ($this->getUser() === $user) {
            $mode = 'updateClient';
        } else {
            $mode = 'updateClientByStaff';
        }

        $form = $this->createForm(UserType::class, $user, ['mode' => $mode]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_client_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_client_delete', methods: ['POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function delete(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ): Response {
        if ($this->isCsrfTokenValid('delete' . $user->getId(), $request->getPayload()->getString('_token'))) {

            // Si l'utilisateur supprime son propre compte
            if ($this->getUser() === $user) {
                $tokenStorage->setToken(null);
                $request->getSession()->invalidate();
            }

            $entityManager->remove($user);
            $entityManager->flush();
        }

        if ($this->isGranted('ROLE_EMPLOYE')) {
            return $this->redirectToRoute('app_search_client', [], Response::HTTP_SEE_OTHER);
        } else {
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        };
    }
}
