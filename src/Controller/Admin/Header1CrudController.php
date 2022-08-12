<?php

namespace App\Controller\Admin;

use App\Entity\Header1;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\ImageField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;

class Header1CrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Header1::class;
    }

    public function configureFields(string $pageName): iterable
    {
        return [
            ImageField::new('illustration')
                ->setBasePath('uploads/')
                ->setUploadDir('public/uploads')
                ->setUploadedFileNamePattern('[randomhash].[extension]')
                ->setRequired(false),
        ];
    }
}
