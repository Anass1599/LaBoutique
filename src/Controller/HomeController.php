<?php

namespace App\Controller;


use App\Repository\HeaderRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\Routing\Annotation\Route;

class HomeController extends AbstractController
{
    /**
     * @Route("/", name="home")
     */
    public function index(HeaderRepository $headerRepository)
    {
        $headers = $headerRepository->findAll();
        return $this->render('home/index.html.twig', ['headers' => $headers]);
    }
}
