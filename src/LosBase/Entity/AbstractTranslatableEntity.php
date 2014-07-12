<?php
/**
 * Definição de uma classe abstrata para as tabelas de tradução
 *
 * @package    LosBase\Entity
 * @author     Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright  2011-2012 Olympus Sistemas
 * @version	   SVN: $Id: AbstractTranslatableEntity.php 44 2012-11-08 17:27:14Z leandro $
 */
namespace LosBase\Entity;
use Doctrine\Common\Collections\ArrayCollection;
use LosBase\Entity\AbstractEntity as AbstractEntity;

/**
 * /**
 * Definição de uma classe abstrata para as tabelas de tradução
 *
 * Criar variável $translations nas classes filhas
 *
 * @package LosBase\Entity
 * @author Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright 2011-2012 Olympus Sistemas
 * @version SVN: $Id: AbstractTranslatableEntity.php 44 2012-11-08 17:27:14Z leandro $
 *
 * @Doctrine\ORM\Mapping\MappedSuperclass
 */
abstract class AbstractTranslatableEntity extends AbstractEntity
{

    public function __construct ()
    {
        parent::__construct();
        $this->translations = new ArrayCollection();
    }

    abstract public function getTranslatableFields();

    public function getTranslationClass()
    {
        return get_class($this) . 'Translation';
    }

    /**
     *
     * @param
     *            Ambigous <\Doctrine\Common\Collections\ArrayCollection,
     *            SetorTranslation> $translations
     */
    public function setTranslations ($translations)
    {
        $this->translations = $translations;
    }

    public function getTranslations ()
    {
        return $this->translations;
    }

    public function addTranslation (AbstractEntityTranslation $t)
    {
        if (! $this->translations->contains($t)) {
            $this->translations[] = $t;
            $t->setObject($this);
        }
    }
    /*
    public function hasAllTranslationsFor($field, $locales)
    {
        if (!is_array($locales)) {
            throw new \InvalidArgumentException('Lista deve ser um array!');
        }
        $temLocale = array();

        foreach ($this->translations as $translation)
        {
            if ($translation->getField() == $field) {
                $temLocale[$translation->getLocale()] = true;
            }
        }

        foreach ($locales as $locale)
        {
            if (!isset($temLocale[$locale])) {
                return false;
            }
        }
        return true;
    }
    */
}
