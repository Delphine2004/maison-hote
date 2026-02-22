<?php

namespace App\Controller;

use App\Entity\User;
use App\Repository\UserRepository;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\HttpFoundation\JsonResponse;

final class UserApiController extends AbstractController
{

    #[Route('/user/api', name: 'app_user_api', methods: ['GET'])]
    public function index(
        Request $request,
        UserRepository $userRepository
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

        $users = $userRepository->findUserByFilters($criteria);

        // Transformation en tableau JSON des propriétés
        $data = array_map(function (User $user) {
            return [
                'id' => $user->getId(),
                'lastName' => $user->getLastName(),
                'firstName' => $user->getFirstName(),


            ];
        }, $users);

        // Réponse JSON
        return $this->json([
            'status' => 'success',
            'count'  => count($data),
            'users' => $data
        ]);
    }
}
