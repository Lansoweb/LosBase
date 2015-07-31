<?php
namespace LosBase\Document;

use Doctrine\ODM\MongoDB\DocumentManager;

trait DocumentManagerAwareTrait
{
    /**
     *
     * @var \Doctrine\ODM\MongoDb\DocumentManager
     */
    private $dm;

    /**
     * Seta o DocumentManager
     *
     * @param \Doctrine\ODM\MongoDb\DocumentManager $dm
     */
    public function setDocumentManager(DocumentManager $dm)
    {
        $this->dm = $dm;
    }

    /**
     * Retorna o DocumentManager
     *
     * @return \Doctrine\ODM\MongoDb\DocumentManager
     */
    public function getDocumentManager()
    {
        if (null === $this->dm) {
            $this->dm = $this->getServiceLocator()->get('doctrine.documentmanager.odm_default');
        }

        return $this->dm;
    }
}
