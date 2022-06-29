<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SavonnerieController extends AbstractController
{
    /**
     * @Route("/savonnerie", name="savonnerie")
     */
    public function index(): Response
    {
        return $this->render('savonnerie/index.html.twig');
    }
}
