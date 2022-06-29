<?php

namespace App\Controller;

use App\Entity\Technique;
use App\Form\TechniqueType;
use App\Repository\TechniqueRepository;
use App\Service\FileUploader;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/technique", name="technique_")
 */
class TechniqueController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $fileUploader;
    private $showRender;
    private $listRender;

    public function __construct(EntityManagerInterface $manager, TechniqueRepository $repository, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->route = 'home_index';
        $this->fragment = 'technique';
        $this->formRender = 'technique/index.html.twig';
        $this->showRender = 'technique/show.html.twig';
        $this->listRender = 'technique/list.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new Technique();
        $form = $this->createForm(TechniqueType::class, $item, [
            'required' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $imageName = $this->fileUploader->upload($image);
                $item->setImage($imageName);
                $item->setSlug($this->slugger->slugify($item->getName()));
                $this->manager->persist($item);
                $this->manager->flush();
            }
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'image' => null,
        ]);
    }


    /**
     * @Route("/update/{slug}", name="update", methods={"GET", "POST"})
     */
    public function update(Technique $item, Request $request): Response
    {
        $form = $this->createForm(TechniqueType::class, $item, [
            'required' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
                $this->fileUploader->delete($image);
                $imageName = $this->fileUploader->upload($image);
                $item->setImage($imageName);
            }
            $item->setSlug($this->slugger->slugify($item->getName()));
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'image' => $item->getimage(),
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
            $this->fileUploader->delete($item->getImage());
            $this->manager->remove($item);
            $this->manager->flush();
        }
        return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
    }

    /**
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(TechniqueRepository $techniqueRepository): Response
    {
        return $this->render($this->listRender, [
            'techniques' => $techniqueRepository->findAllByName(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Technique $technique): Response
    {
        return $this->render($this->showRender, [
            'technique' => $technique,
        ]);
    }
}
