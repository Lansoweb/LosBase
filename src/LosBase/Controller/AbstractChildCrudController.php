<?php
namespace LosBase\Controller;
use LosBase\Controller\AbstractCrudController;

abstract class AbstractChildCrudController extends AbstractCrudController
{
    abstract public function getEditRouteName();
    
    public function getActionRoute ($action = null)
    {
        if (null == $action) {
            return $this->getEditRouteName();
        }
    
        return $this->getRouteName() . '/' . $action;
    }
}

?>