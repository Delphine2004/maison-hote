<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Enum\UserRole;
use App\Enum\BookingStatus;
use App\Form\BookingType;
use App\Repository\BookingRepository;
use DateTimeImmutable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[Route('/booking')]
final class BookingController extends AbstractController
{

    #[Route('', name: 'app_booking_index', methods: ['GET'])]
    public function index(
        BookingRepository $bookingRepository
    ): Response {
        return $this->render('booking/index.html.twig', [
            'todayBookings' => $bookingRepository->findBookingsByFilters([new DateTimeImmutable('now')])
        ]);
    }

    #[Route('/search', name: 'app_booking_search', methods: ['GET'])]
    public function renderSearch(
        Request $request,
        BookingRepository $bookingRepository
    ): Response {

        $criteria = array_filter($request->query->all());
        $bookings = [];

        if (!empty($criteria)) {
            $bookings = $bookingRepository->findBookingsByFilters($criteria);
        }

        return $this->render('booking/search.html.twig', [
            'bookings' => $bookings
        ]);
    }

    #[Route('/new', name: 'app_booking_new', methods: ['GET', 'POST'])]
    public function new(
        Request $request,
        EntityManagerInterface $entityManager
    ): Response {
        $booking = new Booking();
        $form = $this->createForm(BookingType::class, $booking);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($booking);
            $entityManager->flush();

            return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('booking/new.html.twig', [
            'booking' => $booking,
            'form' => $form,
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
