<?php

namespace App\Controller;

use App\Repository\Header1Repository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class SavonnerieController extends AbstractController
{
    /**
     * @Route("/savonnerie", name="savonnerie")
     */
    public function index(Header1Repository $header1Repository)
    {
        $headers = $header1Repository->findAll();
        return $this->render('savonnerie/index.html.twig' , ['headers' => $headers]);
    }
}
