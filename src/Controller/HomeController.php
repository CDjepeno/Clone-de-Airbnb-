<?php
namespace App\Controller;

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;


class HomeController extends AbstractController {
    
    /**
     * Montre la page qui dit bonjour
     * 
     * @Route("/hello/{prenom}/{age}", name="hello")
     * @Route("/hello", name="hello_base")
     * @Route("/salut/{prenom}", name="hello_prenom")
     *
     * @param [type] $nom
     * @return void
     */
    public function hello($prenom = "anonyme",$age = " 0") {
        return $this->render(
            "hello.html.twig",
            [
                'prenom' => $prenom,
                'age' => $age
            ]
            );
    }

    /**
     *
     * @Route("/", name="homepage")
     */
    public function home() {
        $prenoms = ['lior'=>'chamla','steeve'=>'perichon','marcel'=>'desailly'];

        return $this->render(
           'home.html.twig',
            [
               'title' => "Bonjour tous le monde",
               'age' => 29,
               'tableau' => $prenoms
            ]
        );
    }
}
?>