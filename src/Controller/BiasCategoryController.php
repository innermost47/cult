<?php

namespace App\Controller;

use App\Entity\BiasCategory;
use App\Form\BiasCategoryType;
use App\Repository\BiasCategoryRepository;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bias-category", name="bias_category_")
 */
class BiasCategoryController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $listRender;
    private $showRender;

    public function __construct(EntityManagerInterface $manager, BiasCategoryRepository $repository)
    {
        $this->manager = $manager;
        $this->route = 'home_index';
        $this->fragment = 'bias-category';
        $this->formRender = 'bias_category/index.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new BiasCategory();
        $form = $this->createForm(BiasCategoryType::class, $item, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setSlug($this->slugger->slugify($item->getName()));
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
    public function update(BiasCategory $item, Request $request): Response
    {
        $form = $this->createForm(BiasCategoryType::class, $item, []);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setSlug($this->slugger->slugify($item->getName()));
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
