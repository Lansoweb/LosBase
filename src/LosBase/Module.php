<?php
/**
 * Module definition
 *
 * @package   LosBase
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase;

use Zend\ModuleManager\Feature\AutoloaderProviderInterface;
use Zend\ModuleManager\Feature\LocatorRegisteredInterface;
use DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity;

/**
 * Module definition
 *
 * @package LosBase
 * @author Leandro Silva <leandro@leandrosilva.info>
 * @link http://leandrosilva.info Development Blog
 * @link http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license http://leandrosilva.info/licenca-bsd New BSD license
 */
class Module implements AutoloaderProviderInterface,
        LocatorRegisteredInterface
{

    public function getServiceConfig()
    {
        return array(
            'factories' => array(
                // TODO: create classes and use them on critical places
                'app_cache' => function ($sm) {
                    $cache = \Zend\Cache\StorageFactory::factory(array(
                        'adapter' => 'filesystem',
                        'plugins' => array(
                            'exception_handler' => array(
                                'throw_exceptions' => false
                            ),
                            'serializer'
                        )
                    ));

                    $cache->setOptions(array(
                        'cache_dir' => 'data/cache'
                    ));

                    return $cache;
                },
                'DoctrineORMModule\Stdlib\Hydrator\DoctrineEntity' => function ($sm) {
                    $em = $sm->get('doctrine.entitymanager.orm_default');

                    return new DoctrineEntity($em);
                }
            )
        );
    }

    public function getAutoloaderConfig()
    {
        return array(
            'Zend\Loader\ClassMapAutoloader' => array(
                __DIR__ . '/../../autoload_classmap.php'
            ),
            'Zend\Loader\StandardAutoloader' => array(
                'namespaces' => array(
                    __NAMESPACE__ => __DIR__
                )
            )
        );
    }

    public function getConfig()
    {
        return include __DIR__ . '/../../config/module.config.php';
    }
}
