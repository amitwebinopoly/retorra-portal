<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit17630c1bf34b3f297dd64284beec8b2e
{
    public static $prefixLengthsPsr4 = array (
        'Q' => 
        array (
            'QuickBooksOnline\\API\\' => 21,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'QuickBooksOnline\\API\\' => 
        array (
            0 => __DIR__ . '/..' . '/quickbooks/v3-php-sdk/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit17630c1bf34b3f297dd64284beec8b2e::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit17630c1bf34b3f297dd64284beec8b2e::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit17630c1bf34b3f297dd64284beec8b2e::$classMap;

        }, null, ClassLoader::class);
    }
}
