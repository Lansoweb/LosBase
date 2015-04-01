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
     * @var \DateTime
     */
    protected $created = null;

    /**
     * Getter for $created
     *
     * @return \DateTime
     */
    public function getCreated()
    {
        return $this->created;
    }

    /**
     * Setter for $created
     *
     * @param  \DateTime                      $created
     * @return \LosBase\Entity\AbstractEntity
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
