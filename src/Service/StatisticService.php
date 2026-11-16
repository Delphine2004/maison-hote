<?php

namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\RoomRepository;

use DateTimeImmutable;


final class StatisticService
{

    public function __construct(
        private BookingRepository $bookingRepository,
        private RoomRepository $roomRepository
    ) {}


    // TO = (total chambres occupées / total chambre dispo) x 100 - A TESTER
    public function getOR(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {

        // Obtenir le nombre total de chambre
        $totalRooms = $this->roomRepository->countAvailableRoomsByPeriod($startDate, $endDate);

        // Calculer la période - nombre de nuit selectionné
        $interval = $startDate->diff($endDate);
        $numberOfDays = $interval->days ?: 1;
        $totalAvailableRoomNights = $totalRooms * $numberOfDays;

        // Récupérer le nombre de chambre occupée
        $occupiedRoomNights = $this->bookingRepository->countRoomWithBookingByPeriod($startDate, $endDate);

        // éviter la division par zéro
        if ($totalAvailableRoomNights === 0) {
            return 0;
        }

        // Calcul du pourcentage
        $occupancyRate = ($occupiedRoomNights / $totalAvailableRoomNights) * 100;

        return (float)round($occupancyRate, 2);
    }

    // Le Prix Moyen (ou ARR / ADR) = CA / total chambres occupées
    public function getARR(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {
        // obtenir le CA
        $ca = $this->bookingRepository->sumSalesRevenueByPeriod($startDate, $endDate);

        // Obtenir le nombre total de chambre occupées
        $totalOccupiedRooms = $this->bookingRepository->countRoomWithBookingByPeriod($startDate, $endDate);

        // éviter la division par zéro
        if ($totalOccupiedRooms === 0) {
            return 0;
        }

        //calcul
        return (float)round($ca / $totalOccupiedRooms);
    }


    // Le RevPAR (Revenue Per Available Room) = ca / total chambre dispo
    public function getREVPAR(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {
        // obtenir le CA
        $ca = $this->bookingRepository->sumSalesRevenueByPeriod($startDate, $endDate);

        // Obtenir le nombre total de chambre
        $totalAvailableRooms = $this->roomRepository->countAvailableRoomsByPeriod($startDate, $endDate);

        // éviter la division par zéro
        if ($totalAvailableRooms === 0) {
            return 0;
        }

        //calcul
        return (float) round($ca / $totalAvailableRooms);
    }


    // La Durée Moyenne de Séjour (DMS) = nb de nuitées / nb total de réservation
    public function getAverageStay(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): int {
        // obtenir le nombre de nuitée réservée
        $totalBookedNights = $this->bookingRepository->sumNumberOfNightByPeriod($startDate, $endDate);

        // obtenir le nombre total de réservation
        $totalBookings = $this->bookingRepository->sumNumberOfBookingByPeriod($startDate, $endDate);

        // éviter la division par zéro
        if ($totalBookings === 0) {
            return 0;
        }

        // calcul
        return (int) round($totalBookedNights / $totalBookings);
    }
}
