<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Security\Core\Security as CoreSecurity;

/**
 * @Route("/group", name="group_")
 * @Security("is_granted('ROLE_USER')")
 */
class ChangePasswordController extends AbstractController
{
    public function update($id, CoreSecurity $security, UserRepository $userRepository, Request $request, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher): Response
    {
        if (!$id) {
            return $this->redirectToRoute('admin');
        }

        $item = $userRepository->findOneById($id);

        $user = $security->getUser();
        $user = $userRepository->findOneByUserIdentifier($user->getUserIdentifier());

        if ($user != $item) {
            return $this->redirectToRoute('admin');
        }

        if ($item != null) {
            $form = $this->createForm(ChangePasswordType::class);
            $form->handleRequest($request);

            if ($form->isSubmitted() && $form->isValid()) {
                if ($form->get('password')->getData() !== $form->get('checkPassword')->getData()) {
                    $this->addFlash('error', 'Les mots de passe ne correspondent pas');
                } else {
                    if (strlen($form->get('password')->getData()) < 8) {
                        $this->addFlash('error', 'Votre mot de passe doit être composé d\'au moins 8 caractères');
                    } else {
                        $item->setPassword(
                            $userPasswordHasher->hashPassword(
                                $item,
                                $form->get('password')->getData()
                            )
                        );
                        $manager->persist($item);
                        $manager->flush();
                        return $this->redirectToRoute('admin');
                    }
                }
            }
            return $this->render('change_password/index.html.twig', [
                'form' => $form->createView(),
            ]);
        } else {
            return $this->redirectToRoute('admin');
        }
    }
}
