<?php

namespace App\Controller;

use App\Entity\Review;
use App\Entity\Booking;

use App\Enum\UserRole;
use App\Enum\ReviewStatus;
use App\Form\SearchReviewType;
use App\Form\ReviewType;
use App\Repository\ReviewRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/review')]

final class ReviewController extends AbstractController
{

    #[Route('', name: 'app_review_index')]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function index(ReviewRepository $reviewRepository): Response
    {
        $newReviews = $reviewRepository->findReviewsToApprove();
        return $this->render('review/index.html.twig', [
            'newReviews' => $newReviews,
        ]);
    }

    #[Route('/search', name: 'app_search_review', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function search(
        Request $request,
        ReviewRepository $reviewRepository
    ): Response {
        $form = $this->createForm(SearchReviewType::class);
        $form->handleRequest($request);

        $reviews = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $reviews = $reviewRepository->findReviewByFilters(
                $data
            );
        }

        return $this->render('review/search.html.twig', [
            'form' => $form->createView(),
            'reviews' => $reviews
        ]);
    }

    #[Route('/new/{bookingId}', name: 'app_review_new', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::CLIENT->value)]
    public function new(
        Request $request,
        int $bookingId,
        EntityManagerInterface $entityManager
    ): Response {

        // Récupération
        $booking = $entityManager->getRepository(Booking::class)->find($bookingId);

        $review = new Review();
        $form = $this->createForm(ReviewType::class, $review);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {


            $review->setBooking($booking);
            $review->setStatus(ReviewStatus::PENDING);

            $entityManager->persist($review);
            $entityManager->flush();
            $this->addFlash('success', 'Commentaire ajouté avec succés.');
            return $this->redirectToRoute('app_client_show', ['id' => ($booking->getPassenger())->getId()], Response::HTTP_SEE_OTHER);
        }

        return $this->render('review/new.html.twig', [
            'review' => $review,
            'form' => $form->createView(),
        ]);
    }


    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function show(
        Review $review
    ): Response {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/approve', name: 'app_review_approve', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function approve(
        Request $request,
        Review $review,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('approve' . $review->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $userConnected = $this->getUser();
        // Vérification que l'utilisateur connécté est bien autorisé
        if (!$userConnected && !$this->isGranted('ROLE_EMPLOYE')) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Vérification que le commentaire n'est pas "presente"
        if ($review->getStatus() === ReviewStatus::CONFIRMED || $review->getStatus() === ReviewStatus::REJECTED) {
            $this->addFlash('sucess', 'Vous n\'êtes pas autorisé à modifier le commentaire.');
            return $this->redirectToRoute('app_review_show', [
                'id' => $review->getId()
            ]);
        }

        $review->setStatus(ReviewStatus::CONFIRMED);

        $entityManager->flush();

        $this->addFlash('success', 'Commentaire approuvé avec succés.');
        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }

    #[Route('/{id}/reject', name: 'app_review_reject', methods: ['GET', 'POST'])]
    #[IsGranted(UserRole::EMPLOYE->value)]
    public function rejecte(
        Request $request,
        Review $review,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('reject' . $review->getId(), $request->request->get('_token'))) {
            throw $this->createAccessDeniedException('Token CSRF invalide.');
        }

        $userConnected = $this->getUser();

        // Vérification que l'utilisateur connécté est bien autorisé
        if (!$userConnected && !$this->isGranted('ROLE_EMPLOYE')) {
            $this->addFlash('sucess', 'Vous devez être connecté.');
            return $this->redirectToRoute('app_login');
        }

        // Vérification que le commentaire n'est pas "presente"
        if ($review->getStatus() === ReviewStatus::CONFIRMED || $review->getStatus() === ReviewStatus::REJECTED) {
            $this->addFlash('sucess', 'Vous n\'êtes pas autorisé à modifier le commentaire.');
            return $this->redirectToRoute('app_review_show', [
                'id' => $review->getId()
            ]);
        }

        $review->setStatus(ReviewStatus::REJECTED);

        $entityManager->flush();

        $this->addFlash('success', 'Commentaire approuvé avec succés.');
        return $this->redirectToRoute('app_review_index', [], Response::HTTP_SEE_OTHER);
    }
}
