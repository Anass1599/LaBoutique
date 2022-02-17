<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class OrderController extends AbstractController
{
    /**
     *  @Route("/commande", name="order")
     */
    public function index(Cart $cart, ProductRepository $productRepository, Request $request)
    {
        $cartComplete = [];

        if ($cart->get()) {

            foreach ($cart->get() as $id => $quantity) {
                $product_objet = $productRepository->findOneById($id);
                if (!$product_objet) {
                    $cart->delete($id);
                    continue;
                }
                $cartComplete[] = [
                    'product' => $product_objet,
                    'quantity' => $quantity,
                ];

            }
        }
        if (!$this->getUser()->getAddresses()->getValues())
        {
            return $this->redirectToRoute('account_address_add');
        }
        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {
            dd($form->getData());
        }

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartComplete,
        ]);
    }
}
