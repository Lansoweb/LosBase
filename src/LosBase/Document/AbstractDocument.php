<?php

/**
 * Definição de uma classe abstrata para os Documentos
 *
 * @package   LosBase\Document
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as ODM;
use Zend\Form\Annotation as Form;

/**
 * Definição de uma classe abstrata para os Documentos
 *
 * @package   LosBase\Document
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 *
 * @ODM\MappedSuperclass
 * @Form\Name("entity") Not necessary, but there must be at least one line with Form to use the "use" statement without complains from IDE and cs-fixer
 */
abstract class AbstractDocument implements BaseDocumentInterface
{
    use Db\Field\Id, Db\Field\Created, Db\Field\Updated;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }
}
