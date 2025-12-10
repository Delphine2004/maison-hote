<?php

namespace App\Controller;

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
    public function renderGallery(): Response
    {
        return $this->render('home/gallery.html.twig');
    }

    #[Route('/services', name: 'app_services')]
    public function renderServices(): Response
    {
        return $this->render('home/services.html.twig');
    }

    #[Route('/search', name: 'app_search')]
    public function renderSearch(): Response
    {
        return $this->render('home/search.html.twig');
    }
}
