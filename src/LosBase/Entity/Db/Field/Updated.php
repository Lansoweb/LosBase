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
     */
    protected $updated = '';

    /**
     * Getter for $updated
     */
    public function getUpdated()
    {
        return $this->updated;
    }

    /**
     * Setter for $updated
     *
     * @param  DateTime                            $updated
     * @return \LosBase\Entity\CreatedUpdatedTrait
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}
