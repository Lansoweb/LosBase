<?php
namespace LosBase\Document\Db\Field;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Annotation as Form;

trait Id
{
    /**
     * Id do documento na tabela do banco de dados
     *
     * @ODM\Id
     * @Form\Exclude()
     */
    protected $id;

    /**
     * Getter id
     *
     * @return mixed
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter id
     *
     * @param  mixed                  $id
     * @return \LosBase\Document\Db\Field\Id
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }
}
