<?php
/**
 * View Helper that shows the system's version
 *
 * @package   LosBase\View\Helper
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\View\Helper;

use Zend\View\Helper\AbstractHelper;

/**
 * View Helper that shows the system's version
 *
 * @package   LosBase\View\Helper
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright Copyright (c) 2011-2013 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
class Version extends AbstractHelper
{
    public function __invoke()
    {
        if (!file_exists('data/version.txt')) {
            $version = '1.0.0';
        } else {
            $arq = file('data/version.txt');
            $version = trim($arq[0]);
        }

        return $version;
    }
}
