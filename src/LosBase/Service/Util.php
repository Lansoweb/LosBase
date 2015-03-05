<?php

/**
 * Define alguns serviços úteis
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
namespace LosBase\Service;

use Zend\Console\Console;

/**
 * Define alguns serviços úteis
 *
 * @package   LosBase\Service
 * @author    Leandro Silva <leandro@leandrosilva.info>
 * @link      http://leandrosilva.info Development Blog
 * @link      http://github.com/LansoWeb/LosBase for the canonical source repository
 * @copyright 2011-2015 Leandro Silva (http://leandrosilva.info)
 * @license   http://leandrosilva.info/licenca-bsd New BSD license
 */
class Util
{
    public static function getUserAgent()
    {
        if (Console::isConsole()) {
            return 'Console';
        }
        if (!empty($_SERVER['HTTP_USER_AGENT'])) {
            return $_SERVER['HTTP_USER_AGENT'];
        }

        return '???';
    }

    public static function getIP($just_remote = true)
    {
        if (Console::isConsole()) {
            return '127.0.0.1';
        }

        // O único realmente seguro de se confiar é o REMOTE_ADDR
        $validator = new \Zend\Validator\Ip();

        $remote = $_SERVER['REMOTE_ADDR'];
        if (!$validator->isValid($remote)) {
            throw new \RuntimeException("Endereço de IP '$remote' inválido");
        }
        if ($just_remote) {
            return $remote;
        }

        $ips = [$remote];

        if (! empty($_SERVER['HTTP_CLIENT_IP'])) {
            $ip = $_SERVER['HTTP_CLIENT_IP'];
            if ($validator->isValid($ip)) {
                $ips[2] = $ip;
            }
        } elseif (! empty($_SERVER['HTTP_X_FORWARDED_FOR'])) {
            $ip = $_SERVER['HTTP_X_FORWARDED_FOR'];
            if ($validator->isValid($ip)) {
                $ips[1] = $ip;
            }
        }

        return $ips;
    }
}
