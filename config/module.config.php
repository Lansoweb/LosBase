<?php
/**
 * Configuration file for Módulo LosBase
 *
 * @package   LosBase
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase;

return array(
    'view_helpers' => array(
        'invokables' => array(
            'losversion'            => 'LosBase\View\Helper\Version',
            'losformelementerrors'  => 'LosBase\Form\View\Helper\FormElementErrors'
        )
    ),
    'view_manager' => array(
        'helper_map' => array(
            'LosVersion' => 'LosBase\View\Helper\Version'
        )
    ),
    'doctrine' => array(
        'driver' => array(
            __NAMESPACE__ .'_entity' => array(
                'class' => 'Doctrine\ORM\Mapping\Driver\StaticPHPDriver',
                'cache' => 'array',
                'paths' => array(
                    __DIR__ . '/../src/' . __NAMESPACE__ . '/Entity'
                )
            ),
            'orm_default' => array(
                'drivers' => array(
                    __NAMESPACE__ . '\Entity' => __NAMESPACE__ .'_entity'
                )
            )
        )
    )
);
