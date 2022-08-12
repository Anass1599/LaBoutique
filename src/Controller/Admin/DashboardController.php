<?php

namespace App\Controller\Admin;

use App\Entity\Carrier;
use App\Entity\Category;
use App\Entity\Header;
use App\Entity\Header1;
use App\Entity\Order;
use App\Entity\Product;
use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Config\Dashboard;
use EasyCorp\Bundle\EasyAdminBundle\Config\MenuItem;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractDashboardController;
use EasyCorp\Bundle\EasyAdminBundle\Router\AdminUrlGenerator;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;

class DashboardController extends AbstractDashboardController
{
    /**
     * @Route("/admin", name="admin")
     */
    public function index(): Response
    {
        $routeBuilder = $this->get(AdminUrlGenerator::class);

        return $this->redirect($routeBuilder->setController(OrderCrudController::class)->generateUrl());
    }

    public function configureDashboard(): Dashboard
    {
        return Dashboard::new()
            ->setTitle('LaBoutique');
    }

    public function configureMenuItems(): iterable
    {
        yield MenuItem::linkToDashboard('La boutique', 'fa fa-home');
        yield MenuItem::linkToCrud('Users', 'fa fa-user', User::class );
        yield MenuItem::linkToCrud('Orders', 'fa fa-shopping-cart', Order::class );
        yield MenuItem::linkToCrud('Categories', 'fa fa-list', Category::class );
        yield MenuItem::linkToCrud('Produits', 'fa fa-tag', Product::class );
        yield MenuItem::linkToCrud('Carriers', 'fa fa-truck', Carrier::class );
        yield MenuItem::linkToCrud('Header(Accueil)', 'fa fa-desktop', Header::class );
        yield MenuItem::linkToCrud('Header(La savonnerie)', 'fa fa-desktop', Header1::class );
    }
}
