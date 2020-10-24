<?php

namespace App\Controller;

use App\Entity\Ad;
use App\Form\AdType;
use App\Entity\Image;
use App\Repository\AdRepository;
use Monolog\Handler\HandlerInterface;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Security;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\IsGranted;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;

class AdController extends AbstractController
{
    /**
     * Récupération de tous les appartements dans la bdd
     * 
     * @Route("/ad", name="ads_index")
     * 
     * @param AdRepository $repo
     * 
     * @return Response
     */
    public function index(AdRepository $repo)
    {
        $ads = $repo->findAll();

        return $this->render('ad/index.html.twig', [
            'ads' => $ads,
            ]);
    }


    /**
     * Permet de crée une annonce
     * 
     * @Route("/ads/new", name="ads_create")
     * 
     * @IsGranted("ROLE_USER", message="vous ne pouvez pas crée d'annonce vous n'ètes pas un utilisateur")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {
        $ad   = new Ad();
        $form = $this->createForm(AdType::class, $ad);
        
        $form->handleRequest($request);
        
        if($form->isSubmitted() && $form->isValid()) {
            
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }
            // Récupération de l'utilisateur
            $ad->setAuthor($this->getUser());

            $manager->persist($ad); 
            $manager->flush($ad); 
            
            $this->addFlash(
                'success',
                'Votre annonce a bien été ajouter!'
            );
            
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
                ]);
            }               
        return $this->render('ad/new.html.twig',[
            'form' => $form->createView()
        ]);
    }

    /**
     * Permet de modifier une annonce
     * 
     * @Route("/ads/{slug}/edit", name="ads_edit")
     * 
     * @Security("is_granted('ROLE_USER') and user === ad.getAuthor()", message="Cette annonce ne vous appartient pas, vous ne pouvez pas la modifier")
     * @param Request $request
     * @param EntityManagerInterface $manager
     * @param Ad $ad
     * 
     * @return Response
     */
    public function edit(Request $request, Ad $ad, EntityManagerInterface $manager) {

        $form = $this->createForm(AdType::class, $ad);
        
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {
            
            foreach($ad->getImages() as $image) {
                $image->setAd($ad);
                $manager->persist($image);
            }

            $manager->persist($ad); 
            $manager->flush($ad); 
            
            $this->addFlash(
                'success',
                'Votre annonce à bien été modifiée!'
            );
            
            return $this->redirectToRoute('ads_show', [
                'slug' => $ad->getSlug()
                ]);
            }               

        return $this->render('ad/edit.html.twig',[
            "form" => $form->createView(),
            "ad" => $ad
        ]);
    }

    /**
     * Permet de supprimer une annonce
     *
     * @Route("/ad/{slug}/delete", name="ads_delete")
     * 
     * @Security("is_granted('ROLE_USER') and user == ad.getAuthor()", message="vous n'avez pas le droit d'acceder a cette recource")
     * @param Ad $ad
     * @param EntityManagerInterface $manager
     * 
     * @return Response
     */
    public function delete(Ad $ad, EntityManagerInterface $manager) {
    
        $manager->remove($ad);
        $manager->flush();

        $this->addFlash(
            'success',
            'Votre annonce à bien été supprimée!'
        );

        return $this->redirectToRoute("ads_index");
    }


    /**
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
     * 
     * @param [type] $slug
     *
     * @return Response
     */
    public function show(Ad $ad) {
        return $this->render('ad/show.html.twig',[
            'ad' => $ad,
            ]);
    }




}
