<?php

use \Business\User\LoginModel;

class SessionController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        \Business\Mail\SendModel::init();
        echo json_encode(['result' => $result]);
    }

    public function save($request) {
        // 注意：Bootstrap 已做了 json 接收的兼容，但是 $request 对象并没有反应出来
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
            return [200, '', ['id' => $info['id']]];
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
            header('X-Token: ');
            return [200, 'success'];
        } else {
            return [401, '登录失败', ['message' => $login->getMessage()]];
        }
    }
}