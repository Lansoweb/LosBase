<?php
namespace LosBase\Entity\Db\Field;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation as Form;

trait Updated
{
    /**
     * Updated datetime
     *
     * @ORM\Column(type="datetime")
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
     * @return \LosBase\Entity\AbstractEntity
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
