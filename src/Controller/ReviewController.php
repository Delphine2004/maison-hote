<?php

namespace App\Controller;

use App\Entity\Review;
use App\Enum\UserRole;
use App\Enum\ReviewStatus;
use App\Form\SearchReviewType;
use App\Repository\ReviewRepository;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;
use Symfony\Component\Security\Http\Attribute\IsGranted;


#[Route('/review')]
#[IsGranted(UserRole::EMPLOYE->value)]
final class ReviewController extends AbstractController
{

    #[Route('', name: 'app_review_index')]
    public function index(ReviewRepository $reviewRepository): Response
    {
        $newReviews = $reviewRepository->findReviewsToApprouve();
        return $this->render('review/index.html.twig', [
            'newReviews' => $newReviews,
        ]);
    }

    #[Route('/search', name: 'app_search_review', methods: ['GET', 'POST'])]
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

    #[Route('/{id}', name: 'app_review_show', methods: ['GET'])]
    public function show(
        Review $review
    ): Response {
        return $this->render('review/show.html.twig', [
            'review' => $review,
        ]);
    }

    #[Route('/{id}/approuve', name: 'app_review_approuve', methods: ['GET', 'POST'])]
    public function approuve(
        Request $request,
        Review $review,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('approuve' . $review->getId(), $request->request->get('_token'))) {
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
    public function rejecte(
        Request $request,
        Review $review,
        EntityManagerInterface $entityManager
    ): Response {

        // Vérification que la requête est valide
        if (!$this->isCsrfTokenValid('approuve' . $review->getId(), $request->request->get('_token'))) {
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
