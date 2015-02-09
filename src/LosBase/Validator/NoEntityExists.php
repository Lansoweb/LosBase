<?php

/**
 * Define uma classe de para validar que outra entidade não existe com os campos especificados
 *
 * @package   LosBase\Validator
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Validator;

use Zend\Validator\Exception\InvalidArgumentException;
use DoctrineModule\Validator\NoObjectExists;

/**
 * Define uma classe de para validar que outra entidade não existe com os campos especificados
 *
 * @package   LosBase\Validator
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
class NoEntityExists extends NoObjectExists
{
    private $additionalFields = null;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (isset($options['additionalFields'])) {
            $this->additionalFields = $options['additionalFields'];
        }
    }

    public function isValid($value, $context = null)
    {
        if (null !== $this->additionalFields && is_array($context)) {
            $value = (array) $value;
            foreach ($this->additionalFields as $field) {
                if (! isset($context[$field])) {
                    throw new InvalidArgumentException('Campo "'.$field.'"não especificado em additionalFields');
                }
                $value[] = $context[$field];
            }
        }
        $value = $this->cleanSearchValue($value);
        $match = $this->objectRepository->findOneBy($value);

        if (is_object($match)) {
            if (is_array($value)) {
                $str = '';
                foreach ($value as $campo) {
                    if ($str != '') {
                        $str .= ', ';
                    }
                    $str .= $campo;
                }
                $value = $str;
            }
            $this->error(self::ERROR_OBJECT_FOUND, $value);

            return false;
        }

        return true;
    }
}
