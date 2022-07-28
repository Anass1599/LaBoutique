<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Entity\ResetPassword;
use App\Entity\User;
use App\Form\ResetPasswordType;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class ResetPasswordController extends AbstractController
{
    /**
     * @Route("/mot-de-passe-oublie", name="reset_password")
     *
     */
    public function index(Request $request, EntityManagerInterface $entityManager)
    {
        if ($this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($request->get('email')) {

            $user = $entityManager->getRepository(User::class)->findOneByEmail($request->get('email'));

            if($user) {

                //1 : Enregistrer en base la demande de reset_password avec user, token, createAT.
                $reset_password = new ResetPassword();
                $reset_password->setUser($user);
                $reset_password->setToken(uniqid());
                $reset_password->setCreateAT(new \DateTime());
                $entityManager->persist($reset_password);
                $entityManager->flush();

                //2 : Envoyer un email à l'utitisateur avec un lien

                $url = $this->generateUrl('update_password', [
                    'token' => $reset_password->getToken()]
                );

                $content ="bonjour ".$user->getFirstname()."<br/>Vous avez demander à réinitiatiser votre mot de passe.<br/><br/>";
                $content .= "Merci de bien vouloir clique sur le lien suivant pour <a href='".$url."'>mettre à jour votre mot de passe.</a>";

                $mail = new Mail();
                $mail->send($user->getEmail(), $user->getFirstname().' '.$user->getLastname(), 'Réinitialiser votre mot de passe', $content );
                $this->addFlash('info', 'Vous allez recevoir dans quelques secondes un mail avec un lien pour réinitialiser votre mot de passe.');

            } else {
                $this->addFlash('danger', 'Cette adresse email est inconnue.');
            }
        }

        return $this->render('reset_password/index.html.twig');
    }

    /**
     *  @Route("/modifie-mot-de-passe/{token}", name="update_password")
     */
    public function update($token, EntityManagerInterface $entityManager, Request $request, UserPasswordHasherInterface $passwordHasher)
    {

        $reset_password = $entityManager->getRepository(ResetPassword::class)->findOneByToken($token);

        if (!$reset_password) {
            return $this->redirectToRoute('reset_password');
        }

        $now = new \DateTime();
        if($now > $reset_password->getCreateAt()->modify('+ 1 hour')) {

            $this->addFlash('danger', 'Votre demande de mot de passe a expiré. Merci de la renouveller.');
            return $this->redirectToRoute('reset_password');
        }

        //Rendre une vue avec mot de passe et confirmez votre mot de passe.
        $form = $this->createForm(ResetPasswordType::class);
        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()) {

            $new_pwd = $form->get('new_password')->getData();

            //Encodage des mot de passe.
            $password = $passwordHasher->hashPassword($reset_password->getUser(), $new_pwd);
            $reset_password->getUser()->setPassword($password);

            // Flush en base de donne.
            $entityManager->flush();

            //redirection de l'utilisateur.
            $this->addFlash('info', 'Votre mot de passe a bien été mis à jour.');
            return $this->redirectToRoute('app_login');

        }
        return $this->render('reset_password/update.html.twig', ['form' => $form->createView()]);


    }
}
