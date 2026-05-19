<?php

namespace App\Controller;

use App\Entity\User;
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


#[Route('/user')]
#[IsGranted(UserRole::EMPLOYE->value)]
final class UserController extends AbstractController
{
    #[Route('/dashboard', name: 'app_user_dashboard', methods: ['GET'])]
    public function renderUserDashboard(
        BookingRepository $bookingRepository
    ): Response {

        // Récupération des séjours en cours
        $booking =  $bookingRepository->findInHouse();

        $today = new DateTimeImmutable('today');

        // Récupération des départs
        $checkOut = $bookingRepository->findCheckOutsForDay($today);

        // Récupération des arrivées
        $checkIn = $bookingRepository->findCheckInsForDay($today);

        return $this->render('booking/dashboard.html.twig', [
            'bookings' => $booking,
            'checkouts' => $checkOut,
            'checkins' => $checkIn,
        ]);
    }

    #[Route('/{id}', name: 'app_user_show', methods: ['GET'])]
    public function showUser(
        User $user
    ): Response {
        return $this->render('user/show_user.html.twig', [
            'user' => $user,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_user_edit', methods: ['GET', 'POST'])]
    public function edit(
        Request $request,
        User $user,
        EntityManagerInterface $entityManager
    ): Response {
        $form = $this->createForm(UserType::class, $user, ['mode' => 'updateUser']);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            $this->addFlash('success', 'Modification(s) réalisée(s) avec succés.');
            return $this->redirectToRoute('app_user_show', ['id' => $user->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('user/edit.html.twig', [
            'user' => $user,
            'form' => $form->createView(),
        ]);
    }
}
