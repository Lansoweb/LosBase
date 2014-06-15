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
            'losversion' => 'LosBase\View\Helper\Version'
        )
    ),
    'view_manager' => array(
        'helper_map' => array(
            'LosVersion' => 'LosBase\View\Helper\Version'
        )
    ),
);
