<?php

/**
 * Definição de uma classe abstrata para as Entidades
 *
 * @package   LosBase\Entity
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Entity;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Mapping\Builder\ClassMetadataBuilder;
use Zend\InputFilter\InputFilterAwareInterface;
use Zend\InputFilter\InputFilterInterface;
use LosLog\Log\LoggableObject;

/**
 * Definição de uma classe abstrata para as Entidades
 *
 * @package   LosBase\Entity
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 *
 * @Doctrine\ORM\Mapping\MappedSuperclass
 */
abstract class AbstractEntity extends LoggableObject implements InputFilterAwareInterface
{

    /**
     * Filtro usado para preencher os dados vindos de uma form por exemplo
     *
     * @var Zend\InputFilter\InputFilter
     */
    protected $inputFilter;

    /**
     * Id da entidade na tabela do banco de dados
     *
     * @Doctrine\ORM\Mapping\Id
     * @Doctrine\ORM\Mapping\Column(type="integer");
     * @Doctrine\ORM\Mapping\GeneratedValue(strategy="AUTO")
     */
    protected $id;

    /**
     * Created datetime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     * @Zend\Form\Annotation\Exclude()
     */
    protected $created = '';

    /**
     * Updated datetime
     *
     * @Doctrine\ORM\Mapping\Column(type="datetime")
     * @Zend\Form\Annotation\Exclude()
     */
    protected $updated = '';

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }

    /**
     * Getter id
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Setter id
     *
     * @param  int                            $id
     * @return \LosBase\Entity\AbstractEntity
     */
    public function setId($id)
    {
        $this->id = (int) $id;

        return $this;
    }

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
     * @param  DateTime                       $created
     * @return \LosBase\Entity\AbstractEntity
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
     * @param  DateTime                       $updated
     * @return \LosBase\Entity\AbstractEntity
     */
    public function setUpdated($updated)
    {
        $this->updated = $updated;

        return $this;
    }

    /**
     * Converte o objeto para um array.
     *
     * @return array
     */
    public function getArrayCopy()
    {
        return get_object_vars($this);
    }

    /**
     * Preenche a entidade com um array
     *
     * @param array $data
     */
    public function populate($data = array())
    {
        foreach ($data as $chave => $valor) {
            $metodo = 'set' . ucfirst($chave);
            if (method_exists($this, $metodo)) {
                $this->$metodo($valor);
            }
        }
    }

    /**
     * Seta o InputFilter
     *
     * @see \Zend\InputFilter\InputFilterAwareInterface::setInputFilter()
     */
    public function setInputFilter(InputFilterInterface $inputFilter)
    {
        throw new \Exception("Not used");
    }

    /**
     * Funcao que cria o InputFilter e define os campos
     *
     * @return Zend\InputFilter\InputFilter
     */
    protected function createInputFilter() {}

    /**
     * Busca o InputFilter ou cria um se não existir
     *
     * @see \Zend\InputFilter\InputFilterAwareInterface::getInputFilter()
     */
    public function getInputFilter()
    {
        if (! $this->inputFilter) {
            $this->inputFilter = $this->createInputFilter();
        }

        return $this->inputFilter;
    }

    public static function loadMetadata(ClassMetadata $metadata)
    {
        $builder = new ClassMetadataBuilder($metadata);
        $builder->setMappedSuperClass();
        $builder->createField('id', 'integer')->isPrimaryKey()->generatedValue()->build();
        $builder->addField('created', 'datetime');
        $builder->addField('updated', 'datetime');
    }
}
