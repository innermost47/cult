<?php

namespace App\Controller;

use App\Entity\Image;
use App\Entity\Praticien;
use App\Form\PraticienType;
use App\Repository\PraticienRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

/**
 * @Route("/praticien", name="praticien_")
 */
class PraticienController extends AbstractController
{

    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $fileUploader;
    private $showRender;

    public function __construct(EntityManagerInterface $manager, PraticienRepository $repository, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->route = 'home_index';
        $this->fragment = 'praticien';
        $this->formRender = 'praticien/index.html.twig';
        $this->showRender = 'praticien/show.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new Praticien();
        $form = $this->createForm(PraticienType::class, $item, [
            'required' => true
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {
                    $imageName = $this->fileUploader->upload($image);
                    $img = new Image();
                    $img->setName($imageName);
                    $item->addImage($img);
                }
                $item->setSlug($this->slugger->slugify($item->getFirstName() . ' ' . $item->getLastName()));
                $this->manager->persist($item);
                $this->manager->flush();
            }
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'images' => null
        ]);
    }


    /**
     * @Route("/update/{slug}", name="update", methods={"GET", "POST"})
     */
    public function update(Praticien $item, Request $request): Response
    {
        $form = $this->createForm(PraticienType::class, $item, [
            'required' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $images = $form->get('images')->getData();
            if ($images) {
                foreach ($images as $image) {
                    $this->fileUploader->delete($image);
                    $imageName = $this->fileUploader->upload($image);
                    $img = new Image();
                    $img->setName($imageName);
                    $item->addImage($img);
                }
            }
            $item->setSlug($this->slugger->slugify($item->getFirstName() . ' ' . $item->getLastName()));
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'images' => $item->getImages(),
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
            foreach ($item->getImages() as $image) {
                $this->fileUploader->delete($image->getName());
            }
            $this->manager->remove($item);
            $this->manager->flush();
        }
        return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
    }

    /**
     * @Route("/delete/image/{id}", name="image_delete", methods={"DELETE"})
     */
    public function deleteImage(Image $image, Request $request)
    {
        $data = json_decode($request->getContent(), true);
        if ($this->isCsrfTokenValid('delete' . $image->getId(), $data['_token'])) {
            $this->fileUploader->delete($image->getName());
            $this->manager->remove($image);
            $this->manager->flush();
            return new JsonResponse(['success' => true], 200);
        } else {
            return new JsonResponse(['error' => 'Token invalide'], 400);
        }
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(Praticien $praticien): Response
    {
        return $this->render($this->showRender, [
            'praticien' => $praticien,
        ]);
    }
}
