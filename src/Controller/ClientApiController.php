<?php

namespace App\Controller;

use App\Entity\Client;
use App\Repository\ClientRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class ClientApiController extends AbstractController
{

    #[Route('/client/api', name: 'app_client_api', methods: ['GET'])]
    public function index(
        Request $request,
        ClientRepository $clientRepository
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

        $clients = $clientRepository->findClientByFilters($criteria);

        // Transformation en tableau JSON des propriétés
        $data = array_map(function (Client $client) {
            return [
                'id' => $client->getId(),
                'lastName' => $client->getLastName(),
                'firstName' => $client->getFirstName(),


            ];
        }, $clients);

        // Réponse JSON
        return $this->json([
            'status' => 'success',
            'count'  => count($data),
            'clients' => $data
        ]);
    }
}
