<?php
namespace LosBase\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

trait IdentifableTrait
{

    /**
     * Id da entidade na tabela do banco de dados
     *
     * @ORM\Id
     * @ORM\Column(type="integer");
     * @ORM\GeneratedValue(strategy="AUTO")
     * @Annotation\Attributes({"type":"hidden","id":"id"})
     */
    protected $id;

    /**
     * Getter id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter id
     *
     * @param int $id
     * @return \LosBase\Entity\IdentifableTrait
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }
}