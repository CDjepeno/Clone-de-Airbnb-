<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Entity\PasswordUpdate;
use App\Form\RegistrationType;
use App\Form\PasswordUpdateType;
use Symfony\Component\Form\FormError;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Http\Authentication\AuthenticationUtils;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AccountController extends AbstractController
{
    /**
     * Permet d'afficher et de gérer le formulaire de login
     * 
     * @Route("/login", name="login_account")
     * 
     * @param AutheticationUtils $utils
     * 
     * @return Response
     */
    public function login(AuthenticationUtils $utils)
    {
        $error    = $utils->getLastAuthenticationError();
        $username = $utils->getLastUsername();

        return $this->render('account/login.html.twig', [

            "hasError" => $error !== null,
            "username" => $username
        ]);
    }

    /**
     * Permet d'afficher et de gérer le formulaire d'enregistrement
     *
     * @Route("/register", name="register_account")
     * 
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * 
     * @return Response
     */
    public function register(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $user = new User();

        $form = $this->createForm(RegistrationType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $hash = $encoder->encodePassword($user,$user->getPassword());
            $user->setHash($hash);

            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                'Votre compte a bien été crée !'
            );

            return $this->redirectToRoute("ads_index");
        }

        return $this->render("account/register.html.twig",[
            "form" => $form->createView()
        ]);
    }


    /**
     * Permet d'afficher et de gerer le formulaire de modification d'utilisateur
     *
     * @Route("/profile-update/account", name="profile_update_account")
     * 
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function updateProfile(Request $request, EntityManagerInterface $manager) {
        $user = $this->getUser();
        
        $form = $this->createForm(AccountType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                'success',
                "Votre compte a été modifié avec succès !"
            );

            return $this->redirectToRoute("homepage");
        }

        return $this->render("account/profileUpdate.html.twig",[
            "form" => $form->createView()
        ]);
    }


    /**
     * Permet d'afficher de gerer le formulaire de modification de mot de passe
     *
     * @Route("/password-update/account", name="password_update_account")
     * 
     * @IsGranted("ROLE_USER")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param UserPasswordEncoderInterface $encoder
     * 
     * @return Response
     */
    public function updatePassword(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder) {
        $password = new PasswordUpdate;
        $form     = $this->createForm(PasswordUpdateType::class, $password);

        $form->handleRequest($request);

        $user     = $this->getUser();

        if($form->isSubmitted() && $form->isValid()) {

            if(!password_verify($password->getOldPassword(), $user->getHash())) {
                $form->get('oldPassword')->addError(new FormError("Le mot de passe que vous avez tapé n'est pas votre mot de passe actuel"));
            } else {
                $newPassword = $password->getNewPassword();

                $hash        = $encoder->encodePassword($user,$newPassword);

                $user->setHash($hash);

                $manager->persist($user);
                $manager->flush();

                $this->addFlash(
                    "success",
                    "Votre mot de passe a bien été modifié"
                );
                return $this->redirectToRoute("homepage");
            }
        }

        return $this->render("account/passwordUpdate.html.twig",[
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * Permet de renvoyer vers l'annonce de l'utilisateur connecter
     *
     * @Route("/account", name="account_user")
     * 
     * @IsGranted("ROLE_USER")
     * 
     * @return Response
     */
    public function account() {
        return $this->render('user/index.html.twig',[
            "user" => $this->getUser()
        ]);
    }

    /**
     * Permet d'afficher la liste des reservations faites par l'utilisateur
     * 
     * @Route("/account/bookings", name="account_bookings")
     *
     * @return Response
     */
    public function bookings() {
        return $this->render('account/bookings.html.twig');
    }


    /**
     * Permet de ce déconnecter 
     *
     * @Route("/logout", name="logout_account")
     * 
     * @return void
     */
    public function logout() {

    }
}
