<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Order;
use App\Entity\OrderDetails;
use App\Form\OrderType;
use App\Repository\ProductRepository;
use Doctrine\ORM\EntityManagerInterface;
use Monolog\DateTimeImmutable;
use Stripe\Checkout\Session;
use Stripe\Stripe;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Symfony\Component\Validator\Constraints\DateTime;

class OrderController extends AbstractController
{
    /**
     *  @Route("/commande", name="order")
     */
    public function index(Cart $cart, ProductRepository $productRepository)
    {
        $product_objet = $productRepository->findOneByName($id);
        dump($product_objet);
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

        return $this->render('order/index.html.twig', [
            'form' => $form->createView(),
            'cart' => $cartComplete,
        ]);
    }

    /**
     *  @Route("/commande/recapitulatif", name="order_recap", methods={"POST"})
     */
    public function add(Cart $cart, ProductRepository $productRepository, Request $request, EntityManagerInterface $entityManager)
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

        $form = $this->createForm(OrderType::class, null, [
            'user' => $this->getUser(),
        ]);

        $form->handleRequest($request);

        if ($form->isSubmitted() && $form->isValid())
        {

            $date = new \DateTime();
            $carriers = $form->get('carriers')->getData();
            $delivery = $form->get('addresses')->getData();
            $delivery_content = $delivery->getFirstname().' '.$delivery->getlastname().'<br/>'.$delivery->getPhone();

            if ($delivery->getCompany()){
               $delivery_content .= '<br/>'.$delivery->getCompany();
            }

            $delivery_content .= '<br/>'.$delivery->getAddress();
            $delivery_content .= '<br/>'.$delivery->getPostal().' '.$delivery->getCity();
            $delivery_content .= '<br/>'.$delivery->getCountry();


           $order = new Order();
           $order->setUser($this->getUser());
           $order->setCreateAt($date);
           $order->setCarrierName($carriers->getName());
           $order->setCarrierPrice($carriers->getPrice());
           $order->setDelivery($delivery_content);
           $order->setIsPaid(0);
           $entityManager->persist($order);

           foreach ($cartComplete as $product) {

               $orderDetails = new OrderDetails();
               $orderDetails->setMyOrder($order);
               $orderDetails->setProduct($product['product']->getName());
               $orderDetails->setQuantity($product['quantity']);
               $orderDetails->setPrice($product['product']->getPrice());
               $orderDetails->setTotal($product['quantity'] * $product['product']->getPrice());
               $entityManager->persist($orderDetails);

           }
           //$entityManager->flush();

            return $this->render('order/add.html.twig', [
                'cart' => $cartComplete,
                'carrier' => $carriers,
                'delivery' => $delivery_content,
            ]);
        }
        return $this->redirectToRoute('cart');
    }
}
