<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInit344512f45d54a806c7e4e7127aa84f9b
{
    public static $prefixLengthsPsr4 = array (
        'T' => 
        array (
            'TelegramBot\\Api\\' => 16,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'TelegramBot\\Api\\' => 
        array (
            0 => __DIR__ . '/..' . '/telegram-bot/api/src',
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInit344512f45d54a806c7e4e7127aa84f9b::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInit344512f45d54a806c7e4e7127aa84f9b::$prefixDirsPsr4;

        }, null, ClassLoader::class);
    }
}
