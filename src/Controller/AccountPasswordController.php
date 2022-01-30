<?php

namespace App\Controller;

use App\Form\ChangePasswordType;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\PasswordHasher\Hasher\UserPasswordHasherInterface;
use Symfony\Component\Routing\Annotation\Route;

class AccountPasswordController extends AbstractController
{
    /**
     * @Route("/compte/password_modifier", name="account_password")
     */
    public function index(Request $request, UserPasswordHasherInterface $passwordHasher, EntityManagerInterface $entityManager): Response
    {
        $user = $this->getUser();
        $form = $this->createForm(ChangePasswordType::class, $user);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $old_pwd = $form->get('old_password')->getData();

            if($passwordHasher->isPasswordValid($user, $old_pwd)){
                $new_pwd = $form->get('new_password')->getData();

                $password = $passwordHasher->hashPassword($user, $new_pwd);
                $user->setPassword($password);

                $entityManager->flush($user);
                $this->addFlash('info', "Votre mot de passe a bien été mise à jour.");
                return $this->redirectToRoute('account');
            }else{
                $this->addFlash('danger', "votre mot de passe actuel n'est pas le bon");
            }
        }

        return $this->render('account/password.html.twig', ['form' => $form->createView()]);

    }
}
