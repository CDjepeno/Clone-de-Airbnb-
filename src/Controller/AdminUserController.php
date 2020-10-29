<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\AccountType;
use App\Repository\UserRepository;
use App\Service\PaginationService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminUserController extends AbstractController
{
    /**
     * Permet d'afficher la liste des utilisateurs
     * 
     * @Route("/admin/user/{page<\d+>?1}", name="admin_users_index")
     * 
     * @return Response
     */
    public function index(UserRepository $users, $page, PaginationService $pagination)
    {
        $pagination->setEntityclass(User::class)
                    ->setLimit(5)
                    ->setPage($page);
                    
        return $this->render('admin/user/index.html.twig', [
            'pagination' => $pagination,
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'un utilisateur
     *
     * @Route("/admin/user/{id}/edit", name="admin_user_edit")
     * 
     * @param User $user
     * @param EntityManagerInterface $manager
     * @param Request $request
     * @return void
     */
    public function edit(User $user, EntityManagerInterface $manager, Request $request) {
        $form = $this->createForm(AccountType::class,$user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($user);
            $manager->flush();

            $this->addFlash(
                "success",
                "Le profil a bien été modifié"
            );
        }
        return $this->render("admin/user/edit.html.twig",[
            "form" => $form->createView(),
            "user" => $user
        ]);
    }

    /**
     * Permet de supprimer un utilisateur
     *
     * @Route("admin/user/{id}/delete", name="admin_user_delete")
     * 
     * @param User $user
     * @param EntityManagerInterface $manager
     * @return Response
     */
    public function delete(User $user, EntityManagerInterface $manager) {
        $manager->remove($user);
        $manager->flush();

        $this->addFlash(
            "success",
            "Le profil de {$user->getFirstName()} a bien été supprimé"
        );

        return $this->redirectToRoute("admin_comments_index");

    }
}
