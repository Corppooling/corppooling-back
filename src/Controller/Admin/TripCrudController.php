<?php

namespace App\Controller\Admin;

use App\DoctrineType\TripMissing;
use App\Entity\Trip;
use EasyCorp\Bundle\EasyAdminBundle\Config\Crud;
use EasyCorp\Bundle\EasyAdminBundle\Field\ChoiceField;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\DateField;
use EasyCorp\Bundle\EasyAdminBundle\Field\IntegerField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextareaField;
use EasyCorp\Bundle\EasyAdminBundle\Field\TextField;
use Symfony\Component\Form\Extension\Core\Type\EnumType;

class TripCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Trip::class;
    }


    public function configureFields(string $pageName): iterable
    {

        $type = ChoiceField::new('type');

        if (Crud::PAGE_INDEX === $pageName) {
            return [
                IntegerField::new('id'),
                $type,
                AssociationField::new('announcer'),
                AssociationField::new('company'),
                AssociationField::new('members'),
                TextField::new('arrival_location'),
                IntegerField::new('available_seats'),
                IntegerField::new('price'),
                TextField::new('departure_location'),
                TextField::new('car_model'),
                TextField::new('car_color'),
                DateField::new('departure_time'),
                TextareaField::new('message'),
                DateField::new('updated_at'),
                DateField::new('created_at'),
            ];
        }

        $type->setChoices(TripMissing::cases());
        return [
            $type,
            TextField::new('arrival_location'),
            IntegerField::new('available_seats'),
            TextField::new('departure_location'),
            IntegerField::new('price'),
            TextField::new('car_model'),
            TextField::new('car_color'),
            TextareaField::new('message'),

        ];
    }
}
