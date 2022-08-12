<?php

namespace App\Controller\Admin;

use App\Classe\Mail;
use App\Entity\Order;
use Doctrine\ORM\EntityManagerInterface;
use EasyCorp\Bundle\EasyAdminBundle\Config\Action;
use EasyCorp\Bundle\EasyAdminBundle\Config\Actions;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Context\AdminContext;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateTimeField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;
use EasyCorp\Bundle\EasyAdminBundle\Field\MoneyField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextEditorField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;

class OrderCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Order::class;
    }

    public function configureActions(Actions $actions): Actions
    {
        $updatePreparation = Action::new('updatePreparation', 'Prépation en cours', 'fas fa-box-open')->linkToCrudAction('updatePraparation');
        $updateDelivery = Action::new('updateDelivery', 'livraison en cours', 'fas fa-truck')->linkToCrudAction('updateDelivery');
        $finished = Action::new('finished', 'Commande terminée', 'fas fa-truck')->linkToCrudAction('finished');

        return $actions
            ->add('detail', $finished )
            ->add('detail', $updateDelivery)
            ->add('detail', $updatePreparation)
            ->add('index', 'detail');
    }

    public function updatePraparation(AdminContext $context,EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(2);
        $entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'<strong>La comande ".$order->getReference()." est bien <u>en cours de préparation</u></strong></span>");
        $url = $adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function updateDelivery(AdminContext $context,EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(3);
        $entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'<strong>La comande ".$order->getReference()." est bien <u>en cours de livraison</u></strong></span>");
        $url = $adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        $mail = new Mail();
        $content = "<h3>Bonjour ".$order->getUser()->getFirstname().",</h3>
            <div style='text-align: center'>
             <p>
               Nous vous remercions encore une fois pour votre commande passée dans notre boutique savonneriedesadrets.com.
               <div style='color: #0c4a6e; font-weight: bold'>
                Nous avons le plaisir de vous informer que nous venons d’expédier votre colis qui vous sera livré entre 24h & 72h ouvrés.
               </div>
             </p>
            </div>
            <h4 style='color: #0c4a6e'>Une Question ?</h4>
            <p>
               Nous vous répondons avec plaisir du mardi au samedi de 8h à 18h30, par téléphone au 06.46.76.01.83 ou par mail à contact@savonneriedesadrets.com<br/><br/>
               <div style='text-align: center'>Merci et à très bientôt sur www.savonneriedesadrets.com</div>
            </p>"
        ;

        $mail->send($order->getUser()->getEmail(), $order->getUser()->getFirstname(), 'Votre commande a été expédiée.', $content);

        return $this->redirect($url);
    }

    public function finished(AdminContext $context,EntityManagerInterface $entityManager, AdminUrlGenerator $adminUrlGenerator)
    {
        $order = $context->getEntity()->getInstance();
        $order->setState(4);
        $entityManager->flush();

        $this->addFlash('notice', "<span style='color:green;'<strong>La comande ".$order->getReference()." est bien terminée<u></u></strong></span>");
        $url = $adminUrlGenerator
            ->setController(OrderCrudController::class)
            ->setAction('index')
            ->generateUrl();

        return $this->redirect($url);
    }

    public function configureCrud(Crud $crud): Crud
    {
        return $crud->setDefaultSort(['id' => 'DESC']);
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id'),
            DateTimeField::new('createAt', 'Passée le'),
            TextField::new('user.getFullName', 'Utilisateur'),
            MoneyField::new('total', 'Total produit')->setCurrency('EUR'),
            TextField::new('carrierName', 'Transporteur'),
            TextEditorField::new('delivery', 'Adresse')->formatValue(function ($value) {return $value;}) ->hideOnIndex(),
            TextField::new('reference', 'Référence'),
            MoneyField::new('carrierPrice', 'Frais de port')->setCurrency('EUR'),
            ChoiceField::new('state','statut')->setChoices([
                'Non payée' => 0,
                'Payée' => 1,
                'Préparation en cours' => 2,
                'livraison en cours' => 3,
                'Terminé' => 4,
            ]),
            ArrayField::new('orderDetails', 'Produits commandées')->hideOnIndex(),
        ];
    }
}
