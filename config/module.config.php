<?php
/**
 * Configuration file for Módulo LosBase
 *
 * @package   LosBase
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase;

return [
    'losbase' => [
        'enable_console' => false,
    ],
    'view_helpers' => [
        'invokables' => [
            'losversion' => 'LosBase\View\Helper\Version',
            'losformelementerrors' => 'LosBase\Form\View\Helper\FormElementErrors',
        ],
    ],
    'view_manager' => [
        'helper_map' => [
            'LosVersion' => 'LosBase\View\Helper\Version',
        ],
    ],
    'doctrine' => [
        'driver' => [
            'LosBase_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\AnnotationDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__.'/../src/LosBase/Entity',
                ],
            ],
            'orm_default' => [
                'drivers' => [
                    'LosBase\Entity' => 'LosBase_entity',
                ],
            ],
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'utcdatetime' => 'LosBase\DBAL\Types\UtcDateTimeType',
                    'brdatetime' => 'LosBase\DBAL\Types\BrDateTimeType',
                    'brprice' => 'LosBase\DBAL\Types\BrPriceType',
                ],
            ],
        ],
    ],
    'controllers' => array(
        'invokables' => array(
            'LosBase\Controller\Create' => 'LosBase\Controller\CreateController',
        ),
    ),
    'console' => [
        'router' => [
            'routes' => [
                'losbase-create-module' => [
                    'options' => [
                        'route' => 'create crud <name> [<path>]',
                        'defaults' => [
                            'controller' => 'LosBase\Controller\Create',
                            'action' => 'crud',
                        ],
                    ],
                ],
            ],
        ],
    ],
];
