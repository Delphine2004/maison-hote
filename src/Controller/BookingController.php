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

#[IsGranted('IS_AUTHENTICATED_FULLY')]
#[Route('/booking')]
final class BookingController extends AbstractController
{
    #[Route('', name: 'app_booking_index', methods: ['GET'])]
    public function index(
        BookingRepository $bookingRepository
    ): Response {
        return $this->render('booking/index.html.twig', [
            'bookings' => $bookingRepository->findBookingsByFilters([new DateTimeImmutable('now')]),
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

    #[Route('/{id}', name: 'app_booking_show', methods: ['GET'])]
    public function show(
        Booking $booking
    ): Response {
        return $this->render('booking/show.html.twig', [
            'booking' => $booking,
        ]);
    }

    #[Route('/{id}/cancelbystaff', name: 'app_booking_cancel_staff', methods: ['POST'])]
    public function cancelByStaff(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$this->isCsrfTokenValid('cancelbystaff' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $booking->setStatus(BookingStatus::CANCELLED);
        $booking->setUpdatedByUser($this->getUser());

        $entityManager->flush();

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/cancel', name: 'app_booking_cancel_client', methods: ['POST'])]
    public function cancel(
        Request $request,
        Booking $booking,
        EntityManagerInterface $entityManager
    ): Response {

        if (!$this->isCsrfTokenValid('cancel' . $booking->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $booking->setStatus(BookingStatus::CANCELLED);
        $booking->setUpdatedByClient($this->getUser());

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
        $booking->setUpdatedByUser($this->getUser());

        $entityManager->flush();

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
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
        $booking->setUpdatedByUser($this->getUser());

        $entityManager->flush();

        return $this->redirectToRoute('app_booking_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/statistics', name: 'app_booking_statistics')]
    public function renderSearch(): Response
    {
        return $this->render('booking/statistics.html.twig');
    }
}
