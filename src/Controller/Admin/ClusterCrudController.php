<?php

namespace App\Controller\Admin;

use App\Entity\Cluster;
use EasyCorp\Bundle\EasyAdminBundle\Controller\AbstractCrudController;

class ClusterCrudController extends AbstractCrudController
{
    public static function getEntityFqcn(): string
    {
        return Cluster::class;
    }
}
