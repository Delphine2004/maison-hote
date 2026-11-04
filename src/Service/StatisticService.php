<?php

namespace App\Service;

use App\Repository\BookingRepository;
use App\Repository\RoomRepository;

use Exception;


final class StatisticService
{

    public function __construct(
        private BookingRepository $bookingRepository,
        private RoomRepository $roomRepository
    ) {}


    // TO = (total chambres occupées / total chambre dispo) x 100 - A TESTER
    /* public function getTOFByPeriod(
        DateTimeImmutable $startDate,
        DateTimeImmutable $endDate
    ): float {

        // Obtenir le nombre total de chambre
        $totalRooms = (int) $this->getEntityManager()
            ->createQuery('SELECT COUNT(r.id) FROM App\Entity\Room r')
            ->getSingleScalarResult();

        // Calculer la période - nombre de nuit selectionné
        $start = (clone $startDate)->setTime(0, 0, 0);
        $end = (clone $endDate)->setTime(23, 59, 59);

        $interval = $start->diff($end);
        $numberOfDays = $interval->days ?: 1;
        $totalAvailableRoomNights = $totalRooms * $numberOfDays;

        // Compter le nombre de chambres occupées (nuits réservées) sur la période
        $qb = $this->createQueryBuilder('b')
            ->select('SUM(DATE_DIFF(b.endingDate, B.startingDate))')
            ->where('b.startingDate < :end')
            ->andWhere('b.endingDate > :start')
            ->andWhere('b.status != :status')
            ->setParameter('start', $start)
            ->setParameter('end', $end)
            ->setParameter('status', BookingStatus::CANCELLED->value);

        $occupiedRoomNights = (int) $qb->getQuery()->getSingleScalarResult();

        // Calcul du pourcentage
        $occupancyRate = ($occupiedRoomNights / $totalAvailableRoomNights) * 100;
        return round($occupancyRate, 2);
    }*/

    // Le Prix Moyen (ou ARR / ADR) = CA / total chambres occupées



    // Le RevPAR (Revenue Per Available Room) = ca / total chambre dispo


    // La Durée Moyenne de Séjour (DMS) = nb de nuitées / nb total de réservation


}
