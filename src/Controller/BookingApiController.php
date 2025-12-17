<?php

namespace App\Controller;

use App\Entity\Booking;
use App\Repository\BookingRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class BookingApiController extends AbstractController
{

    #[Route('/booking/api', name: 'app_booking_api', methods: ['GET'])]
    public function index(
        Request $request,
        BookingRepository $bookingRepository
    ): JsonResponse {

        // Récupération brute des query params
        $criteria = $request->query->all();

        // Validation légère
        if (isset($criteria['id']) && !ctype_digit($criteria['id'])) {
            return $this->json([
                'status' => 'error',
                'message' => 'ID invalide'
            ], 400);
        }

        $bookings = $bookingRepository->findBookingsByFilters($criteria);

        // Transformation en tableau JSON des propriétés
        $data = array_map(function (Booking $booking) {

            $client = $booking->getClient();
            $room   = $booking->getRoom();

            return [
                'id' => $booking->getId(),
                'startingDate' => $booking->getStartingDate()->format('d/m/Y'),
                'endingDate' => $booking->getEndingDate()->format('d/m/Y'),
                'totalAmount' => $booking->getTotalAmount(),
                'client' => $client ? [
                    'id' => $client->getId(),
                    'lastName' => $client->getLastName(),
                    'firstName' => $client->getFirstName(),
                ] : null,
                'room' => $room ? [
                    'id' => $room->getId(),
                    'name' => $room->getName(),
                    'number' => $room->getNumber(),
                ] : null,
            ];
        }, $bookings);

        // Réponse JSON
        return $this->json([
            'status' => 'success',
            'count'  => count($data),
            'bookings' => $data
        ]);
    }
}
