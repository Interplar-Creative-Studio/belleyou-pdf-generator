<?php

namespace PDFGenerators;

if(!defined('PX_TO_PT')) {
    define('PX_TO_PT', 3 / 4);
}

class Autoloader
{
    public static function register()
    {
        spl_autoload_register(function ($class) {
            $file = './' . str_replace('\\', DIRECTORY_SEPARATOR, $class).'.php';
            if (file_exists($file)) {
                require_once $file;
                return true;
            }
            return false;
        });
    }
}
\PDFGenerators\Autoloader::register();
