<?php
namespace LosBase\Entity;

use Doctrine\ORM\EntityManager;

trait EntityManagerAwareTrait
{
    /**
     *
     * @var \Doctrine\ORM\EntityManager
     */
    private $em;

    /**
     * Seta o EntityManager
     *
     * @param \Doctrine\ORM\EntityManager $em
     */
    public function setEntityManager(EntityManager $em)
    {
        $this->em = $em;
    }

    /**
     * Retorna o EntityManager
     *
     * @return \Doctrine\ORM\EntityManager
     */
    public function getEntityManager()
    {
        if (null === $this->em) {
            $this->em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        }

        return $this->em;
    }
}
