<?php
namespace LosBaseTest\Assets\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;
use LosBase\Entity\AbstractEntity;

/**
 * @ORM\Entity
 * @ORM\Table(name="test")
 * @Form\Name("formTest")
 * @Form\Hydrator("Zend\Stdlib\Hydrator\ObjectProperty")
 * @Form\Type("LosBase\Form\AbstractForm")
 */
class TestEntity extends AbstractEntity
{
    /**
     * @ORM\Column(type="string", length=128)
     * @Form\Filter({"name":"StringTrim"})
     * @Form\Validator({"name":"StringLength", "options":{"min":2, "max":128}})
     * @Form\Attributes({"type":"text","id":"name"})
     * @Form\Options({"label":"Name"})
     * @Form\Required(true)
     */
    private $name;
}
