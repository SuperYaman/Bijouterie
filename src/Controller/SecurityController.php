<?php

namespace App\Controller;

use App\Entity\User;
use App\Form\RegistrationType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Security\Core\Encoder\UserPasswordEncoderInterface;

class SecurityController extends AbstractController
{

    /**
     * @Route("/registration",name="registration")
     */
    public function registration(Request $request, EntityManagerInterface $manager, UserPasswordEncoderInterface $encoder)
    {

        $user = new User();

        $form = $this->createForm(RegistrationType::class, $user);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()):

            $mdp = $encoder->encodePassword($user, $request->request->get('registration')['password']);
            $user->setPassword($mdp);
            $manager->persist($user);
            $manager->flush();
            $this->addFlash('success', 'Félicitation! votre inscription s\'est bien déroulée. Connectez vous à présent');

            return $this->redirectToRoute('login');
        endif;


        return $this->render('security/registration.html.twig', [
            'form' => $form->createView()
        ]);
    }

    /**
     * @Route("/login", name="login")
     */
    public function login()
    {

        return $this->render('security/login.html.twig');
    }


    /**
     * @Route("/logout", name="logout")
     */
    public function logout()
    {

    }




}
