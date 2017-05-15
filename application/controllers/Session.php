<?php

use \Business\User\LoginModel;

class SessionController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
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
}