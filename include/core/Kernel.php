<?php


namespace MyApp;

class Kernel
{
    public static $router;
    public static function init() {
        self::$router = new Router();
        self::$router->start();
    }
}