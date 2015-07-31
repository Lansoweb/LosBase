<?php
namespace LosBase\Document\Db\Field;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Annotation as Form;

trait Updated
{
    /**
     * Updated datetime
     *
     * @ODM\Date()
     * @Form\Exclude()
     * @var \DateTime
     */
    protected $updated = null;

    /**
     * Getter for $updated
     *
     * @return \DateTime
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Setter for $updated
     *
     * @param  \DateTime                      $updated
     * @return \LosBase\Document\AbstractDocument
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
