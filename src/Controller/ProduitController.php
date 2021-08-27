<?php

namespace App\Controller;


use App\Entity\Produit;
use App\Repository\CategoryRepository;
use App\Repository\CommandeRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProduitRepository;
use App\Service\Panier\PanierService;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;

class ProduitController extends AbstractController
{
    /**
     * La fonction catalogue() permet d'afficher les produits à vendre (FRONT-OFFICE)
     *
     * @Route("/", name="catalogue")
     */
    public function catalogue(ProduitRepository $repoProduit, CategoryRepository $categoryRepository, MatiereRepository $matiereRepository,Request $request)
    {

        /*
            Lorsqu'on créé une entity, est généré en même temps son Repository
            Repository : Requête SELECT

            Création de l'objet issu de la class ProduitRepository

            1e façon :
            On appelle la méthode getDoctrine() provenant de la class AbstractController
            suivi la methode getRepository() dans laquelle on défini comme argument la classe de l'entity voulue

            ----------------------------------
            2e façon :
            Appeler en argument de la fonction catalogue() la class suivi du nom de l'objet
            ==> c'est ce que l'on appelle UNE DEPENDANCE
        */

        // $repoProduit = $this->getDoctrine()->getRepository(Produit::class);



        if ($_POST):
            $cat=$request->request->get('categorie'); // valeur par defaut= 'all'
            $matiere=$request->request->get('matiere'); // valeur par defaut= 'all'
            $prix=$request->request->get('prixmax'); // valeur par defaut=1500

            if ($cat!=='all' && $matiere=='all' && $prix==1500):

                $produitsArray=$repoProduit->findBy(['category'=>$cat]);

            elseif ($cat=='all' && $matiere!=='all' && $prix==1500):

                $produitsArray=$repoProduit->findByMatiere($matiere);

            elseif ($cat=='all' && $matiere=='all' && $prix!==1500):

                $produitsArray=$repoProduit->findByPrix($prix);

            elseif ($cat!=='all' && $matiere!=='all' && $prix==1500):

                $produitsArray=$repoProduit->findBy(['matieres'=>$matiere, 'category'=>$cat]);

            elseif ($cat!=='all' && $matiere=='all' && $prix!==1500):

                $produitsArray=$repoProduit->findByPrixCategory($prix, $cat);

            elseif ($cat=='all' && $matiere!=='all' && $prix!==1500):

                $produitsArray=$repoProduit->findByPrixMatiere($prix, $matiere);

            elseif ($cat!=='all' && $matiere!=='all' && $prix!==1500):

                $produitsArray=$repoProduit->findByPrixCatMatiere($prix, $cat, $matiere);

            elseif ($cat=='all' && $matiere=='all' && $prix==1500):

                $produitsArray = $repoProduit->findAll();

            endif;

            $categories=$categoryRepository->findAll();
            $matieres=$matiereRepository->findAll();
            return $this->render('produit/catalogue.html.twig',[
                "produits" => $produitsArray,
                "categories"=>$categories,
                "matieres"=>$matieres
            ]);


        endif;


        $produitsArray = $repoProduit->findAll(); // SELECT * FROM produit
        $categories=$categoryRepository->findAll();
        $matieres=$matiereRepository->findAll();

        //dd($produitsArray);

        return $this->render('produit/catalogue.html.twig', [
            "produits" => $produitsArray,
            "categories"=>$categories,
            "matieres"=>$matieres
        ]);
    }


    /**
     * La fonction fiche_produit() permet d'afficher les données d'un produit
     *
     * @Route("/fiche_produit/{id}", name="fiche_produit")
     */
    public function fiche_produit(Produit $produit)
    {                           //($id, ProduitRepository $repoProduit)
        //dd($id);
        //$produit = $repoProduit->find($id); // SELECT * FROM produit WHERE id = $id

        // la méthode find() permet de faire une requête SELECT par le champ "id" de la table
        // Cette méthode a besoin d'un argument (integer)


        //dd($produit);
        /*

        Lorsqu'on fait une requête de selection, on ne récupère pas un tableau d'un produit [id, prix, image etc...]
        Ce qu'on récupère par la requête est UN OBJET
        
        */
        return $this->render('produit/fiche_produit.html.twig', [
            "produit" => $produit
        ]);
    }


    /**
     * @Route("/addCart/{id}/{param}", name="addCart")
     */
    public function addCart(PanierService $panierService, $id, $param)
    {
        $panierService->add($id);

        //dd($panier);

        $this->addFlash('success', 'le produit a bien été ajouté au panier');

        if ($param == 'catalogue'):
            return $this->redirectToRoute('catalogue');
        else:
            return $this->redirectToRoute('cart');
        endif;


    }

    /**
     * @Route("/removeCart/{id}", name="removeCart")
     */
    public function removeCart(PanierService $panierService, $id)
    {
        $panierService->remove($id);

        //dd($panier);
        $this->addFlash('success', 'le produit a bien été retiré au panier');
        return $this->redirectToRoute('cart');
    }

    /**
     * @Route("/deleteCart/{id}", name="deleteCart")
     */
    public function deleteCart(PanierService $panierService, $id)
    {
        $panierService->delete($id);

        //dd($panier);
        $this->addFlash('success', 'le produit a bien été supprimé du panier');
        return $this->redirectToRoute('cart');

    }


    /**
     * @Route("/cart", name="cart")
     */
    public function cart(PanierService $panierService)
    {
        $panier = $panierService->getFullPanier();


        return $this->render('produit/cart.html.twig', [
            'panier' => $panier
        ]);
    }


    /**
     * @Route("/recap", name="recap")
     */
    public function recap(CommandeRepository $repository)
    {
        $commandes = $repository->findBy(['user' => $this->getUser()]);


        return $this->render('produit/recap.html.twig', [
            'commandes' => $commandes
        ]);

    }


}
