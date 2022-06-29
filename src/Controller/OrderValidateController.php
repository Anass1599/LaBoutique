<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderValidateController extends AbstractController
{
    #[Route('/order/validate', name: 'order_validate')]
    public function index(): Response
    {
        return $this->render('order_validate/index.html.twig', [
            'controller_name' => 'OrderValidateController',
        ]);
    }
}
