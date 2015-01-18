<?php
namespace LosBase\Entity;

use Doctrine\ORM\Mapping as ORM;
use Zend\Form\Annotation;

trait CreatedUpdatedTrait
{

    /**
     * Created datetime
     *
     * @ORM\Column(type="datetime")
     * @Annotation\Exclude()
     */
    protected $created = '';

    /**
     * Updated datetime
     *
     * @ORM\Column(type="datetime")
     * @Annotation\Exclude()
     */
    protected $updated = '';

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
     * @param  DateTime $created
     * @return LosBase\Entity\CreatedUpdatedTrait
     */
    public function setCreated($created)
    {
        $this->created = $created;

        return $this;
    }

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
     * @param  DateTime $updated
     * @return \LosBase\Entity\CreatedUpdatedTrait
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }
}