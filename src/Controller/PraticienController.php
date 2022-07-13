<?php

namespace App\Controller;

use App\Entity\Comments;
use App\Entity\Image;
use App\Entity\Praticien;
use App\Entity\Reporting;
use App\Form\CommentType;
use App\Form\PraticienType;
use App\Form\ReportType;
use App\Repository\CommentsRepository;
use App\Repository\PraticienRepository;
use App\Service\FileUploader;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Cocur\Slugify\Slugify;
use DateTime;
use DateTimeImmutable;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;

/**
 * @Route("/praticien", name="praticien_")
 * @Security("is_granted('ROLE_USER')")
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
    private $listRender;

    public function __construct(EntityManagerInterface $manager, PraticienRepository $repository, FileUploader $fileUploader)
    {
        $this->manager = $manager;
        $this->route = 'admin';
        $this->fragment = 'praticien';
        $this->formRender = 'praticien/index.html.twig';
        $this->showRender = 'praticien/show.html.twig';
        $this->listRender = 'praticien/list.html.twig';
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
     * @Route("/list", name="list", methods={"GET"})
     */
    public function list(PraticienRepository $praticienRepository): Response
    {
        return $this->render($this->listRender, [
            'praticiens' => $praticienRepository->findAllByLastName(),
        ]);
    }

    /**
     * @Route("/{slug}", name="show", methods={"GET", "POST"})
     */
    public function show(Praticien $praticien, Request $request, CommentsRepository $commentsRepository): Response
    {

        // ADD REPORT 

        $item = new Reporting();

        $form = $this->createForm(ReportType::class, $item, [
            'function' => 'add'
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setPraticien($praticien);
            $item->setCreatedAt(new DateTimeImmutable());
            $newDate = new DateTime();
            $newDate = $newDate->format('d-m-Y-h-i-s');
            $item->setSlug($this->slugger->slugify('report-' . $item->getPraticien()->getFirstName() . '-' . $item->getPraticien()->getLastName() . '-' . $newDate));
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirect($request->getUri());
        }

        // ADD COMMENT

        $comment = new Comments();
        $commentForm = $this->createForm(CommentType::class, $comment);
        $commentForm->handleRequest($request);

        if ($commentForm->isSubmitted() && $commentForm->isValid()) {
            $comment->setCreatedAt(new DateTimeImmutable());
            $comment->setUser($this->getUser());
            $comment->setPraticien($praticien);
            $newDate = new DateTime();
            $newDate = $newDate->format('d-m-Y-h-i-s');
            $comment->setSlug($this->slugger->slugify($comment->getTitle() . '-' . $newDate));
            $this->manager->persist($comment);
            $this->manager->flush();
            return $this->redirect($request->getUri());
        }

        // UPDATE COMMENT

        if (isset($_POST["update"])) {
            $title = $this->verifyInput($_POST['title']);
            $text = $this->verifyInput($_POST['text']);
            $slug = $this->verifyInput($_POST["slug"]);
            if (!empty($title) && !empty($text) && !empty($slug)) {
                if (strlen($title) < 255) {
                    $commentToUpdate = $commentsRepository->findOneBy(["slug" => $slug]);
                    if ($commentToUpdate !== null) {
                        $commentToUpdate->setTitle($title);
                        $commentToUpdate->setText($text);
                        $this->manager->persist($commentToUpdate);
                        $this->manager->flush();
                    }
                }
            }
        }

        return $this->render($this->showRender, [
            'form' => $form->createView(),
            'praticien' => $praticien,
            'commentForm' => $commentForm->createView(),
            'user' => $this->getUser(),
        ]);
    }

    /**
     * @Route("/comment/delete/{id}", name="comment_delete")
     */
    public function deleteComment($id, CommentsRepository $commentsRepository)
    {
        if (!$id) {
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        $item = $commentsRepository->findOneBy(["id" => $id]);

        if ($item !== null) {
            $this->manager->remove($item);
            $this->manager->flush();
        }
        $page = $_SERVER["HTTP_REFERER"];
        return $this->redirect($page);
    }
}
