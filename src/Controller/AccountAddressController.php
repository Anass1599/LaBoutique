<?php

namespace App\Controller;

use App\Classe\Cart;
use App\Entity\Address;
use App\Form\AddressType;
use App\Repository\AddressRepository;
use Doctrine\ORM\EntityManagerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class AccountAddressController extends AbstractController
{
    /**
     * @Route("/compte/address", name="account_address")
     */
    public function index(AddressRepository $addressRepository): Response
    {
        return $this->render('account/address.html.twig');
    }

    /**
     * @Route("/compte/ajouter-une-address", name="account_address_add")
     */
    public function add(Cart $cart, Request $request, EntityManagerInterface $entityManager)
    {

        $address = new Address();

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $address->setUser($this->getUser());
            $entityManager->persist($address);
            $entityManager->flush();
            if ($cart->get())
            {
                return $this->redirectToRoute('order');
            } else {
                return $this->redirectToRoute('account_address');
            }
        }

        return $this->render('account/address_add.html.twig', [ 'form' => $form->createView()]);
    }

    /**
     * @Route("/compte/modifier-une-address/{id}", name="account_address_edit")
     */
    public function edit($id, Request $request, EntityManagerInterface $entityManager, AddressRepository $addressRepository)
    {

        $address = $addressRepository->findOneById($id) ;

        if (!$address || $address->getUser() != $this->getUser()){
            return  $this->redirectToRoute('account_address');
        }

        $form = $this->createForm(AddressType::class, $address);

        $form->handleRequest($request);

        if($form->isSubmitted() && $form->isValid()){
            $entityManager->flush();
            return $this->redirectToRoute('account_address');
        }

        return $this->render('account/address_add.html.twig', [ 'form' => $form->createView()]);
    }

    /**
     * @Route("/compte/supprimer-une-address/{id}", name="account_address_delete")
     */
    public function delete($id, EntityManagerInterface $entityManager, AddressRepository $addressRepository)
    {

        $address = $addressRepository->findOneById($id) ;

        if (!$address || $address->getUser() == $this->getUser()){

            $entityManager->remove($address);
            $entityManager->flush();
        }

        return  $this->redirectToRoute('account_address');
    }
}
