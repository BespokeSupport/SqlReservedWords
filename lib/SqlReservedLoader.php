<?php

namespace BespokeSupport\SqlReserved;

/**
 * TODO TESTS
 * Class SqlReservedLoader
 * @package BespokeSupport\SqlReserved
 */
class SqlReservedLoader
{
    /**
     * @param $path
     * @return bool
     */
    public static function tryIncludeComposerAutoload($path)
    {
        if (file_exists($path . 'dist/sql-reserved-words.phar')) {
            return false;
        }

        $composerJson = $path . 'composer.json';
        if (!file_exists($composerJson)) {
            return false;
        }

        $json = file_get_contents($composerJson);
        $vendor = $json['config']['vendor-dir'] ?? 'vendor';

        $vendorDir = $path . $vendor;

        $autoload = "$vendorDir/autoload.php";

        if (file_exists($autoload)) {
            require_once $autoload;
            return true;
        }

        return false;
    }
}