<?php
namespace LosBase\Entity\Db\Field;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

trait Created
{

    /**
     * Created datetime
     *
     * @ORM\Column(type="datetime")
     * @Form\Exclude()
     */
    protected $created = '';

    /**
     * Retorna o campo $created
     *
     * @return $created DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Setter for $created
     *
     * @param  DateTime                        $created
     * @return LosBase\Entity\Db\Field\Created
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
