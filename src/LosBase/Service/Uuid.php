<?php
namespace LosBase\Service;

/**
 * UUID class
 *
 * The following class generates VALID RFC 4211 COMPLIANT
 * Universally Unique IDentifiers (UUID) version 3, 4 and 5.
 *
 * UUIDs generated validates using OSSP UUID Tool, and output
 * for named-based UUIDs are exactly the same. This is a pure
 * PHP implementation.
 *
 * @author Andrew Moore
 * @link http://www.php.net/manual/en/function.uniqid.php#94959
 */
class Uuid
{
    /**
     * Generates v3 or v5 UUIDs
     * @param  string         $namespace
     * @param  string         $name
     * @param  int            $version
     * @return boolean|string
     */
    private static function generateVersion($namespace, $name, $version)
    {
        if (!self::isValid($namespace)) {
            return false;
        }

        $nhex = str_replace(array('-', '{', '}'), '', $namespace);
        $nstr = '';
        $len = strlen($nhex);

        for ($i = 0; $i < $len; $i += 2) {
            $nstr .= chr(hexdec($nhex[$i].$nhex[$i+1]));
        }

        if ($version == 3) {
            $hash = md5($nstr.$name);
            $digit = 0x3000;
        } else {
            $hash = sha1($nstr.$name);
            $digit = 0x5000;
        }

        return sprintf('%08s-%04s-%04x-%04x-%12s',
            substr($hash, 0, 8),
            substr($hash, 8, 4),
            (hexdec(substr($hash, 12, 4)) & 0x0fff) | $digit,
            (hexdec(substr($hash, 16, 4)) & 0x3fff) | 0x8000,
            substr($hash, 20, 12)
        );
    }

    /**
     * Generate v3 UUID
     *
     * Version 3 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param uuid   $namespace
     * @param string $name
     */
    public static function v3($namespace, $name)
    {
        return self::generateVersion($namespace, $name, 3);
    }

    /**
     *
     * Generate v4 UUID
     *
     * Version 4 UUIDs are pseudo-random.
     */
    public static function v4()
    {
        return sprintf('%04x%04x-%04x-%04x-%04x-%04x%04x%04x',
            mt_rand(0, 0xffff), mt_rand(0, 0xffff),
            mt_rand(0, 0xffff),
            mt_rand(0, 0x0fff) | 0x4000,
            mt_rand(0, 0x3fff) | 0x8000,
            mt_rand(0, 0xffff), mt_rand(0, 0xffff), mt_rand(0, 0xffff)
        );
    }

    /**
     * Generate v5 UUID
     *
     * Version 5 UUIDs are named based. They require a namespace (another
     * valid UUID) and a value (the name). Given the same namespace and
     * name, the output is always the same.
     *
     * @param uuid   $namespace
     * @param string $name
     */
    public static function v5($namespace, $name)
    {
        return self::generateVersion($namespace, $name, 5);
    }

    /**
     * Validates an Uuid string
     *
     * @param  string  $uuid
     * @return boolean
     */
    public static function isValid($uuid)
    {
        return preg_match('/^\{?[0-9a-f]{8}\-?[0-9a-f]{4}\-?[0-9a-f]{4}\-?'.
                      '[0-9a-f]{4}\-?[0-9a-f]{12}\}?$/i', $uuid) === 1;
    }
}
