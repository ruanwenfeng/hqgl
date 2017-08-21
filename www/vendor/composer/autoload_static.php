<?php

// autoload_static.php @generated by Composer

namespace Composer\Autoload;

class ComposerStaticInitcf32c0fbaecba1c4fdc07be832b50105
{
    public static $files = array (
        '1cfd2761b63b0a29ed23657ea394cb2d' => __DIR__ . '/..' . '/topthink/think-captcha/src/helper.php',
    );

    public static $prefixLengthsPsr4 = array (
        't' => 
        array (
            'think\\captcha\\' => 14,
        ),
    );

    public static $prefixDirsPsr4 = array (
        'think\\captcha\\' => 
        array (
            0 => __DIR__ . '/..' . '/topthink/think-captcha/src',
        ),
    );

    public static $prefixesPsr0 = array (
        'P' => 
        array (
            'PHPExcel' => 
            array (
                0 => __DIR__ . '/..' . '/phpoffice/phpexcel/Classes',
            ),
        ),
    );

    public static function getInitializer(ClassLoader $loader)
    {
        return \Closure::bind(function () use ($loader) {
            $loader->prefixLengthsPsr4 = ComposerStaticInitcf32c0fbaecba1c4fdc07be832b50105::$prefixLengthsPsr4;
            $loader->prefixDirsPsr4 = ComposerStaticInitcf32c0fbaecba1c4fdc07be832b50105::$prefixDirsPsr4;
            $loader->prefixesPsr0 = ComposerStaticInitcf32c0fbaecba1c4fdc07be832b50105::$prefixesPsr0;

        }, null, ClassLoader::class);
    }
}