<?php

namespace App\Controller;

use App\Entity\User;
use App\Enum\BookingStatus;
use App\Enum\UserRole;
use App\Form\UserType;
use App\Repository\BookingRepository;

use DateTimeImmutable;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;

//#[IsGranted(UserRole::EMPLOYE->value)]
#[Route('/user')]
final class UserController extends AbstractController
{
    #[Route('', name: 'app_user_index', methods: ['GET'])]
    public function index(
        BookingRepository $bookingRepository
    ): Response {

        // Récupération des séjours en cours
        $inHouse =  $bookingRepository->findInHouse();

        $today = new DateTimeImmutable('today');

        // Récupération des départs
        $checkOut = $bookingRepository->findCheckOutsForDay($today);

        // Récupération des arrivées
        $checkIn = $bookingRepository->findCheckInsForDay($today);

        return $this->render('user/index.html.twig', [
            'inhouses' => $inHouse,
            'checkouts' => $checkOut,
            'checkins' => $checkIn,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function show(User $user): Response
    {
        return $this->render('user/show.html.twig', [
            'user' => $user,
        ]);
    }

    //------------------------------

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, User $user, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(UserType::class, $user);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_user_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form,
        ]);
    }
}
