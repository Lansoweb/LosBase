<?php

/**
 * Define os serviços básicos de entidade
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Service\ORM;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use LosBase\EventManager\EventProvider;

/**
 * Define os serviços básicos de entidade
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractEntity extends EventProvider implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait;

    public function save($form, $entity)
    {
        $this->getEventManager()->trigger(__FUNCTION__.'.init', $this, [
            'entity' => $entity,
            'form' => $form,
        ]);
        if (! $form->isValid()) {
            $this->getEventManager()->trigger(__FUNCTION__.'.invalid', $this, [
                'entity' => $entity,
                'form' => $form,
            ]);

            return false;
        }
        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');
        $entity = $form->getData();
        if ($entity->getId() > 0) {
            $entity = $em->merge($entity);
            if (\method_exists($entity, "setUpdated")) {
                $entity->setUpdated(new \DateTime('now'));
            }
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, [
            'entity' => $entity,
            'form' => $form,
        ]);
        $em->persist($entity);
        $em->flush();
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, [
            'entity' => $entity,
            'form' => $form,
        ]);

        return $entity;
    }

    public function delete($entity)
    {
        if (! is_object($entity)) {
            throw new \InvalidArgumentException(sprintf("Entity argument must be an object, %s given.", \gettype($entity)));
        }
        $this->getEventManager()->trigger(__FUNCTION__.'.init', $this, [
            'entity' => $entity,
        ]);

        $em = $this->getServiceLocator()->get('doctrine.entitymanager.orm_default');

        $id = 0;
        if ($entity->getId() > 0) {
            $id = $entity->getId();
            $em->remove($entity);
            $em->flush();
        }

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, [
            'entityId' => $id,
        ]);

        return $entity;
    }
}
