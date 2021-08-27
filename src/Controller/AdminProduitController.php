<?php

namespace App\Controller;

use App\Entity\Achat;
use App\Entity\Category;
use App\Entity\Commande;
use App\Entity\Matiere;
use App\Entity\Produit;
use App\Form\CategoryType;
use App\Form\MatiereType;
use App\Form\ProduitType;
use App\Repository\CategoryRepository;
use App\Repository\CommandeRepository;
use App\Repository\MatiereRepository;
use App\Repository\ProduitRepository;
use App\Repository\UserRepository;
use App\Service\Panier\PanierService;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class AdminProduitController extends AbstractController
{
    /*
        La class AdminProduitController contient les routes du CRUD de produit
        CRUD : Create (INSERT INTO) / Read (SELECT) / Update / Delete
        Routes :
        /gestion_produit/afficher      name="produit_afficher"
        /gestion_produit/ajouter       name="produit_ajouter"
        /gestion_produit/modifier      name="produit_modifier"
        /gestion_produit/supprimer     name="produit_supprimer"

    */


    /**
     * La fonction produit_afficher() permet d'afficher sous forme de tableau la liste des produits (BACK OFFICE)
     * Sur ligne du tableau on y trouve les routes de la modification et de la suppression des produits
     * Également, on y trouve la route pour ajouter un produit
     *
     * @Route("/gestion_produit/afficher", name="produit_afficher")
     */
    public function produit_afficher(ProduitRepository $repoProduit)
    {
        $produitsArray = $repoProduit->findAll();
        //dd($produitsArray);

        return $this->render('admin_produit/produit_afficher.html.twig', [
            "produits" => $produitsArray
        ]);
    }


    /**
     * La fonction produit_ajouter() permet d'ajouter un produit (BACK OFFICE)
     * Besoin d'un formulaire
     *
     * @Route("/gestion_produit/ajouter", name="produit_ajouter")
     */
    public function produit_ajouter(Request $request, EntityManagerInterface $manager)
    {
        // MVC :
        // Model :
        // Entity (Table en BDD)
        // Repository (SELECT)
        // EntityManagerInterface (INSERT INTO, UPDATE, DELETE)
        // methode persist() INSERT INTO et UPDATE
        // methode remove() DELETE
        // methode flush() envoyer la requête en bdd


        // Pour ajouter un produit, on a besoin de créer une nouvelle instance issu de la class Produit
        $produit = new Produit;

        //dump($produit);// on observe qu'on retrouve toutes les propriétés qui se trouvent dans l'entity Produit à valeur null

        /*
            Pour créer un formulaire, on utilise la méthode createForm() provenant de AbstractController
            2 arguments obligatoires :
            1e : class ProduitType (formulaire créé avec builder)
            2e : objet de la class Produit ($produit)

            3e (facultatif) : tableau = option
        */

        $form = $this->createForm(ProduitType::class, $produit, array('ajouter' => true));
        // $form est un objet (qui contient des méthodes)


        $form->handleRequest($request); // traitement du formulaire

        // si le formulaire a été soumis (clic sur le bouton "Ajouter" : type="submit")
        // et si le formulaire est valide (contraintes : il respecte toutes les conditions : ex: prix, titre non vides)
        if ($form->isSubmitted() && $form->isValid()) {
            //dump($produit);

            //dd($produit->getTitre());

            $imageFile = $form->get('image')->getData();
            // $imageFile est soit un objet soit null
            //dd($imageFile);


            if ($imageFile) // si $imageFile n'est pas vide/null (une image a été upload/chargée)
            {
                // Traitement de l'image
                // 3 étapes

                // 1e étape : renommer le nom de l'image

                $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();
                //dd($nomImage);


                // 2e étape : Envoyer l'image dans le dossier public / images / imagesUpload

                // move() permet de déplacer le fichier
                // 2 arguments :
                // 1e : emplacement : getParameter()        // où déplacer l'image ???
                // 2e : le nom du fichier qu'on déplace      // sous quel nom ???

                $imageFile->move(
                    $this->getParameter("image_produit"),
                    $nomImage
                );

                /*
                    la méthode getParameter() renvoie sur le fichier config/services.yaml aux paramètres (ligne 6)
                    les paramètres définissent un emplacement 
                    1 argument : le nom du paramètre
                */


                // 3e étape : envoyer le nom de l'image en bdd

                $produit->setImage($nomImage);


            }// fermeture de la condition $imageFile


            $produit->setDateAt(new \DateTimeImmutable('now'));

            //dump($produit);
            //

            $manager->persist($produit); // on défini ce qu'on envoie
            $manager->flush(); // envoie de l'objet $produit en BDD dans la table Produit

            //dd($produit);


            // notification 
            // methode addFlash() provenant de AbstractController qui permet de véhiculer sur le twig un message
            // 2 arguments :
            // 1e : le nom du flash
            // 2e : le message

            $this->addFlash('success', "Le produit N° " . $produit->getId() . " a bien été ajouté");


            // Redirection
            // methode redirectToRoute() de AbstractController
            // 2 arguments :
            // 1e obligatoire : name de la route
            // 2e facultatif : tableau


            return $this->redirectToRoute("produit_afficher");
        }


        return $this->render('admin_produit/produit_ajouter.html.twig', [
            "formProduit" => $form->createView() // méthode permettant de créer la vue du formulaire 
        ]);
    }


    /**
     * @Route("/gestion_produit/supprimer/{id}", name="produit_supprimer")
     */
    public function produit_supprimer(Produit $produit, EntityManagerInterface $manager)
    {
        // si $produit a une image alors il faut également la supprimer
        if ($produit->getImage()) // si l'image n'est pas null
        {
            //  unlink($this->getParameter("image_produit") . "/" . $produit->getImage());
            //unlink() permet de supprimer un fichier
            // 1e argument : le chemin jusqu'au fichier
        }

        $titre = $produit->getTitre();
        // suppression
        $manager->remove($produit);
        $manager->flush();

        // notification
        $this->addFlash("success", "Le produit $titre a bien été supprimé");


        // redirection
        return $this->redirectToRoute("produit_afficher");

    }


    /**
     * @Route("/gestion_produit/modifier/{id}", name="produit_modifier")
     */
    public function produit_modifier(Produit $produit, Request $request, EntityManagerInterface $manager)
    {
        $form = $this->createForm(ProduitType::class, $produit, array('modifier' => true));

        $form->handleRequest($request); // traitement du formulaire

        // si le formulaire a été soumis (clic sur le bouton "Ajouter" : type="submit")
        // et si le formulaire est valide (contraintes : il respecte toutes les conditions : ex: prix, titre non vides)
        if ($form->isSubmitted() && $form->isValid()) {
            //dump($produit);

            //dd($produit->getTitre());

            $imageFile = $form->get('imageFile')->getData();
            // $imageFile est soit un objet soit null
            //dd($imageFile);


            if ($imageFile) // si $imageFile n'est pas vide/null (une image a été upload/chargée)
            {
                // Traitement de l'image
                // 3 étapes

                // 1e étape : renommer le nom de l'image

                $nomImage = date("YmdHis") . "-" . uniqid() . "-" . $imageFile->getClientOriginalName();
                //dd($nomImage);


                // 2e étape : Envoyer l'image dans le dossier public / images / imagesUpload

                // move() permet de déplacer le fichier
                // 2 arguments :
                // 1e : emplacement : getParameter()        // où déplacer l'image ???
                // 2e : le nom du fichier qu'on déplace      // sous quel nom ???

                $imageFile->move(
                    $this->getParameter("image_produit"),
                    $nomImage
                );

                /*
                    la méthode getParameter() renvoie sur le fichier config/services.yaml aux paramètres (ligne 6)
                    les paramètres définissent un emplacement
                    1 argument : le nom du paramètre
                */


                // 3e étape : envoyer le nom de l'image en bdd

                $produit->setImage($nomImage);


            }// fermeture de la condition $imageFile


            //dump($produit);
            //

            $manager->persist($produit); // on défini ce qu'on envoie
            $manager->flush(); // envoie de l'objet $produit en BDD dans la table Produit

            //dd($produit);


            // notification
            // methode addFlash() provenant de AbstractController qui permet de véhiculer sur le twig un message
            // 2 arguments :
            // 1e : le nom du flash
            // 2e : le message

            $this->addFlash('success', "Le produit N° " . $produit->getId() . " a bien été modifié");


            // Redirection
            // methode redirectToRoute() de AbstractController
            // 2 arguments :
            // 1e obligatoire : name de la route
            // 2e facultatif : tableau


            return $this->redirectToRoute("produit_afficher");
        }


        return $this->render("admin_produit/produit_modifier.html.twig", [
            "formProduit" => $form->createView(),
            "produit" => $produit
        ]);
    }

    /**
     * @Route("/commande", name="commande")
     */
    public function commande(EntityManagerInterface $manager, PanierService $panierService)
    {

        $panier = $panierService->getFullPanier();

        $commande = new Commande();

        $commande->setDate(new \DateTime());
        $commande->setTotal($panierService->getTotal());
        $commande->setUser($this->getUser());


        foreach ($panier as $lignePanier):
            $achat = new Achat();
            $achat->setProduit($lignePanier['produit']);
            $achat->setTotal($lignePanier['produit']->getPrix() * $lignePanier['quantite']);
            $achat->setQuantite($lignePanier['quantite']);
            $achat->setCommande($commande);
            $manager->persist($achat);
            $panierService->delete($lignePanier['produit']->getId());
        endforeach;
        $manager->persist($commande);
        $manager->flush();
        $this->addFlash('success', 'Votre commande a bien été prise en compte, merci pour votre achat');


        return $this->redirectToRoute('recap', [
        ]);
    }

    /**
     * @Route("/recapAdmin", name="recapAdmin")
     */
    public function recapAdmin(CommandeRepository $repository)
    {
        $commandes = $repository->findAll();

        return $this->render('admin_produit/recapAdmin.html.twig', [
            'commandes' => $commandes
        ]);
    }

    /**
     * @Route("/modifCategory/{id}", name="modifCategory")
     * @Route("/addCategory", name="addCategory")
     *
     */
    public function addCategory(Request $request, EntityManagerInterface $manager, CategoryRepository $repository, $id = null)
    {


        if ($id == null):
            $category = new Category();
        else:
            $category = $repository->find($id);
        endif;

        $categories = $repository->findAll();


        $form = $this->createForm(CategoryType::class, $category);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):

            $manager->persist($category);
            $manager->flush();

            if ($id == null):
                $this->addFlash('success', 'La catégorie a bien été créée');
            else:
                $this->addFlash('success', 'La catégorie a bien été modifiée');
            endif;

            return $this->redirectToRoute('addCategory');

        endif;

        return $this->render("admin_produit/addCategory.html.twig", [
            'formu' => $form->createView(),
            'categories' => $categories
        ]);
    }

    /**
     * @Route("/modifMatiere/{id}", name="modifMatiere")
     * @Route("/addMatiere", name="addMatiere")
     */
    public function addMatiere(Request $request, EntityManagerInterface $manager, MatiereRepository $repository, $id = null)
    {

        if ($id == null):
            $matiere = new Matiere();
        else:
            $matiere = $repository->find($id);
        endif;

        $matieres = $repository->findAll();

        $form = $this->createForm(MatiereType::class, $matiere);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):
            $manager->persist($matiere);
            $manager->flush();
            if ($id == null):
                $this->addFlash('success', 'La matière a bien été créée');
            else:
                $this->addFlash('success', 'La matière a bien été modifiée');
            endif;

            return $this->redirectToRoute('addMatiere');
        endif;

        return $this->render("admin_produit/addMatiere.html.twig", [
            'formu' => $form->createView(),
            'matieres' => $matieres
        ]);
    }


    /**
     * @Route("/deleteMatiere/{id}", name="deleteMatiere")
     */
    public function deleteMatiere(EntityManagerInterface $manager, Matiere $matiere)
    {

        $manager->remove($matiere);
        $manager->flush();
        $this->addFlash('success', 'La matière a bien été supprimée');

        return $this->redirectToRoute('addMatiere', [
        ]);
    }

    /**
     * @Route("/deleteCategory/{id}", name="deleteCategory")
     */
    public function deleteCategory(EntityManagerInterface $manager, Category $category)
    {

        $manager->remove($category);
        $manager->flush();
        $this->addFlash('success', 'La catégorie a bien été supprimée');

        return $this->redirectToRoute('addCategory', [
        ]);
    }

    /**
     * @Route("/sendMail", name="sendMail")
     */
    public function sendMail(Request $request)
    {
        $transporter = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
            ->setUsername('dorancosalle220821@gmail.com')
            ->setPassword('Doranco@salle22');

        $mailer = new \Swift_Mailer($transporter);

        $mess = $request->request->get('message');
        $from = $request->request->get('email');
        $objectif = $request->request->get('need');


        $message = (new \Swift_Message($objectif))
            ->setFrom($from)
            ->setTo('dorancosalle220821@gmail.com');

        $cid = $message->embed(\Swift_Image::fromPath('images/imageDefault.jpg'));
        $message->setBody(
            $this->renderView('mail/mailTemplate.html.twig', [
                'from' => $from,
                'message' => $mess,
                'logo' => $cid,
                'subject' => $objectif,
                'objectif' => 'Accéder au site',
                'liens' => 'https://127.0.0.1:8000'
            ]),
            'text/html'
        );

        $mailer->send($message);


        return $this->redirectToRoute('catalogue', [
        ]);
    }


    /**
     * @Route("/contact", name="contact")
     */
    public function contact()
    {

        return $this->render('mail/mailForm.html.twig', [
        ]);
    }

    /**
     * @Route("/forgotPassword", name="forgotPassword")
     */
    public function forgotPassword(Request $request, EntityManagerInterface $manager, UserRepository $repository)
    {

        if ($_POST):
            $email = $request->request->get('email');
            $user = $repository->findOneBy(['email' => $email]);

            if ($user):

                $transporter = (new \Swift_SmtpTransport('smtp.gmail.com', 465, 'ssl'))
                    ->setUsername('dorancosalle220821@gmail.com')
                    ->setPassword('Doranco@salle22');

                $mailer = new \Swift_Mailer($transporter);


                $message = (new \Swift_Message('Réinitialisation de mot de passe'))
                    ->setFrom('dorancosalle220821@gmail.com')
                    ->setTo($email);

                $token = uniqid(); // on génère un clé aléatoire que l'on va inserer en BDD qui nous permetra d'authentifier l'utilisateur après avoir cliqué sur son lien de réinitialisation

                $user->setReset($token);
                $manager->persist($user);
                $manager->flush();

                $cid = $message->embed(\Swift_Image::fromPath('images/imageDefault.jpg'));
                $message->setBody(
                    $this->renderView('mail/mailTemplate.html.twig', [
                        'from' => 'dorancosalle220821@gmail.com',
                        'message' => 'Vous avez fait une demande de réinitialisation de mot de passe, veuillez  cliquer sur le bouton ci-dessous pour y procéder',
                        'logo' => $cid,
                        'subject' => 'Réinitialisation de mot de passe',
                        'objectif' => 'Réinitialiser',
                        'liens' => 'http://127.0.0.1:8000/resetToken/' . $user->getId() . '/' . $token
                    ]),
                    'text/html'
                );

                $mailer->send($message);

                $this->addFlash('success', 'Un mail de réinitialisation vous a été adressé sur votre boite mail');

                return $this->redirectToRoute('catalogue', [
                ]);


            else:
                $this->addFlash('error', 'Aucun compte n\'existe à cette adresse mail');
                return $this->redirectToRoute('forgotPassword');
            endif;

        endif;

        return $this->render('security/forgotPassword.html.twig', [
        ]);
    }


    /**
     * @Route("/resetToken/{id}/{token}", name="resetToken")
     */
    public function resetToken($id, $token, UserRepository $repository)
    {

        $user = $repository->findOneBy(['id' => $id, 'reset' => $token]);

        if ($user):
            return $this->render('security/resetPassword.html.twig', ['id' => $id]);

        else:
            $this->addFlash('error', 'Une erreur s\'est produite, veuillez refaire une demande de réinitialisation de mot de passe');
            return $this->redirectToRoute('forgotPassword');
        endif;

    }

    /**
     * @Route("/resetPassword", name="resetPassword")
     */
    public function resetPassword(Request $request, UserRepository $repository, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        if ($_POST):

            //dd($_POST);
            $password = $request->request->get('password');
            $confirmPassword = $request->request->get('confirmPassword');
            $id = $request->request->get('id');

            if ($password == $confirmPassword):
                $user=$repository->find($id);
                $mdp=$encoder->encodePassword($user, $password);
                $user->setPassword($mdp);
                $user->setReset(null);
                $manager->persist($user);
                $manager->flush();
                $this->addFlash('success', 'votre mot de passe a bien été réinitialisé, connectez  vous à présent');
                return $this->redirectToRoute('login');

            else:

                $this->addFlash('error', 'Vos mots de passe ne correspondent pas');
                return $this->render('security/resetPassword.html.twig',['id'=>$id]);
            endif;

        endif;

        return $this->render('security/resetPassword.html.twig', [
        ]);
    }


}
