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
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Form\Extension\HttpFoundation\HttpFoundationRequestHandler;

class AdController extends AbstractController
{
    /**
     * Récupération de tous les appartements dans la bdd
     * @Route("/ad", name="ads_index")
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
     * @return Response
     * 
     */
    public function create(Request $request, EntityManagerInterface $manager) {
        $ad = new Ad();
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
     * @Route("/ads/{slug}/edit", name="ads_edit")
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
     * Permet d'afficher une seule annonce
     *
     * @Route("/ads/{slug}", name="ads_show")
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
