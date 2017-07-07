<?php

namespace Business\User;

use \Mysql\UserModel;

class RegisterModel
{
    public static $account = '';
    public static $password = '';
    private static $_error = '';

    public static function done(): bool
    {
        return false;
    }

    public static function checkAccount(): bool
    {
        if (empty(self::$account)) {
            self::$_error = '电邮地址不能为空';
            return false;
        } elseif (!empty(UserModel::getInstance()->findByAccount(self::$account))) {
            self::$_error = '该电邮地址已注册';
            return false;
        }
        return true;
    }

    public static function getError(): string
    {
        return self::$_error;
    }
} 