<?php
namespace LosBase\Controller;

abstract class AbstractChildCrudController extends AbstractCrudController
{
    abstract public function getEditRouteName();

    public function getActionRoute($action = null)
    {
        if (null == $action) {
            return $this->getEditRouteName();
        }

        return $this->getRouteName() . '/' . $action;
    }
}
