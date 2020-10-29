<?php 

namespace App\Service;

use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\RequestStack;
use Twig\Environment;

/**
 * Class de pagination qui extrait toute notion de calcul et de récupération de données de nos controllers
 * 
 * Elle nécessite après instanciation qu'on lui pas l'entité sur laquelle ont souhaite travailler
 */
class PaginationService {
    /**
     * Le nom de l'entitité sur laquelle on veut effectuer une pagination
     *
     * @var [string]
     */
    private $entityclass;

    /**
     * Le nombre d'enregistrement à récupérer
     *
     * @var integer
     */
    private $limit = 10;

    /**
     * La page sur laquelle on se trouve actuellement
     *
     * @var integer
     */
    private $currentPage = 1;

    /**
     * La manager de Doctrine qui nous permet notament de trouver le repository dont on a besoin
     *
     * @var EntityManagerInterface
     */
    private $manager;

    /**
     * Le nom de la route qu l'on veut
     *
     * @var [string]
     */
    private $route;

    /**
     * le chemin vers le template qui contient la pagination
     */
    private $templatePath;

    /**
     * Contructeur du service de pagination qui sera appelé par symfony
     * 
     * Ne pas oublier de configurer votre fichier services.yaml afin que symfony sache quelle valeur
     * utiliser pour le $templatePath
     *
     * @param EntityManagerInterface $manager
     * @param Environment $twig
     * @param RequestStack $request
     * @param [string] $templatePath
     */
    public function __construct(EntityManagerInterface $manager, Environment $twig, RequestStack $request, $templatePath) {
        // On récupère le nom de la route à utiliser à partir des attributs de la requête actuelle
        $this->route        = $request->getCurrentRequest()->attributes->get('_route');
        $this->manager      = $manager;
        $this->twig         = $twig;
        $this->templatePath = $templatePath;
        
        return $this;
    }

    public function setRoute($route) {
        $this->route = $route;

        return $this;
    }

    public function getRoute() {
        return $this->route;
    }

    /**
     * Permet d'afficher le rendu de la navigation au sein d'un template twig!
     *                                                                               
     * @return void
     */
    public function display() {
        $this->twig->display($this->templatePath,[
            'page' => $this->currentPage,
            'pages' => $this->getPages(),
            'route' => $this->route
        ]);
    }

    /**
     * Récupère les donner dans la bdd avec une limite donnée
     *
     * @return void
     */
    public function getdata() {
        if(empty($this->entityClass)) {
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass() de votre objet PaginationService !");
        }
        // 1) Calculer l'offset
        $offset = $this->currentPage * $this->limit - $this->limit;
        
        // 1) Demander au repository de trouver les éléments
        $repo = $this->manager->getRepository($this->entityclass);
        $data = $repo->findBy([],[],$this->limit, $offset);

        // 1) Renvoyer les éléments en questions
        return $data;
    }

    /**
     * Permet de récuprer le nombre de pages qui existent sur une entité particulière
     *
     * @trows Exception si la propriété $entitityClass n'est pas configuée
     * 
     * @return int
     * 
     */
    public function getpages() {
        if(empty($this->entityClass)) {
            // Si il n'y a pas d'entité configurée, on ne peut pas charger le repository
            // la fonction ne peut pas continuer !!
            throw new \Exception("Vous n'avez pas spécifié l'entité sur laquelle nous devons paginer ! Utilisez la méthode setEntityClass( de votre paginationService");
        }
        //  Calculer  le total
        $repo = $this->manager->getRepository($this->entityclass);
        $total = count($repo->findAll());

        $pages = ceil($total / $this->limit);

        return $pages;
    }
  

    /**
     * Get the value of entityclass
     */ 
    public function getEntityclass()
    {
        return $this->entityclass;
    }

    /**
     * Set the value of entityclass
     *
     * @return  self
     */ 
    public function setEntityclass($entityclass)
    {
        $this->entityclass = $entityclass;

        return $this;
    }

  
    /**
     * Get the value of limit
     */ 
    public function getLimit()
    {
        return $this->limit;
    }

    /**
     * Set the value of limit
     *
     * @return  self
     */ 
    public function setLimit($limit)
    {
        $this->limit = $limit;

        return $this;
    }

    /**
     * Get the value of currentPage
     */ 
    public function getPage()
    {
        return $this->currentPage;
    }

    /**
     * Set the value of currentPage
     *
     * @return  self
     */ 
    public function setPage($currentPage)
    {
        $this->currentPage = $currentPage;

        return $this;
    }

    /**
     * Get the value of templatePath
     */ 
    public function getTemplatePath()
    {
        return $this->templatePath;
    }

    /**
     * Set the value of templatePath
     *
     * @return  self
     */ 
    public function setTemplatePath($templatePath)
    {
        $this->templatePath = $templatePath;

        return $this;
    }
}

























?>