<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderSuccessController extends AbstractController
{
    /**
     * @Route("/commande/merci/{stripeSessionId}", name="order_validate")
     */
    public function index($stripeSessionId, EntityManagerInterface $entityManager, Cart $cart)
    {

        $order = $entityManager->getRepository(Order::class)->findOneByStripeSessinId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        if($order->getState() == 0) {

            $cart->remove();

            //Modifier le statut isPid de notre commande rn mettan 1
            $order->setState(1);
            $entityManager->flush();

            //Envoyer un email à notre client pour lui confirmer sa commande
            $mail = new Mail();
            $content = "<h2>Bonjour ".$order->getUser()->getFirstname()."</h2><br>
            <p>Nous avons le plaisir de vous confirmer l’enregistrement de votre commande N° ".$order->getReference(). " à la date du ".$order->getCreateAt()->format('m/d/Y')." sur notre boutique savonneriedesadrets.com et nous vous remercions de votre confiance.</p>
            <p>Votre commande sera expédiée dans les meilleurs délais.</p>
            <p>Nous vous rappelons que vous pouvez à tout moment suivre l’évolution de votre commande dans votre espace privé sur notre site.</p>
            <p>Une Question ?<br>
            Nous vous répondons avec plaisir du mardi au samedi de 8h à 18h30, par téléphone au 06.46.76.01.83 ou par mail à contact@savonneriedesadrets.com</p>
            <p style='align-items: center'>Merci et à très bientôt sur www.savonneriedesadrets.com</p>";

            $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande est bien validée.', $content);

        }


        return $this->render('order_validate/index.html.twig', ['order' => $order]);
    }
}
