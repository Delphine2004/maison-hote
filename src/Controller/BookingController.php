<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Entity\Room;
use App\Entity\User;

use App\Enum\UserRole;
use App\Enum\BookingStatus;

use App\Form\BookingType;
use App\Form\SearchBookingType;


use App\Repository\BookingRepository;


use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bridge\Twig\Mime\TemplatedEmail;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\FormError;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Mailer\MailerInterface;
use Symfony\Component\Mime\Address;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/booking')]
final class BookingController extends AbstractController
{

    #[Route('', name: 'app_booking_index', methods: ['GET'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function index(
        BookingRepository $bookingRepository
    ): Response {
        return $this->render('booking/index.html.twig', [
            'todayBookings' => $bookingRepository->findTodayBookings()
        ]);
    }

    #[Route('/blocked', name: 'app_blocked_list', methods: ['GET'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function renderBlockedList(
        BookingRepository $bookingRepository
    ): Response {
        return $this->render('booking/blocked_list.html.twig', [
            'outOfOrders' => $bookingRepository->findOutOfOrder()
        ]);
    }

    #[Route('/{id}/unblock', name: 'app_unblock', methods: ['POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function unblockRoom(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {
        if (!$this->isCsrfTokenValid('unblock' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $entityManager->remove($booking);

        $entityManager->flush();

        $this->addFlash('success', 'Chambre débloquée');
        return $this->redirectToRoute('app_blocked_list', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/block', name: 'app_block_room', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function blockRoom(
        Request $request,
        EntityManagerInterface $entityManager,
        BookingRepository $bookingRepository,
    ): Response {
        $booking = new Booking();
        $booking->setStatus(BookingStatus::OUTOFORDER);
        $form = $this->createForm(BookingType::class, $booking, ['mode' => 'blockRoom']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $conflicts = $bookingRepository->findOverlappingBookings(
                $booking->getRoom(),
                $booking->getStartingDate(),
                $booking->getEndingDate()
            );

            if ($conflicts) {
                $form->addError(
                    new FormError(
                        'Cette chambre possède déjà une réservation sur cette période.'
                    )
                );

                return $this->render('booking/block_room.html.twig', [
                    'booking' => $booking,
                    'form' => $form->createView(),
                ]);
            }


            $entityManager->persist($booking);
            $entityManager->flush();
            $this->addFlash('success', 'Chambre bloquée avec succés.');
            return $this->redirectToRoute('app_blocked_list', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/block_room.html.twig', [
            'booking' => $booking,
            'form' => $form->createView(),
        ]);
    }

    #[Route('/search', name: 'app_search_booking', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function renderSearch(
        Request $request,
        BookingRepository $bookingRepository
    ): Response {

        $form = $this->createForm(SearchBookingType::class);
        $form->handleRequest($request);

        $bookings = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $bookings = $bookingRepository->findBookingsByFilters(
                $data
            );
        }

        return $this->render('booking/search.html.twig', [
            'form' => $form->createView(),
            'bookings' => $bookings
        ]);
    }

    #[Route('/newByStaff/{roomId}/{userId}', name: 'app_booking_new_by_staff', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function newByStaff(
        int $roomId,
        int $userId,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
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

        // Récupérations
        $room = $entityManager->getRepository(Room::class)->find($roomId);
        $user = $entityManager->getRepository(User::class)->find($userId);

        if (!$room || !$user) {
            throw $this->createNotFoundException();
        }

        // traitement réservation
        $booking = new Booking();
        $booking->setRoom($room);
        $booking->setUser($user);
        $booking->setStatus(BookingStatus::CONFIRMED);
        $booking->setStartingDate($period->getStartingDate());
        $booking->setEndingDate($period->getEndingDate());

        $booking->calculateTotalAmount();

        $entityManager->persist($booking);
        $entityManager->flush();

        // nettoyage session
        $session->remove('period');

        // Envoi de l'email de confirmation
        $email = (new TemplatedEmail())
            ->from(new Address('dfumex2004@gmail.com', 'Les parenthèses dorées'))
            ->to((string) $user->getEmail())
            ->subject('Confirmation de réservation')
            ->htmlTemplate('booking/email_confirmation.html.twig')
            ->context([
                'user' => $user,
                'booking' => $booking
            ]);

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
        }
        $this->addFlash('success', 'Réservation confirmée.');
        return $this->redirectToRoute('app_booking_show', [
            'id' => $booking->getId()
        ]);
    }

    #[Route('/new/{roomId}', name: 'app_booking_new_by_client', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function newByClient(
        int $roomId,
        Request $request,
        EntityManagerInterface $entityManager,
        MailerInterface $mailer,
        SessionInterface $session
    ): Response {

        // Récupération utilisateur connecté
        $user = $this->getUser();

        // Vérification si authentifié
        if (!$user) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

        // Récupération période
        $period = $session->get('period');

        // Vérification si données
        if (
            !$period ||
            !$period->getStartingDate() ||
            !$period->getEndingDate()
        ) {
            $session->remove('period');
            $this->addFlash('sucess', 'Un erreur s\'est produite.');
            return $this->redirectToRoute('app_search_room');
        }

        // Récupération chambre
        $room = $entityManager->getRepository(Room::class)->find($roomId);

        // Vérification
        if (!$room) {
            throw $this->createNotFoundException();
        }

        // Création de l'objet réservation
        $booking = new Booking();

        // Traitement réservation
        $form = $this->createForm(BookingType::class, $booking, ['mode' => 'createBooking']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $booking->setRoom($room);
            $booking->setUser($user);
            $booking->setStatus(BookingStatus::CONFIRMED);
            $booking->setStartingDate($period->getStartingDate());
            $booking->setEndingDate($period->getEndingDate());

            $booking->calculateTotalAmount();


            $entityManager->persist($booking);
            $entityManager->flush();

            // nettoyage session
            $session->remove('period');

            $user = $booking->getUser();

            // Envoi de l'email de confirmation
            $email = (new TemplatedEmail())
                ->from(new Address('dfumex2004@gmail.com', 'Les parenthèses dorées'))
                ->to((string) $user->getEmail())
                ->subject('Confirmation de réservation')
                ->htmlTemplate('booking/email_confirmation.html.twig')
                ->context([
                    'user' => $user,
                    'booking' => $booking
                ]);

            try {
                $mailer->send($email);
            } catch (\Exception $e) {
            }
            $this->addFlash('success', 'Réservation confirmée.');
            return $this->redirectToRoute('app_booking_show', [
                'id' => $booking->getId()
            ]);
        }


        return $this->render('booking/new_by_client.html.twig', [
            'form' => $form->createView(),
        ]);
    }

    #[Route('/statistics', name: 'app_booking_statistics')]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function renderStats(): Response
    {
        return $this->render('booking/statistics.html.twig');
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function show(
        Booking $booking
    ): Response {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_booking_edit', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function edit(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();
            $this->addFlash('success', 'Réservation modifiée avec succés.');
            return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/edit.html.twig', [
            'booking' => $booking,
            'form' => $form,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_booking_cancel', methods: ['POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function cancel(
        Request $request,
        Booking $booking,
        MailerInterface $mailer,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('cancel' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $userConnected = $this->getUser();

        // Vérification que l'utilisateur est connecté
        if (!$userConnected) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_booking_show', [
                'id' => $booking->getId()
            ]);
        }

        // Vérification que l'utilisateur connécté est bien celui qui detient la réservation ou est autorisé
        if ($booking->getUser() !== $userConnected && !$this->isGranted('ROLE_EMPLOYE')) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Vérification que la réservation n'est pas "presente"
        if ($booking->getStatus() === BookingStatus::IN->value) {
            $this->addFlash('sucess', 'Vous n\'êtes pas autorisé à annuler la réservation.');
            return $this->redirectToRoute('app_booking_show', [
                'id' => $booking->getId()
            ]);
        }


        $booking->setStatus(BookingStatus::CANCELLED);
        $booking->setUpdatedBy($userConnected);

        $entityManager->flush();

        $user = $booking->getUser();

        // Envoi de l'email de confirmation
        $email = (new TemplatedEmail())
            ->from(new Address('dfumex2004@gmail.com', 'Les parenthèses dorées'))
            ->to((string) $user->getEmail())
            ->subject('Annulation de réservation')
            ->htmlTemplate('booking/email_cancellation.html.twig')
            ->context([
                'user' => $user,
                'booking' => $booking
            ]);

        try {
            $mailer->send($email);
        } catch (\Exception $e) {
        }
        $this->addFlash('success', 'Réservation annulée.');
        return $this->redirectToRoute('app_booking_show', ['id' => $booking->getId()]);
    }

    #[Route('/{id}/checkin', name: 'app_booking_checkin', methods: ['POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function checkin(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$this->isCsrfTokenValid('checkin' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $booking->setStatus(BookingStatus::IN);
        $booking->setUpdatedBy($this->getUser());

        $entityManager->flush();

        $this->addFlash('success', 'Arrivée confirmée.');
        return $this->redirectToRoute('app_user_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/checkout', name: 'app_booking_checkout', methods: ['POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function checkout(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$this->isCsrfTokenValid('checkout' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $booking->setStatus(BookingStatus::FINALIZED);
        $booking->setUpdatedBy($this->getUser());

        $entityManager->flush();

        $this->addFlash('success', 'Départ confirmé.');
        return $this->redirectToRoute('app_user_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
