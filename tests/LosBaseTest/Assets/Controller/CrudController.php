<?php
namespace LosBaseTest\Assets\Controller;

use LosBase\Controller\AbstractCrudController;

class CrudController extends AbstractCrudController
{
    public function getEntityClass()
    {
        return 'LosBaseTest\Assets\Entity\TestEntity';
    }

    public function getEntityServiceClass()
    {
        return 'LosBaseTest\Assets\Service\TestService';
    }
}
