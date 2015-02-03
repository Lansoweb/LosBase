<?php

/**
 * Definição de uma classe para facilitar a criação dos InputFilters
 *
 * @package    LosBase\InputFilter
 * @author     Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright  2011-2012 Olympus Sistemas
 * @version	   SVN: $Id: Factory.php 37 2012-10-29 11:06:37Z leandro $
 */
namespace LosBase\InputFilter;

use Zend\InputFilter\Factory as ZendFactory;
use Zend\Validator\StringLength;

/**
 * Definição de uma classe para facilitar a criação dos InputFilters
 *
 * @package LosBase\InputFilter
 * @author Leandro Silva <leandro@olympussistemas.com.br>
 * @copyright 2011-2012 Olympus Sistemas
 * @version SVN: $Id: Factory.php 37 2012-10-29 11:06:37Z leandro $
 */
class Factory extends ZendFactory
{
    /**
     * Cria um filtro padrão para números inteiros (por exemplo, id)
     *
     * @param  string   $nome
     * @param  bool     $required
     * @return Ambigous <\Zend\InputFilter\InputInterface,
     *                           \Zend\InputFilter\InputFilterInterface>
     */
    public function createInputInt($nome, $required = true)
    {
        return $this->createInput([
            'name' => $nome,
            'required' => $required,
            'filters' => [
                [
                    'name' => 'Int',
                ],
            ],
        ]);
    }

    /**
     * Cria um filtro padrão para strings
     *
     * @param  string   $nome
     * @param  bool     $required
     * @param  int      $min
     * @param  int      $max
     * @param  array    $filters
     * @param  array    $validators
     * @return Ambigous <\Zend\InputFilter\InputInterface,
     *                             \Zend\InputFilter\InputFilterInterface>
     */
    public function createInputString($nome, $required = true, $min = 1, $max = 128, $filters = null, $validators = null)
    {
        if (null === $filters) {
            $filters = [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ];
        }
        if (null === $validators) {
            $validators = [
                [
                    'name' => 'StringLength',
                    'options' => [
                        'encoding' => 'UTF-8',
                        'min' => $min,
                        'max' => $max,
                    ],
                ],
            ];
        }

        return $this->createInput([
            'name' => $nome,
            'required' => $required,
            'filters' => $filters,
            'validators' => $validators,
        ]);
    }

    public function createInputEmail($nome, $required = true, $filters = null, $validators = null)
    {
        if (null === $filters) {
            $filters = [
                ['name' => 'StripTags'],
                ['name' => 'StringTrim'],
            ];
        }
        if (null === $validators) {
            $validators = [
                [
                    'name' => 'EmailAddress',
                ],
            ];
        }

        return $this->createInput([
            'name' => $nome,
            'required' => $required,
            'filters' => $filters,
            'validators' => $validators,
        ]);
    }
}
