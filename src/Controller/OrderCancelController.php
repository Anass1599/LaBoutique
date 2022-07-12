<?php

namespace App\Controller;

use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderCancelController extends AbstractController
{

    /**
     * @Route("commande/erreur/{stripeSessionId}", name="order_cancel")
     */
    public function index($stripeSessionId, EntityManagerInterface $entityManager)
    {

        $order = $entityManager->getRepository(Order::class)->findOneByStripeSessinId($stripeSessionId);

        if (!$order || $order->getUser() != $this->getUser()) {
            return $this->redirectToRoute('home');
        }

        //Envoyer un email à notre client pour lui indique l'echec de paiement.

        return $this->render('order_cancel/index.html.twig', ["order" => $order]);

    }
}
