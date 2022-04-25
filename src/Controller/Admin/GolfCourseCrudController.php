<?php

namespace App\Controller\Admin;

use App\Entity\GolfCourse;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;
use EasyCorp\Bundle\EasyAdminBundle\Field\AssociationField;
use EasyCorp\Bundle\EasyAdminBundle\Field\Field;
use EasyCorp\Bundle\EasyAdminBundle\Field\IdField;

class GolfCourseCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return GolfCourse::class;
    }


    public function configureFields(string $pageName): iterable
    {
        return [
            IdField::new('id')->onlyOnIndex(),
            Field::new('name'),
            Field::new('geoname')->onlyOnForms(),
            Field::new('comment'),
            AssociationField::new('country')->autoComplete(),
            Field::new('longitude'),
            Field::new('latitude'),
        ];
    }

}
