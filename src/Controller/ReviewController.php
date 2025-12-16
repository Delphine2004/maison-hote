<?php

namespace App\Controller;

use App\Enum\UserRole;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

#[IsGranted(UserRole::EMPLOYE->value)]
#[Route('/review')]
final class ReviewController extends AbstractController
{
    #[Route('', name: 'app_review_index')]
    public function index(): Response
    {
        return $this->render('review/index.html.twig', [
            'controller_name' => 'ReviewController',
        ]);
    }
}
