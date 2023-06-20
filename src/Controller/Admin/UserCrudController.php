<?php

namespace App\Controller\Admin;

use App\Entity\User;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\ArrayField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;

class UserCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {

        return User::class;
    }
    public function configureFields(string $pageName): iterable
    {
        return [
            IntegerField::new('id'),
            ArrayField::new('roles'),
            TextField::new('password')->hideOnIndex(),
            TextField::new('email'),
            TextField::new('firstName'),
            TextField::new('lastName'),
            TextField::new('phone'),
            AssociationField::new('company')->onlyOnIndex(),
            AssociationField::new('department')->onlyOnIndex(),
            AssociationField::new('trips')->onlyOnIndex(),
            AssociationField::new('reservations')->onlyOnIndex(),
            TextField::new('profile_image'),
            TextField::new('presentation'),
            DateField::new('updated_at'),
            DateField::new('created_at'),
        ];
    }
}
