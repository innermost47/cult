<?php

namespace App\Controller;

use App\Repository\AdfiRepository;
use App\Repository\BiasCategoryRepository;
use App\Repository\BiasRepository;
use App\Repository\GroupRepository;
use App\Repository\PraticienRepository;
use App\Repository\ReportingRepository;
use App\Repository\TechniqueRepository;
use App\Repository\UserRepository;
use App\Repository\YoutubeChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/admin", name="app_admin")
 * @Security("is_granted('ROLE_USER')")
 */
class AdminController extends AbstractController
{

    private $praticienRepository;
    private $techniqueRepository;
    private $groupRepository;
    private $biasRepository;
    private $biasCategoryRepository;
    private $youtubeChannelRepository;
    private $reportingRepository;
    private $userRepository;
    private $adfiRepository;

    public function __construct(PraticienRepository $praticienRepository, TechniqueRepository $techniqueRepository, GroupRepository $groupRepository, BiasRepository $biasRepository, BiasCategoryRepository $biasCategoryRepository, YoutubeChannelRepository $youtubeChannelRepository, ReportingRepository $reportingRepository, UserRepository $userRepository, AdfiRepository $adfiRepository)
    {
        $this->praticienRepository = $praticienRepository;
        $this->techniqueRepository = $techniqueRepository;
        $this->groupRepository = $groupRepository;
        $this->biasRepository = $biasRepository;
        $this->biasCategoryRepository = $biasCategoryRepository;
        $this->youtubeChannelRepository = $youtubeChannelRepository;
        $this->reportingRepository = $reportingRepository;
        $this->userRepository = $userRepository;
        $this->adfiRepository = $adfiRepository;
    }

    /**
     * @Route("/", name="index")
     */
    public function index(): Response
    {
        return $this->render('admin/index.html.twig', [
            'praticiens' => $this->praticienRepository->findAllByLastName(),
            'techniques' => $this->techniqueRepository->findAllByName(),
            'groups' => $this->groupRepository->findAllByName(),
            'biases' => $this->biasRepository->findAllByName(),
            'biasCategories' => $this->biasCategoryRepository->findAll(),
            'youtubeChannels' => $this->youtubeChannelRepository->findAllByName(),
            'reports' => $this->reportingRepository->findAll(),
            'users' => $this->userRepository->findAll(),
            'adfis' => $this->adfiRepository->findAll(),
        ]);
    }
}
