<?php

namespace App\Controller;

use App\Repository\AdRepository;
use App\Repository\UserRepository;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class HomeController extends AbstractController {

    /**
     * Permet d'allez a la page d'acceuil
     * 
     * @Route("/", name="homepage")
     *
     * @return Response
     */
    public function home(AdRepository $adrepo, UserRepository $usrepo) {
        $a = $adrepo->findBestAds();
        $u = $usrepo->findBestUsers();
      
        return $this->render('home.html.twig',[
            "ads" => $adrepo->findBestAds(),
            "users" => $usrepo->findBestUsers()
        ]);
    }
}