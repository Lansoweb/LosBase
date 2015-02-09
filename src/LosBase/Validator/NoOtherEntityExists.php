<?php

/**
 * Define uma classe de para validar que outra entidade não existe (outro id)
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
 * Define uma classe de para validar que outra entidade não existe (outro id)
 *
 * @package   LosBase\Validator
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
class NoOtherEntityExists extends NoObjectExists
{
    private $id;

    private $additionalFields = null;

    public function __construct(array $options)
    {
        parent::__construct($options);

        if (! isset($options['id'])) {
            throw new InvalidArgumentException('Chave "id" deve ser especificada na procura por outras entidades');
        }
        if (isset($options['additionalFields'])) {
            $this->additionalFields = $options['additionalFields'];
        }

        $this->id = $options['id'];
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

        if (is_object($match) && $match->getId() != $this->id) {
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
