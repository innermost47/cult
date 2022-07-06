<?php

namespace App\Controller;

use App\Entity\Reporting;
use App\Form\ReportType;
use App\Repository\ReportingRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/report", name="report_")
 * @Security("is_granted('ROLE_USER')")
 */
class ReportController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $showRender;
    private $listRender;

    public function __construct(EntityManagerInterface $manager, ReportingRepository $repository)
    {
        $this->manager = $manager;
        $this->route = 'admin';
        $this->fragment = 'report';
        $this->formRender = 'report/index.html.twig';
        $this->showRender = 'report/show.html.twig';
        $this->listRender = 'report/list.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
    }

    /**
     * @Route("/update/{slug}", name="update", methods={"GET", "POST"})
     */
    public function update(Reporting $item, Request $request): Response
    {
        $form = $this->createForm(ReportType::class, $item, [
            'function' => 'update'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
        ]);
    }

    /**
     * @Route("/delete/{id}", name="delete")
     */
    public function delete($id): Response
    {
        if (!$id) {
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        $item = $this->repository->findOneById($id);

        if ($item !== null) {
            $this->manager->remove($item);
            $this->manager->flush();
        }
        return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(ReportingRepository $repository): Response
    {
        return $this->render($this->listRender, [
            'reports' => $repository->findBy([], ["createdAt" => "ASC"]),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Reporting $reporting): Response
    {
        return $this->render($this->showRender, [
            'report' => $reporting,
        ]);
    }
}
