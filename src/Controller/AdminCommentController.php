<?php

namespace App\Controller;

use App\Entity\Comment;
use App\Form\AdminCommentType;
use App\Service\PaginationService;
use App\Repository\CommentRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminCommentController extends AbstractController
{
    /**
     * Permet d'afficher la liste des commentaires
     * 
     * @Route("/admin/comments/{page<\d+>?1}", name="admin_comments_index")
     * 
     * @param CommentRepository $comment
     * 
     * @return Response
     */
    public function index(CommentRepository $comments,$page, PaginationService $pagination)
    {
        $pagination->setEntityclass(Comment::class)
                    ->setLimit(5)
                    ->setPage($page);

        return $this->render('admin/comment/index.html.twig', [
            'pagination' => $pagination
        ]);
    }


    // classe formulaire AdminCommentType| 
    /**
     * Permet de d'afficher le formulaire d'édition d'un commentaire
     *
     * @Route("/admin/comment/{id}/edit", name="admin_comment_edit")
     * 
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * @param Request $request
     * 
     * @return Response
     */
    public function edit(Comment $comment, EntityManagerInterface $manager, Request $request) {
        $form = $this->createForm(AdminCommentType::class, $comment);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            $manager->persist($comment);
            $manager->flush();

            $this->addFlash(
                "success",
                "Le commentaire {$comment->getId()} a bien été modifié !"
            );
        }

        return $this->render("admin/comment/edit.html.twig",[
            "comment" => $comment,
            "form" => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer un commentaire
     * 
     * @Route("Admin/comment/{id}/delete", name="admin_comment_delete")
     *
     * @param Comment $comment
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function delete(Comment $comment, EntityManagerInterface $manager) {
        
        $manager->remove($comment);
        $manager->flush();

        $this->addFlash(
            "success",
            "Le commentaire de {$comment->getAuthor()->fullname()} a bien été supprimé"
        );
       
        return $this->redirectToRoute("admin_comments_index");
    }

    
}
