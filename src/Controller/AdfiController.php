<?php

namespace App\Controller;

use App\Entity\Adfi;
use App\Form\AdfiType;
use App\Repository\AdfiRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/adfi", name="adfi_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class AdfiController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $defaultSite;

    public function __construct(EntityManagerInterface $manager, AdfiRepository $repository)
    {
        $this->manager = $manager;
        $this->route = 'admin';
        $this->fragment = 'adfi';
        $this->formRender = 'adfi/index.html.twig';
        $this->showRender = 'adfi/show.html.twig';
        $this->listRender = 'adfi/list.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
        $this->defaultSite = "https://www.unadfi.org/";
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new Adfi();
        $form = $this->createForm(AdfiType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("site")->getData() === null) {
                $item->setSite($this->defaultSite);
            }
            $item->setSlug($this->slugger->slugify($item->getCity()));
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
        ]);
    }


    /**
     * @Route("/update/{slug}", name="update", methods={"GET", "POST"})
     */
    public function update(Adfi $item, Request $request): Response
    {
        $form = $this->createForm(AdfiType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            if ($form->get("site")->getData() === null) {
                $item->setSite($this->defaultSite);
            }
            $item->setSlug($this->slugger->slugify($item->getCity()));
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
}
