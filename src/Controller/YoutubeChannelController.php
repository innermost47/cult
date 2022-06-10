<?php

namespace App\Controller;

use App\Entity\YoutubeChannel;
use App\Form\YoutubeChannelType;
use App\Repository\YoutubeChannelRepository;
use App\Service\FileUploader;
use Cocur\Slugify\Slugify;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

/**
 * @Route("/youtube-channel", name="youtube_channel_")
 */
class YoutubeChannelController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $slugger;
    private $repository;
    private $fileUploader;
    private $showRender;

    public function __construct(EntityManagerInterface $manager, YoutubeChannelRepository $repository, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->route = 'home_index';
        $this->fragment = 'youtube-channel';
        $this->formRender = 'youtube_channel/index.html.twig';
        $this->showRender = 'youtube_channel/show.html.twig';
        $this->slugger = new Slugify();
        $this->repository = $repository;
        $this->fileUploader = $fileUploader;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request): Response
    {
        $item = new YoutubeChannel();
        $form = $this->createForm(YoutubeChannelType::class, $item, [
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
    public function update(YoutubeChannel $item, Request $request): Response
    {
        $form = $this->createForm(YoutubeChannelType::class, $item, [
            'required' => false
        ]);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $image = $form->get('image')->getData();
            if ($image) {
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
     * @Route("/{slug}", name="show", methods={"GET"})
     */
    public function show(YoutubeChannel $youtubeChannel): Response
    {
        return $this->render($this->showRender, [
            'youtubeChannel' => $youtubeChannel,
        ]);
    }
}
