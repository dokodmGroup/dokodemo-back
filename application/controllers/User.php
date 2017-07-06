<?php

use \Business\User\RegisterModel;

class UserController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        ResponseHelper::json(200, '', ['result' => $result]);
    }

    public function save($request)
    {
        $account = $request->getPost('account', $_POST['account'] ?? '');
        $password = $request->getPost('password', $_POST['password'] ?? '');
        if (empty($account) || empty($password)) {
            return [400, '账号或密码不能为空'];
        }
        RegisterModel::$account = $account;
        if (RegisterModel::checkAccount() === false) {
            return [400, '此邮件已注册'];
        }
        RegisterModel::$password = $password;
        if (RegisterModel::done() === true) {
            return [201, '账号创建成功'];
        } else {
            return [500, '账号创建失败'];
        }

    }
}