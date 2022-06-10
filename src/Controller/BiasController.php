<?php

namespace App\Controller;

use App\Entity\Bias;
use App\Form\BiasType;
use App\Repository\BiasRepository;
use App\Service\FileUploader;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/bias", name="bias_")
 */
class BiasController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $listRender;
    private $showRender;

    public function __construct(EntityManagerInterface $manager, BiasRepository $repository, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->route = 'home_index';
        $this->fragment = 'bias';
        $this->formRender = 'bias/index.html.twig';
        $this->listRender = 'bias/list.html.twig';
        $this->showRender = 'bias/show.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new Bias();
        $form = $this->createForm(BiasType::class, $item, []);
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
    public function update(Bias $item, Request $request): Response
    {
        $form = $this->createForm(BiasType::class, $item, []);
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

    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(): Response
    {
        return $this->render($this->listRender, [
            'biases' => $this->repository->findAllByName(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Bias $bias): Response
    {
        return $this->render($this->showRender, [
            'bias' => $bias,
        ]);
    }
}
