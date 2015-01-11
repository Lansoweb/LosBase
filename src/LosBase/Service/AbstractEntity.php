<?php

/**
 * Define os serviços básicos de entidade
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Service;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorInterface;
use LosBase\EventManager\EventProvider;

/**
 * Define os serviços básicos de entidade
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractEntity extends EventProvider implements ServiceLocatorAwareInterface
{

    /**
     *
     * @var ServiceLocator
     */
    protected $serviceLocator;

    public function save($form, $data, $entity)
    {
        $this->getEventManager()->trigger(__FUNCTION__ . '.init', $this, array(
            'entity' => $entity,
            'form' => $form
        ));
        if ($entity->getInputFilter() !== null) {
            $form->setInputFilter($entity->getInputFilter());
        } else {
            $entity->setInputFilter($form->getInputFilter());
        }
        $form->bind($entity);
        $form->setData($data);
        if (! $form->isValid()) {
            $this->getEventManager()->trigger(__FUNCTION__ . '.invalid', $this, array(
                'entity' => $entity,
                'form' => $form
            ));

            return false;
        }
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $entity = $form->getData();
        if ($entity->getId() > 0) {
            $entity = $em->merge($entity);
            $entity->setUpdated(new \DateTime('now'));
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, array(
            'entity' => $entity,
            'form' => $form
        ));
        $em->persist($entity);
        $em->flush();
        $this->getEventManager()->trigger(__FUNCTION__ . '.post', $this, array(
            'entity' => $entity,
            'form' => $form
        ));

        return $entity;
    }

    /**
     * Retrieve service manager instance
     *
     * @return ServiceManager
     */
    public function getServiceLocator()
    {
        return $this->serviceLocator;
    }

    /**
     * Set service manager instance
     *
     * @param  ServiceManager $locator
     * @return User
     */
    public function setServiceLocator(ServiceLocatorInterface $serviceLocator)
    {
        $this->serviceLocator = $serviceLocator;

        return $this;
    }
}
