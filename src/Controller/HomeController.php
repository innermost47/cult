<?php

namespace App\Controller;

use App\Repository\GroupRepository;
use App\Repository\PraticienRepository;
use App\Repository\ReportingRepository;
use App\Repository\TechniqueRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/", name="home_")
 * @Security("is_granted('ROLE_USER')")
 */
class HomeController extends AbstractController
{
    /**
     * @Route("/", name="index", methods={"GET", "POST"})
     */
    public function index(PraticienRepository $praticienRepository, ReportingRepository $reportingRepository, TechniqueRepository $techniqueRepository, GroupRepository $groupRepository): Response
    {
        if (!empty($_POST["search"])) {
            $search = trim($_POST['search']);
            $search = stripslashes($search);
            $search = htmlspecialchars($search);

            $type = trim($_POST['type']);
            $type = stripslashes($type);
            $type = htmlspecialchars($type);

            $results = [];

            switch ($type) {
                case 'praticien':
                    $results = $praticienRepository->findEverythingLike($search);
                    break;
                case 'technique':
                    $results = $techniqueRepository->findEverythingLike($search);
                    break;
                case 'group':
                    $results = $groupRepository->findEverythingLike($search);
                    break;
                case 'report':
                    $results = $reportingRepository->findEverythingLike($search);
                    break;
                default:
                    return $this->render('home/index.html.twig', []);
                    break;
            }

            return $this->render('home/results.html.twig', [
                'results' => $results,
                'type' => $type
            ]);
        }
        return $this->render('home/index.html.twig', []);
    }
}
