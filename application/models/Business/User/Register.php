<?php

namespace Business\User;

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
        return true;
    }

    public static function getError(): string
    {
        return self::$_error;
    }
} 