<?php

/**
 * Definição de uma classe abstrata para as Entidades
 *
 * @package   LosBase\Entity
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Entity;

use Doctrine\ORM\Mapping as ORM;

/**
 * Definição de uma classe abstrata para as Entidades
 *
 * @package   LosBase\Entity
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 *
 * @ORM\MappedSuperclass
 */
abstract class AbstractEntity
{
    use IdentifableTrait, CreatedUpdatedTrait;

    /**
     * Construtor
     */
    public function __construct()
    {
        $this->created = new \DateTime('now');
        $this->updated = new \DateTime('now');
    }
}
