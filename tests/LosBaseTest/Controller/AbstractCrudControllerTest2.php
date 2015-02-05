<?php
namespace LosBaseTest;

use LosBaseTest\Assets\Controller\CrudController;

class AbstractCrudControllerTest2 extends \PHPUnit_Framework_TestCase
{
    private $controller;

    protected function setUp()
    {
        parent::setUp();

        $this->controller = new CrudController();
    }

    protected function tearDown()
    {
        $this->controller = null;

        parent::tearDown();
    }

    public function testgetRouteName()
    {
        echo $this->controller->getRouteName();
    }
}
