<?php

namespace Business\User;

use \TKS\JWTHelper;

class AuthModel
{
    public static $jsonWebToken = '';
    private static $_errors = [];
    private static $_extData = [];


    public static function run(): bool
    {
        $extData = [];
        if (JWTHelper::checkJWT(self::$jsonWebToken, $extData) === false) {
            self::$_errors += JWTHelper::getErrors();
            return false;
        }
        self::$_extData = $extData;
        return true;
    }

    public static function getExtData(): array
    {
        return self::$_extData;
    }

    public static function getErrors(): array
    {
        return self::$_errors;
    }

    public static function __callStatic($name, $args)
    {
        if (strpos($name, 'get') === 0 &&
        (isset(self::$_extData[str_replace('get', '', $name)]) ||
        isset(self::$_extData[strtolower(str_replace('get', '', $name))]))) {
            return isset(self::$_extData[str_replace('get', '', $name)]) ? 
            self::$_extData[str_replace('get', '', $name)] :
            self::$_extData[strtolower(str_replace('get', '', $name))];
        } else {
            return '';
        }
    }
}