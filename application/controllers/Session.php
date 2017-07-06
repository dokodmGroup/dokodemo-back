<?php

use \Business\User\LoginModel;
use \Business\User\ProfileModel;
use \TKS\JWTHelper;
use \TKS\ResponseHelper;

class SessionController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        // \Business\Mail\SendModel::init();
        return [200, 'success'];
    }

    public function save($request) {
        // 注意：由于 YAF 的 POST 只读机制
        // Bootstrap 已做了 json 接收的兼容，但 $request 对象并不会反应出来
        $account = $request->getPost('account', $_POST['account'] ?? '');
        if (empty($account)) {
            return [400, '缺少账号名'];
        }

        $login = new LoginModel();
        $result = $login->checkAccount($account);

        if ($result ===  false) {
            return [204, '账号不存在'];
        } else {
            $info = $login->fetchUserInfo();
            return [200, 'success', ['id' => $info['id']]];
        }
    }

    public function update($request) {
        $id = $request->getParam('id');
        $password = $request->getParam('password', $_POST['password'] ?? '');

        if (empty($password)) {
            return [400, '缺少必要项：password'];
        }
        $login = new LoginModel();
        $result = $login->checkPassword($password, $id);

        if ($result === true) {
            $info = $login->fetchUserInfo();
            ProfileModel::$id = $info['id'];
            $ext_info = ProfileModel::fetch();
            empty($ext_info) || $info += $ext_info;
            $jwt = JWTHelper::createJWT($info);
            ResponseHelper::addExtHeader('token', $jwt);
            return [200, 'success', $info];
        } else {
            return [401, '登录失败', ['message' => $login->getMessage()]];
        }
    }
}