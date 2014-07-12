<?php
/**
 * Definição de uma classe abstrata para as Entidades que tenham tradução
 *
 * @package    LosBase\Entity
 * @author     Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright  2011-2012 Olympus Sistemas
 * @version	   SVN: $Id: AbstractEntityTranslation.php 44 2012-11-08 17:27:14Z leandro $
 */

namespace LosBase\Entity;
use Gedmo\Translatable\Entity\MappedSuperclass\AbstractPersonalTranslation;

/**
 * Definição de uma classe abstrata para as Entidades que tenham tradução
 *
 * Criar variavel $object nas classes filhas
 *
 * @package    LosBase\Entity
 * @author     Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright  2011-2012 Olympus Sistemas
 * @version	   SVN: $Id: AbstractEntityTranslation.php 44 2012-11-08 17:27:14Z leandro $
 *
 * @Doctrine\ORM\Mapping\MappedSuperclass
 */
abstract class AbstractEntityTranslation extends AbstractPersonalTranslation
{

    /**
     * Convinient constructor
     *
     * @param string $locale
     * @param string $field
     * @param string $value
     */
    public function __construct ($locale, $field, $value)
    {
        $this->setLocale($locale);
        $this->setField($field);
        $this->setContent($value);
    }

    /**
     * @Doctrine\ORM\Mapping\JoinColumn(name="object_id", referencedColumnName="id", onDelete="CASCADE")
     */
    protected $object;

}
