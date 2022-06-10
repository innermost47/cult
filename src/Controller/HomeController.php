<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\PraticienRepository;
use App\Repository\TechniqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/", name="home_")
 */
class HomeController extends AbstractController
{
    private $praticienRepository;
    private $techniqueRepository;
    private $groupRepository;

    public function __construct(PraticienRepository $praticienRepository, TechniqueRepository $techniqueRepository, GroupRepository $groupRepository)
    {
        $this->praticienRepository = $praticienRepository;
        $this->techniqueRepository = $techniqueRepository;
        $this->groupRepository = $groupRepository;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('home/index.html.twig', [
            'praticiens' => $this->praticienRepository->findAllByLastName(),
            'techniques' => $this->techniqueRepository->findAllByName(),
            'groups' => $this->groupRepository->findAllByName()
        ]);
    }
}
