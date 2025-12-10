<?php

namespace App\Controller;

use App\Entity\Rate;
use App\Form\RateType;
use App\Repository\RateRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

#[Route('/rate')]
final class RateController extends AbstractController
{
    #[Route(name: 'app_rate_index', methods: ['GET'])]
    public function index(RateRepository $rateRepository): Response
    {
        return $this->render('rate/index.html.twig', [
            'rates' => $rateRepository->findAll(),
        ]);
    }

    #[Route('/new', name: 'app_rate_new', methods: ['GET', 'POST'])]
    public function new(Request $request, EntityManagerInterface $entityManager): Response
    {
        $rate = new Rate();
        $form = $this->createForm(RateType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->persist($rate);
            $entityManager->flush();

            return $this->redirectToRoute('app_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rate/new.html.twig', [
            'rate' => $rate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rate_show', methods: ['GET'])]
    public function show(Rate $rate): Response
    {
        return $this->render('rate/show.html.twig', [
            'rate' => $rate,
        ]);
    }

    #[Route('/{id}/edit', name: 'app_rate_edit', methods: ['GET', 'POST'])]
    public function edit(Request $request, Rate $rate, EntityManagerInterface $entityManager): Response
    {
        $form = $this->createForm(RateType::class, $rate);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $entityManager->flush();

            return $this->redirectToRoute('app_rate_index', [], Response::HTTP_SEE_OTHER);
        }

        return $this->render('rate/edit.html.twig', [
            'rate' => $rate,
            'form' => $form,
        ]);
    }

    #[Route('/{id}', name: 'app_rate_delete', methods: ['POST'])]
    public function delete(Request $request, Rate $rate, EntityManagerInterface $entityManager): Response
    {
        if ($this->isCsrfTokenValid('delete'.$rate->getId(), $request->getPayload()->getString('_token'))) {
            $entityManager->remove($rate);
            $entityManager->flush();
        }

        return $this->redirectToRoute('app_rate_index', [], Response::HTTP_SEE_OTHER);
    }
}
