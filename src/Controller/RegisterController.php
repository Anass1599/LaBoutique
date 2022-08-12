<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\User;
use App\Form\RegisterType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class RegisterController extends AbstractController
{
    /**
     * @Route("/inscription", name="register")
     */
    public function index(Request $request, EntityManagerInterface $entityManager, UserPasswordHasherInterface $passwordHasher): Response
    {
        $notification = null;

        $user = new User();
        $form = $this->createForm( RegisterType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){

           $user = $form->getData();

           $search_email = $entityManager->getRepository(User::class)->findOneByEmail($user->getEmail());

           if (!$search_email) {

               $password = $passwordHasher->hashPassword($user, $user->getPassword());
               $user->setPassword($password);
               $entityManager->persist($user);
               $entityManager->flush($user);
               $notification ="Votre inscription s'est  correctemment déroulée. Vous pouvez dés présent vous connecter à votre compte";

               $mail = new Mail();
               $content = "<h3>Bonjour ".$order->getUser()->getFirstname().",</h3>
                           <div style='text-align: center'>
                             <h3 style='color: #0c4a6e'>Bienvenue à la Savonnerie des Adrets !</h3>
                             <p>
                             Votre inscription a bien été prise en compte.<br/>
                             Vous pouvez dès à présent vous connecter avec vos identifiants et commander nos produits.
                             </p><br/><br/>
                           </div>
                           <h4 style='color: #0c4a6e'>Une Question ?</h4>
                           <p>
                           Nous vous répondons avec plaisir du mardi au samedi de 8h à 18h30, par téléphone au 06.46.76.01.83 ou par mail à contact@savonneriedesadrets.com<br/><br/>
                           <div style='text-align: center'>Merci et à très bientôt sur www.savonneriedesadrets.com</div>
                           </p>"
               ;
               $mail->send($user->getEmail(), $user->getFirstname(), 'boniours', $content);

           } else {

               $notification = "L'email que vous avez renseigné existe déjà.";

           }


        }

        return $this->render('register/index.html.twig', ['form' => $form->createView(), 'notification'=> $notification]);
    }
}
