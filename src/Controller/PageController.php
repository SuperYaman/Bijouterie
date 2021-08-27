<?php

namespace App\Controller;// App = src

use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class PageController extends AbstractController // notre controller hérite de AbstractController qui contient ses méthodes
{

    /*
        Une route est "une page web" exemple: inscription.php panier.php
        en local : localhost:8000/
        en ligne : www.nomDeDomaine.fr/
    */

    /**
     * les annotations sont écrites dans des commentaires, elles sont "lues" par l'@
     * l'annotation route permet de définir l'url
     * 
     * 
     * 2 arguments :
     * 1er : la route (l'url)
     * 2e : le nom de la route (lien href avec la fonction path())
     * les 2 arguments doivent être placés entre DOUBLE QUOTE
     * 
     * @Route("/page", name="pageName")
     */

    public function page(): Response
    {

        $ageController = 10; // création d'une variable avec une valeur associée

        //dump($ageController);
        //dump($ageController);die; // la lecture du code s'arrête au die
        //dd($ageController);
        
        return $this->render('page/page.html.twig' , [
            // key => value
            // nom de la variable passée sur twig   =>  valeur (variable/tableau/valeur) côté controller
            "ageTwig" => $ageController
        ]);


        // la méthode render() qui provient de AbstractController permet de relier une fonction à une vue (template)
        // 2 arguments :

        // 1e argument (obligatoire) est le fichier html.twig
        // les fichiers appelés dans la méthode render() se trouvent forcément dans le dossier "templates" donc inutile de le préciser
        // il a écrit dans config/package/twig.yaml

        // 2e argument (facultatif) est un tableau
    }



    /**
     * La fonction accueil() est la page pincipale du site 
     * 
     * @Route("/accueil", name="accueil")
     */
    public function accueil()
    {
        return $this->render("page/accueil.html.twig");
    }









} // Fermeture de la class (rien en dessous)
