<?php

namespace Business\User;

use \Mysql\UserProfileModel;

class ProfileModel
{
    public static $id;
    public static function fetch()
    {
        return UserProfileModel::getInstance()->find(self::$id);
    }
}