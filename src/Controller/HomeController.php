<?php

namespace App\Controller;

use App\Repository\PictureRepository;
use App\Repository\ServiceRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
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

    #[Route('/search', name: 'app_search')]
    public function renderSearch(): Response
    {
        return $this->render('home/search.html.twig');
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

    #[Route('/gts', name: 'app_gts')]
    public function renderGts(): Response
    {
        return $this->render('home/gts.html.twig');
    }
}
