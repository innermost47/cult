<?php

namespace App\Controller;

use App\Entity\Recuperation;
use App\Repository\RecuperationRepository;
use App\Repository\UserRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;

class ResetPasswordController extends AbstractController
{
    public function index(UserRepository $userRepository, EntityManagerInterface $manager, RecuperationRepository $recuperationRepository, SessionInterface $session)
    {
        $msg = "";
        if (isset($_POST['submit'], $_POST['email'])) {
            if (!empty($_POST['email'])) {
                $email = $this->verifyInput($_POST['email']);
                if (filter_var($email, FILTER_VALIDATE_EMAIL)) {
                    $user = $userRepository->findOneByEmail($email);
                    if ($user) {
                        $recup_code = "";
                        for ($i = 0; $i < 8; $i++) {
                            $recup_code .= mt_rand(0, 9);
                        }
                        $count = $recuperationRepository->rowCount($email);
                        if ($count > 0) {
                            $recuperation = $recuperationRepository->findOneByEmail($email);
                            $recuperation->setCode($recup_code);
                        } else {
                            $recuperation = new Recuperation();
                            $recuperation->setEmail($user->getEmail());
                            $recuperation->setCode($recup_code);
                        }
                        $session->set('email', $user->getEmail());
                        $recuperation->setConfirm(false);
                        $manager->persist($recuperation);
                        $manager->flush();

                        $header = 'Content-Type: text/html; charset=\"iso-8859-1\"' . "\n";
                        $header .= 'From:"Esprit sceptique"<innedqcf@world-304.fr.planethoster.net>' . "\n";
                        $header .= 'Delivered-to: ' . $email . "\n\n";
                        $message = '
                    <html>
                    <head>
                    <title>Récupération de mot de passe - Développement In Situ</title>
                    <meta charset="utf-8" />
                    </head>
                    <body>
                    <font color="#303030";>
                        <div align="center">
                        <table width="600px">
                            <tr>
                            <td>
                                <div align="center">Bonjour !</div>
                                <div align="center">
                                Voici votre code de récupération: <b>' . $recup_code . '</b>
                                <br>À bientôt sur <a href="https://cult.inner-most.fr/">Esprit sceptique</a> !
                                </div>
                            </td>
                            </tr>
                            <tr>
                            <td align="center">
                                <font size="2">
                                Ceci est un email automatique, merci de ne pas y répondre
                                </font>
                            </td>
                            </tr>
                        </table>
                        </div>
                    </font>
                    </body>
                    </html>
                    ';
                        mail($email, "Récupération de mot de passe - Esprit Sceptique", $message, $header);
                        return $this->redirectToRoute('enter-code');
                    } else {
                        $msg = "Utisateur inexistant";
                    }
                } else {
                    $msg = "Veuillez entrer une adresse email valide !";
                }
            } else {
                $msg = "Veuillez entrer votre adresse mail !";
            }
        }
        return $this->render('reset_password/index.html.twig', [
            'msg' => $msg
        ]);
    }

    public function code(RecuperationRepository $recuperationRepository, SessionInterface $session, EntityManagerInterface $manager)
    {
        $msg = "";
        if (isset($_POST['submit'], $_POST['code'])) {
            if (!empty($_POST['code'])) {
                $code = $this->verifyInput($_POST['code']);
                $email = $session->get('email');
                $count = $recuperationRepository->rowCountEmailAndCode($code, $email);
                if ($count == 1) {
                    $recuperation = $recuperationRepository->findOneByEmail($email);
                    $recuperation->setConfirm(true);
                    $manager->persist($recuperation);
                    $manager->flush();
                    return $this->redirectToRoute('change-password');
                } else {
                    $msg = "Code invalide !";
                }
            } else {
                $msg = "Veuillez entrer votre code de confirmation !";
            }
        }

        return $this->render('reset_password/code.html.twig', [
            'msg' => $msg
        ]);
    }

    public function change(RecuperationRepository $recuperationRepository, SessionInterface $session, UserRepository $userRepository, EntityManagerInterface $manager, UserPasswordHasherInterface $userPasswordHasher)
    {
        $msg = "";
        if (isset($_POST['submit'])) {
            if (isset($_POST['mdp'], $_POST['mdpConfirm'])) {
                $email = $session->get('email');
                $recuperation = $recuperationRepository->findOneByEmail($email);
                $confirm = $recuperation->isConfirm();
                if ($confirm) {
                    $mdp = $this->verifyInput($_POST['mdp']);
                    $mdp2 = $this->verifyInput($_POST['mdpConfirm']);
                    if (!empty($mdp) and !empty($mdp2)) {
                        if ($mdp == $mdp2) {
                            $mdplength = strlen($mdp);
                            if ($mdplength >= 8) {
                                $user = $userRepository->findOneByEmail($email);
                                $user->setPassword(
                                    $userPasswordHasher->hashPassword(
                                        $user,
                                        $mdp
                                    )
                                );
                                $manager->persist($user);
                                $manager->flush();
                                $manager->remove($recuperation);
                                $manager->flush();
                                return $this->redirectToRoute('login');
                            } else {
                                $msg = "Votre mot de passe doît être composé d'au moins 8 caractères !";
                            }
                        } else {
                            $msg = "Vos mots de passe ne correspondent pas !";
                        }
                    } else {
                        $msg = "Veuillez remplir tous les champs !";
                    }
                } else {
                    $msg = "Veuillez valider votre email grâce au code qui vous a été envoyé par email !";
                }
            } else {
                $msg = "Veuillez remplir tous les champs !";
            }
        }
        return $this->render('reset_password/change.html.twig', [
            'msg' => $msg
        ]);
    }

    public function verifyInput($var)
    {
        $var = trim($var);
        $var = stripslashes($var);
        $var = htmlspecialchars($var);
        return $var;
    }
}
