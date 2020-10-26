<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Repository\AdRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class AdminAdController extends AbstractController
{
    /**
     * Permet d'afficher la page de toutes les annonces
     * 
     * @Route("/admin/ads", name="admin_ads_index")
     */
    public function index(AdRepository $ads)
    {
        return $this->render('admin/ad/index.html.twig', [
            'ads' => $ads->findAll()
        ]);
    }

    /**
     * Permet d'afficher le formulaire d'édition d'une annonce
     * 
     * @Route("/admin/ads/{id}/edit", name="admin_ads_edit")
     *
     * @param Ad $ad
     * @param Request $request
     * @param EntityManagerInterface $manager 
     * 
     * @return Response
     */
    public function edit(Ad $ad, Request $request, EntityManagerInterface $manager ) 
    {
        $form = $this->createForm(AdType::class, $ad);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $manager->persist($ad);
            $manager->flush();

            $this->addFlash(
                "success",
                "L'annonce {$ad->getTitle()} à bien été modifier"
            );
        }
        return $this->render('admin/ad/edit.html.twig',[
            'ad' => $ad,
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/Admin/ad/{id}/delete", name="admin_ads_delete")
     * 
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager) 
    {
        if(count($ad->getBookings()) > 0) {
            $this->addFlash(
                "warning",
                "Vous ne pouvez pas supprimer l'annonce {$ad->getTitle()} car elle possède déjà des reservations"
            );
        }else {
            $manager->remove($ad);
            $manager->flush();
    
            $this->addFlash(
                "success",
                "L'annonce {$ad->getTitle()} a bien été supprimée"
            );
        }
        $this->redirectToRoute('admin_ads_index');
    }
}
