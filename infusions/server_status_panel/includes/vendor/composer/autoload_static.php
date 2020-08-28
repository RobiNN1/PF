<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInite32ac3bd2dcf7d4a2fceb21c6e81c04b
{
    public static $prefixLengthsPsr4 = array (
        'P' => 
        array (
            'Psr\\SimpleCache\\' => 16,
            'Psr\\Cache\\' => 10,
            'Phpfastcache\\' => 13,
        ),
        'G' => 
        array (
            'GameQ\\' => 6,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'Psr\\SimpleCache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/simple-cache/src',
        ),
        'Psr\\Cache\\' => 
        array (
            0 => __DIR__ . '/..' . '/psr/cache/src',
        ),
        'Phpfastcache\\' => 
        array (
            0 => __DIR__ . '/..' . '/phpfastcache/phpfastcache/lib/Phpfastcache',
        ),
        'GameQ\\' => 
        array (
            0 => __DIR__ . '/..' . '/austinb/gameq/src/GameQ',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInite32ac3bd2dcf7d4a2fceb21c6e81c04b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInite32ac3bd2dcf7d4a2fceb21c6e81c04b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
