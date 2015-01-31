<?php
namespace __MODULENAME__\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;
use LosBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="__MODULEDASHEDNAME__")
 * @Annotation\Name("form__MODULENAME__")
 * @Annotation\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Annotation\Type("LosBase\Form\AbstractForm")
 */
class __MODULENAME__ extends AbstractEntity
{
}
