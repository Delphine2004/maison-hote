<?php

namespace App\Controller;

use App\Entity\User;
use App\Entity\Room;

use App\Enum\BookingStatus;
use App\Enum\UserRole;

use App\Form\UserType;
use App\Form\SearchClientType;

use App\Repository\UserRepository;
use App\Repository\BookingRepository;


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

    private UserPasswordHasherInterface $hasher;

    public function __construct(UserPasswordHasherInterface $hasher)
    {
        $this->hasher = $hasher;
    }

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
            $this->addFlash('sucess', 'Un erreur s\'est produite.');
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

            $this->addFlash('success', 'Utilisateur ajouté avec succés.');
            return $this->redirectToRoute('app_client_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
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

        $bookings = $bookingRepository->findUpcomingBookingsByClient($user->getId());

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
        $bookings = $bookingRepository->findPastBookingsByClient($user->getId());

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

            $this->addFlash('success', 'Modification(s) réalisée(s) avec succés.');
            return $this->redirectToRoute('app_client_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/{id}/delete', name: 'app_client_delete', methods: ['POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function delete(
        Request $request,
        User $user,
        BookingRepository $bookingRepository,
        EntityManagerInterface $entityManager,
        TokenStorageInterface $tokenStorage
    ): Response {
        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid(
            'delete' . $user->getId(),
            $request->request->get('_token')
        )) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $userConnected = $this->getUser();

        // Vérification que l'utilisateur est connecté
        if (!$userConnected) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Empêche un client de supprimer un autre compte
        if ($user !== $userConnected && !$this->isGranted('ROLE_EMPLOYE')) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Vérification que l'utilisateur n'a pas une réservation en cours
        if ($bookingRepository->hasCurrentReservation($user)) {
            $this->addFlash(
                'sucess',
                'Vous ne pouvez pas supprimer votre compte pendant un séjour en cours.'
            );
            return $this->redirectToRoute('app_client_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        // Récupérer et annuler les réservations à venir
        try {
            $userId = $user->getId();
            $bookings = $bookingRepository->findUpcomingBookingsByClient($userId);

            foreach ($bookings as $booking) {
                $booking->setStatus(BookingStatus::CANCELLED);
            }
        } catch (\Exception $e) {
            $this->addFlash(
                'sucess',
                'Une erreur est survenue.'
            );

            $this->addFlash('sucess', 'Une erreur est survenue.');
            return $this->redirectToRoute('app_client_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        $user->setFirstName('anonyme');
        $user->setLastName('anonyme');
        $user->setLogin('anonyme' . $user->getId());
        $user->setEmail('anonyme_' . $user->getId() . '@example.com');
        $hashedPassword = $this->hasher->hashPassword($user, 'Anonymised12*');
        $user->setPassword($hashedPassword);
        $user->setRoles([UserRole::ANONYMIZED]);
        $user->setPhone('N-A');
        $user->setAddress('N-A');
        $user->setZipCode('N-A');
        $user->setCity('N-A');

        $entityManager->flush();


        if ($this->isGranted('ROLE_EMPLOYE')) {
            $this->addFlash('success', 'Client supprimé.');
            return $this->redirectToRoute('app_search_client', [], Response::HTTP_SEE_OTHER);
        } else {
            // Déconnexion
            $tokenStorage->setToken(null);
            $request->getSession()->invalidate();
            return $this->redirectToRoute('app_home', [], Response::HTTP_SEE_OTHER);
        };
    }
}
