<?php
namespace LosBase\Document\Db\Field;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Annotation as Form;

trait Created
{
    /**
     * Created datetime
     *
     * @ODM\Date()
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
     * @return \LosBase\Document\AbstractEntity
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }
}
