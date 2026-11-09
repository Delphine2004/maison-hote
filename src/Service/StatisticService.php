<?php

namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\RoomRepository;

use DateTimeImmutable;
use Exception;


final class StatisticService
{

    public function __construct(
        private BookingRepository $bookingRepository,
        private RoomRepository $roomRepository
    ) {}


    // TO = (total chambres occupées / total chambre dispo(bloquée ou non???)) x 100 - A TESTER
    public function getOR(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {

        // Obtenir le nombre total de chambre
        $totalRooms = $this->bookingRepository->sumAvailableRoomsByPeriod($startDate, $endDate);

        // Calculer la période - nombre de nuit selectionné
        $interval = $startDate->diff($endDate);
        $numberOfDays = $interval->days ?: 1;
        $totalAvailableRoomNights = $totalRooms * $numberOfDays;

        // Récupérer le nombre de chambre occupée
        $occupiedRoomNights = $this->bookingRepository->sumRoomWithBookingByPeriod($startDate, $endDate);

        // Calcul du pourcentage
        $occupancyRate = ($occupiedRoomNights / $totalAvailableRoomNights) * 100;

        return round($occupancyRate, 2);
    }

    // Le Prix Moyen (ou ARR / ADR) = CA / total chambres occupées
    public function getARR(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {
        // obtenir le CA
        $ca = $this->bookingRepository->sumSalesRevenueByPeriod($startDate, $endDate);

        // Obtenir le nombre total de chambre
        $totalRooms = $this->bookingRepository->sumAvailableRoomsByPeriod($startDate, $endDate);

        //calcul
        return round($ca / $totalRooms);
    }


    // Le RevPAR (Revenue Per Available Room) = ca / total chambre dispo (bloquée ou non???)


    // La Durée Moyenne de Séjour (DMS) = nb de nuitées / nb total de réservation


}
