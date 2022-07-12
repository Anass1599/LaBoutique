<?php

namespace App\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class ConditionsController extends AbstractController
{
    /**
     * @Route("/conditions/mentions_legale", name="condition_mentions_legale")
     */
    public function mentions_legale()
    {
        return $this->render('conditions/mentions_legale.html.twig');
    }

    /**
     * @Route("/conditions/generales_de_vente", name="condition_generales_de_vente")
     */
    public function generales_de_vente()
    {
        return $this->render('conditions/generales_de_vente.html.twig');
    }
}
