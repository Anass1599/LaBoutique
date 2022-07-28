<?php

namespace App\Controller;

use App\Classe\Mail;
use App\Form\ContactType;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ContactController extends AbstractController
{
    /**
     * @Route("/contact", name="contact")
     */

    public function index(Request $request)
    {
        $form = $this->createForm(ContactType::class);
        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid()) {

            $this->addFlash('info', 'Merci de nous avoir contacté. Notre équipe va vous répondre dans les meilleurs délais.');

            $content = $form->get('nom')->getData().'<br/>'.$form->get('prenom')->getData().'<br/>'.$form->get('email')->getData().'<br/><br/>'.$form->get('content')->getData();
            $mail = new Mail();
            $mail->send('anass.moujahid@lapiscine.pro', 'Anass', 'info', $content);

        }

        return $this->render('contact/index.html.twig', ['form' => $form->createView()]);
    }
}
