<?php

use \Business\User\LoginModel;
use \TKS\ResponseHelper;


class UserController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        ResponseHelper::json(200, '', ['result' => $result]);
    }
}