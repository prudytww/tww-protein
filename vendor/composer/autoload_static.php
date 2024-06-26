<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit40fb544d1c935e200958456fe9619a4d
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TwwcProtein\\' => 12,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TwwcProtein\\' => 
        array (
            0 => __DIR__ . '/../..' . '/src',
        ),
    );

    public static $classMap = array (
        'Composer\\InstalledVersions' => __DIR__ . '/..' . '/composer/InstalledVersions.php',
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit40fb544d1c935e200958456fe9619a4d::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit40fb544d1c935e200958456fe9619a4d::$prefixDirsPsr4;
            $loader->classMap = ComposerStaticInit40fb544d1c935e200958456fe9619a4d::$classMap;

        }, null, ClassLoader::class);
    }
}
