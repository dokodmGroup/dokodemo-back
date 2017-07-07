<?php

use \Business\User\RegisterModel;

class UserController extends \Our\Controller_AbstractRest {

    public function read($request)
    {
        $result = [];
        ResponseHelper::json(200, '', ['result' => $result]);
    }

    public function save($request)
    {
        $mode = $request->getPost('mode', $_POST['mode'] ?? 'check');
        $account = $request->getPost('account', $_POST['account'] ?? '');
        switch($mode) {
            case 'check':
                return $this->checkAccount($account);
                break;
            case 'submit':
                return $this->submitRegister($account, $request);
                break;
            default:
                return [400, 'Bad Request'];
                break;
        }
    }

    private function checkAccount(string $account)
    {
        if (empty($account)) {
            return [400, '账号不能为空'];
        }
        RegisterModel::$account = $account;
        if (RegisterModel::isEmail() === false) {
            return [400, '电邮格式不正确'];
        }
        if (RegisterModel::checkAccount() === false) {
            return [400, '此邮件已注册'];
        }

        return [200, '此邮件可以注册'];
    }

    private function submitRegister(string $account, $request)
    {
        $result = $this->checkAccount($account);
        if ($result[0] !== 200) {
            return $result;
        }
        $password = $request->getPost('password', $_POST['password'] ?? '');
        
        RegisterModel::$password = $password;
        if (RegisterModel::done() === true) {
            return [201, '账号创建成功'];
        } else {
            return [500, '账号创建失败'];
        }
    }
}