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
               $content = "Bonjours ".$user->getFirstname()."<br/>What is Lorem Ipsum?Lorem Ipsum is simply dummy text of the printing and typesetting industry. Lorem Ipsum has been the industry's standard dummy text ever since the 1500s, when an unknown printer took a galley of type and scrambled it to make a type specimen book. It has survived not only five centuries, but also the leap into electronic typesetting, remaining essentially unchanged. It was popularised in the 1960s with the release of Letraset sheets containing Lorem Ipsum passages, and more recently with desktop publishing software like Aldus PageMaker including versions of Lorem Ipsum.";
               $mail->send($user->getEmail(), $user->getFirstname(), 'boniours', $content);

           } else {

               $notification = "L'email que vous avez renseigné existe déjà.";

           }


        }

        return $this->render('register/index.html.twig', ['form' => $form->createView(), 'notification'=> $notification]);
    }
}
