<?php
/**
 * Configuration file for Módulo LosBase
 *
 * @package   LosBase
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase;

return [
    'view_helpers' => [
        'invokables' => [
            'losversion'            => 'LosBase\View\Helper\Version',
            'losformelementerrors'  => 'LosBase\Form\View\Helper\FormElementErrors'
        ]
    ],
    'view_manager' => [
        'helper_map' => [
            'LosVersion' => 'LosBase\View\Helper\Version'
        ]
    ],
    'doctrine' => [
        'driver' => [
            __NAMESPACE__ .'_entity' => [
                'class' => 'Doctrine\ORM\Mapping\Driver\StaticPHPDriver',
                'cache' => 'array',
                'paths' => [
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                ]
            ],
            'orm_default' => [
                'drivers' => [
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ .'_entity'
                ]
            ]
        ],
        'configuration' => [
            'orm_default' => [
                'types' => [
                    'utcdatetime' => 'LosBase\DBAL\Types\UtcDateTimeType',
                    'brdatetime' => 'LosBase\DBAL\Types\BrDateTimeType',
                ],
            ]
        ]
    ]
];
