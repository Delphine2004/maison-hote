<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use App\Repository\ServiceRepository;
use App\Repository\RoomRepository;

use App\Form\SearchType;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Attribute\Route;

final class HomeController extends AbstractController
{
    #[Route('/', name: 'app_home')]
    public function index(): Response
    {
        return $this->render('home/index.html.twig');
    }

    #[Route('/gallery', name: 'app_gallery')]
    public function renderGallery(
        PictureRepository $pictureRepository
    ): Response {
        $pictures = $pictureRepository->findAllPictures();
        return $this->render('home/gallery.html.twig', ['pictures' => $pictures]);
    }

    #[Route('/infos', name: 'app_services')]
    public function renderServices(
        ServiceRepository $serviceRepository
    ): Response {
        $services = $serviceRepository->findAllServices();
        return $this->render('home/services.html.twig', ['services' => $services]);
    }

    #[Route('/search', name: 'app_search_room', methods: ['GET', 'POST'])]
    public function renderSearch(
        Request $request,
        RoomRepository $roomRepository
    ): Response {
        $form = $this->createForm(SearchType::class);
        $form->handleRequest($request);

        $rooms = [];

        if ($form->isSubmitted() && $form->isValid()) {
            $data = $form->getData();

            $startDate = $data->startingDate;
            $endDate   = $data->endingDate;

            $rooms = $roomRepository->findRoomsByPeriod(
                $startDate,
                $endDate
            );
        }

        if ($this->isGranted('ROLE_EMPLOYE')) {
            return $this->render('booking/staff_search.html.twig', [
                'form' => $form->createView(),
                'rooms' => $rooms
            ]);
        } else {
            return $this->render('home/search.html.twig', [
                'form' => $form->createView(),
                'rooms' => $rooms
            ]);
        }
    }

    #[Route('/faq', name: 'app_faq')]
    public function renderFaq(): Response
    {
        return $this->render('home/faq.html.twig');
    }

    #[Route('/legalNotices', name: 'app_legal_notices')]
    public function renderLegalNotices(): Response
    {
        return $this->render('home/legal_notices.html.twig');
    }

    #[Route('/privatyPolicy', name: 'app_privaty_policy')]
    public function renderPrivacyPolicy(): Response
    {
        return $this->render('home/privaty_policy.html.twig');
    }

    #[Route('/gts', name: 'app_gts')]
    public function renderGts(): Response
    {
        return $this->render('home/gts.html.twig');
    }
}
