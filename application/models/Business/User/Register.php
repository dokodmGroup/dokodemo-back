<?php

namespace Business\User;

use \Mysql\UserModel;

class RegisterModel
{
    public static $account = '';
    public static $password = '';
    private static $_uid = 0;
    private static $_error = '';

    public static function done(): bool
    {
        $password = self::passwordPreprocess();
        try {
            self::$_uid = UserModel::getInstance()->insert(
                [
                    'account' => self::$account,
                    'password' => $password,
                    'create_time' => time(),
                    'status' => 1
                ]
            , true);
        } catch(\Exception $e) {
            self::$_error = $e->getMessage();
            return false;
        }
        return true;
    }

    public static function getUid()
    {
        return intval(self::$_uid);
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

    public static function isEmail()
    {
        $result = preg_match("/([\w\-]+\@[\w\-]+\.[\w\-]+)/", self::$account);
        return $result !== 0 && $result !== false;
    }

    public static function getError(): string
    {
        return self::$_error;
    }

    private static function passwordPreprocess()
    {
        return md5(self::$password);
    }
} 