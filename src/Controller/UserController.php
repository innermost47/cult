<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\UserType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

/**
 * @Route("/user", name="user_")
 * @Security("is_granted('ROLE_ADMIN')")
 */
class UserController extends AbstractController
{
    private $manager;
    private $route;
    private $fragment;
    private $formRender;
    private $repository;
    private $showRender;
    private $listRender;

    public function __construct(EntityManagerInterface $manager, UserRepository $repository)
    {
        $this->manager = $manager;
        $this->route = 'admin';
        $this->fragment = 'user';
        $this->formRender = 'user/index.html.twig';
        $this->showRender = 'user/show.html.twig';
        $this->listRender = 'user/list.html.twig';
        $this->repository = $repository;
    }

    /**
     * @Route("/add", name="add", methods={"GET", "POST"})
     */
    public function add(Request $request, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        $item = new User();
        $form = $this->createForm(UserType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $item->setPassword(
                $userPasswordHasher->hashPassword(
                    $item,
                    "12345678"
                )
            );
            $role = $form->get("role")->getData();
            if ($role == "admin") {
                $item->setRoles(["ROLE_ADMIN"]);
            }
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'role' => 'user'
        ]);
    }


    /**
     * @Route("/update/{id}", name="update", methods={"GET", "POST"})
     */
    public function update(User $item, Request $request): Response
    {
        $form = $this->createForm(UserType::class, $item);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {
            $role = $form->get("role")->getData();
            if ($role == "admin") {
                $item->setRoles(["ROLE_ADMIN"]);
            } else {
                $item->setRoles([]);
            }
            $this->manager->persist($item);
            $this->manager->flush();
            return $this->redirectToRoute($this->route, ['_fragment' => $this->fragment]);
        }

        $role = "";

        if (in_array('ROLE_ADMIN', $item->getRoles())) {
            $role = 'admin';
        } else {
            $role = 'user';
        }

        return $this->render($this->formRender, [
            'form' => $form->createView(),
            'role' => $role
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
    public function list(UserRepository $userRepository): Response
    {
        return $this->render($this->listRender, [
            'users' => $userRepository->findAllByName(),
        ]);
    }

    /**
     * @Route("/{id}", name="show", methods={"GET"})
     */
    public function show(User $user): Response
    {
        return $this->render($this->showRender, [
            'user' => $user,
        ]);
    }
}
