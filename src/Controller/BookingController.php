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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/booking')]
final class BookingController extends AbstractController
{

    #[Route('', name: 'app_booking_index', methods: ['GET'])]
    public function index(
        BookingRepository $bookingRepository
    ): Response {
        return $this->render('booking/index.html.twig', [
            'todayBookings' => $bookingRepository->findTodayBookings()
        ]);
    }

    #[Route('/search', name: 'app_search_booking', methods: ['GET', 'POST'])]
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
    public function newByStaff(
        int $roomId,
        int $userId,
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

        $session->remove('period');


        return $this->redirectToRoute('app_booking_show', [
            'id' => $booking->getId()
        ]);
    }

    #[Route('/new/{roomId}', name: 'app_booking_new_by_client', methods: ['GET', 'POST'])]
    public function newByClient(
        int $roomId,
        Request $request,
        EntityManagerInterface $entityManager,
        SessionInterface $session
    ): Response {

        $user = $this->getUser();

        // si pas authentifié
        if (!$user) {
            // ----> redirection vers page connexion
            return $this->redirectToRoute('app_login', [], Response::HTTP_SEE_OTHER);
        }

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

        // Récupérations
        $room = $entityManager->getRepository(Room::class)->find($roomId);


        if (!$room) {
            throw $this->createNotFoundException();
        }


        // traitement réservation
        $form = $this->createForm(BookingType::class); // modifier le type pour uniquement nom à rechercher ou à ajouter
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

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
            return $this->redirectToRoute('app_booking_show', [
                'id' => $booking->getId()
            ]);
        }


        return $this->render('booking/new_by_client.html.twig', [
            'form' => $form->createView(),
        ]);
    }


    #[Route('/statistics', name: 'app_booking_statistics')]
    public function renderStats(): Response
    {
        return $this->render('booking/statistics.html.twig');
    }

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(
        Booking $booking
    ): Response {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/cancel', name: 'app_booking_cancel', methods: ['POST'])]
    public function cancel(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$this->isCsrfTokenValid('cancel' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $booking->setStatus(BookingStatus::CANCELLED);
        $booking->setUpdatedBy($this->getUser());

        $entityManager->flush();

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/checkin', name: 'app_booking_checkin', methods: ['POST'])]
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

        return $this->redirectToRoute('app_user_dashboard', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/checkout', name: 'app_booking_checkout', methods: ['POST'])]
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

        return $this->redirectToRoute('app_user_dashboard', [], Response::HTTP_SEE_OTHER);
    }
}
