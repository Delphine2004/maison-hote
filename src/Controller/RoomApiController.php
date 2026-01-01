<?php

namespace App\Controller;

use App\Entity\Room;
use App\Repository\RoomRepository;

use DateTimeImmutable;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;

final class RoomApiController extends AbstractController
{

    #[Route('/room/api', name: 'app_room_api', methods: ['GET'])]
    public function index(
        Request $request,
        RoomRepository $roomRepository
    ): JsonResponse {

        // Récupération des paramètres
        $start = $request->query->get('start');
        $end   = $request->query->get('end');

        // Validation basique
        if (!$start || !$end) {
            throw new BadRequestHttpException('Les paramètres start et end sont obligatoires');
        }

        try {
            $startDate = new DateTimeImmutable($start);
            $endDate   = new DateTimeImmutable($end);
        } catch (\Exception) {
            throw new BadRequestHttpException('Format de date invalide (ISO 8601 recommandé)');
        }

        if ($startDate >= $endDate) {
            throw new BadRequestHttpException('La date de début doit être antérieure à la date de fin');
        }



        $rooms = $roomRepository->findRoomsByPeriod($startDate, $endDate);

        // Réponse JSON
        return $this->json([
            'status' => 'success',
            'count'  => count($rooms),
            'rooms' => $rooms
        ]);
    }
}
