<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\OrderDetails;
use App\Repository\ProductRepository;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class StripeController extends AbstractController
{
    /**
     * @Route("/commande/create-session", name="stripe_create_session")
     */
    public function index(Cart $cart, ProductRepository $productRepository)
    {
        $product_for_stripe = [];
        $YOUR_DOMAIN = 'http://127.0.0.1:8000';

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

        foreach ($cartComplete as $product) {

            $product_for_stripe[] = [
                'price_data' => [
                    'currency' => 'eur',
                    'unit_amount' => $product['product']->getPrice(),
                    'product_data' => [
                        'name' => $product['product']->getName() ,
                        'images' => [$YOUR_DOMAIN."/uploads/".$product['product']->getIllustration()],
                    ],
                ],
                'quantity' => $product['quantity'],
            ];
        }

        Stripe::setApiKey('sk_test_51KXXdPB3uY8ggA5zbHTGDre3tMIQinH6256bWE8zix80yjTj5l0KDvxaIUD1RYPHGida3UKa9PhlmSc9MDwMsypC00Qvk3elIW');



        $checkout_session = Session::create([
            'payment_method_types' => ['card'],
            'line_items' => [[
                $product_for_stripe
            ]],
            'mode' => 'payment',
            'success_url' => $YOUR_DOMAIN . '/success.html',
            'cancel_url' => $YOUR_DOMAIN . '/cancel.html',
        ]);

        $response = new JsonResponse(['id' => $checkout_session->id]);
        return $response;
    }
}
