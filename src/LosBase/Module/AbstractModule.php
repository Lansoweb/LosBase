<?php

/**
 * Definição de uma classe abstrata para os módulos LosXX
 *
 * @package   LosBase\Module
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Module;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;

/**
 * Definição de uma classe abstrata para os módulos LosXX
 *
 * @package     LosBase\Module
 * @author      Leandro Silva <leandro@leandrosilva.info>
 * @link        http://leandrosilva.info Development Blog
 * @link        http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright   Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license     http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractModule implements AutoloaderProviderInterface, LocatorRegisteredInterface
{
    /**
     * Retorna o diretório atual
     */
    private function getDir()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();

        return dirname($filename);
    }

    /**
     * Retorna o namespace atual
     */
    private function getNamespace()
    {
        $reflector = new \ReflectionClass(get_class($this));

        return $reflector->getNamespaceName();
    }

    /**
     * Configura os Autolodaers
     *
     * @see \Zend\ModuleManager\Feature\AutoloaderProviderInterface::getAutoloaderConfig()
     */
    public function getAutoloaderConfig()
    {
        return [
            'Zend\Loader\ClassMapAutoloader' => [
                $this->getDir().'/../../autoload_classmap.php',
            ],
            'Zend\Loader\StandardAutoloader' => [
                'namespaces' => [
                    $this->getNamespace() => $this->getDir().'/../../src/'.$this->getNamespace(),
                ],
            ],
        ];
    }

    /**
     * @codeCoverageIgnore
     */
    public function getConfig()
    {
        return include $this->getDir().'/../../config/module.config.php';
    }
}
