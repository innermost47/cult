<?php

namespace App\Controller;

use App\Repository\BiasCategoryRepository;
use App\Repository\BiasRepository;
use App\Repository\GroupRepository;
use App\Repository\PraticienRepository;
use App\Repository\TechniqueRepository;
use App\Repository\YoutubeChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', []);
    }
}
