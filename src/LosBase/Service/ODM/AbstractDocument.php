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
namespace LosBase\Service\ODM;

use Zend\ServiceManager\ServiceLocatorAwareInterface;
use Zend\ServiceManager\ServiceLocatorAwareTrait;
use LosBase\EventManager\EventProvider;
use LosBase\Document\DocumentManagerAwareTrait;

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
abstract class AbstractDocument extends EventProvider implements ServiceLocatorAwareInterface
{
    use ServiceLocatorAwareTrait, DocumentManagerAwareTrait;

    public function save($form, $document)
    {
        $this->getEventManager()->trigger(__FUNCTION__.'.init', $this, [
            'document' => $document,
            'form' => $form,
        ]);
        if (! $form->isValid()) {
            $this->getEventManager()->trigger(__FUNCTION__.'.invalid', $this, [
                'entity' => $document,
                'form' => $form,
            ]);

            return false;
        }
        $dm = $this->getDocumentManager();
        $document = $form->getData();
        if ($document->getId() > 0) {
            $document = $dm->merge($document);
            if (\method_exists($document, "setUpdated")) {
                $document->setUpdated(new \DateTime('now'));
            }
        }
        $this->getEventManager()->trigger(__FUNCTION__, $this, [
            'entity' => $document,
            'form' => $form,
        ]);
        $dm->persist($document);
        $dm->flush();
        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, [
            'entity' => $document,
            'form' => $form,
        ]);

        return $document;
    }

    public function delete($document)
    {
        if (! is_object($document)) {
            throw new \InvalidArgumentException(sprintf("Document argument must be an object, %s given.", \gettype($document)));
        }
        $this->getEventManager()->trigger(__FUNCTION__.'.init', $this, [
            'document' => $document,
        ]);

        $dm = $this->getDocumentManager();

        $id = 0;
        if ($document->getId() > 0) {
            $id = $document->getId();
            $dm->remove($document);
            $dm->flush();
        }

        $this->getEventManager()->trigger(__FUNCTION__.'.post', $this, [
            'entityId' => $id,
        ]);

        return $document;
    }
}
