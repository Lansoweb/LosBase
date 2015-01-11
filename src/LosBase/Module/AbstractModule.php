<?php

/**
 * Definição de uma classe abstrata para os módulos LosXX
 *
 * @package   LosBase\Module
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Module;

use InvalidArgumentException;
use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;

/**
 * Definição de uma classe abstrata para os módulos LosXX
 *
 * @package     LosBase\Module
 * @author      Leandro Silva <leandro@leandrosilva.info>
 * @link        http://leandrosilva.info Development Blog
 * @link        http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright   Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license     http://leandrosilva.info/licenca-bsd New BSD license
 */
abstract class AbstractModule implements AutoloaderProviderInterface, LocatorRegisteredInterface
{

    /**
     * Array contendo toda a configuração da aplicação
     *
     * @var array
     */
    protected $mergedConfig;

    /**
     * Retorna o diretório atual
     */
    public function getDir()
    {
        $reflector = new \ReflectionClass(get_class($this));
        $filename = $reflector->getFileName();

        return dirname($filename);
    }

    /**
     * Retorna o namespace atual
     */
    public function getNamespace()
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
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                $this->getDir() . '/../../autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    $this->getNamespace() => $this->getDir() . '/../../src/' . $this->getNamespace()
                )
            )
        );
    }

    public function getConfig()
    {
        return include $this->getDir() . '/../../config/module.config.php';
    }

    /**
     * Retorna o mergedConfig
     *
     * @return array
     */
    public function getMergedConfig()
    {
        return $this->mergedConfig;
    }

    /**
     * Seta o mergedConfig
     *
     * @param array $mergedConfig
     */
    public function setMergedConfig($mergedConfig)
    {
        $this->mergedConfig = $mergedConfig;
    }

    /**
     * Retorna todas as opções dentro do namespace espeficicado
     *
     * @param  string $namespace
     * @return array
     */
    public function getOptions($namespace = 'options')
    {
        $config = $this->getMergedConfig();
        if (empty($config[$this->getNamespace()][$namespace])) {
            return array();
        }

        if (is_array($config[$this->getNamespace()][$namespace])) {
            return $config[$this->getNamespace()][$namespace];
        } else {
            return $config[$this->getNamespace()][$namespace]->toArray();
        }
    }

    /**
     * Returns module option value.
     * Dot character is used to separate sub arrays.
     *
     * Example:
     * array(
     * 'option1' => 'this is my option 1'
     * 'option2' => array(
     * 'key1' => 'sub key1',
     * 'key2' => 'sub key2',
     * )
     * )
     *
     * $module->getOption('option1');
     * Returns: (string) "This is my option 1"
     *
     * $module->getOption('option2');
     * Returns: array(
     * 'key1' => 'sub key1',
     * 'key2' => 'sub key2',
     * )
     *
     * $module->getOption('option2.key1');
     * Returns: (string) "sub key1"
     *
     * @param  string $option
     * @param  mixed  $default
     * @return mixed
     */
    public function getOption($option, $default = null, $namespace = 'options')
    {
        $options = $this->getOptions($namespace);
        $optionArr = explode('.', $option);

        $option = $this->getOptionFromArray($options, $optionArr, $default, $option);

        return $option;
    }

    /**
     * Busca uma opção do array
     *
     * @param  unknown_type             $options
     * @param  array                    $option
     * @param  unknown_type             $default
     * @param  unknown_type             $origOption
     * @throws InvalidArgumentException
     * @return Ambigous                 <unknown,
     *                                             \Zend\Config\Config>|\Zend\Config\Config|unknown
     */
    private function getOptionFromArray($options, array $option, $default, $origOption)
    {
        $currOption = array_shift($option);
        if (array_key_exists($currOption, $options) || ($options instanceof \Zend\Config\Config && $options->offsetExists($currOption))) {
            if (count($option) >= 1) {
                return $this->getOptionFromArray($options[$currOption], $option, $default, $origOption);
            }

            return $options[$currOption];
        }

        if ($default !== null) {
            return $default;
        }

        throw new InvalidArgumentException("Opção '$origOption' não está definida.");
    }
}
