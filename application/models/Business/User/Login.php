<?php

namespace Business\User;

use \DAO\UserModel;

/**
 * 用户登录业务
 */
class LoginModel extends \Business\AbstractModel {

    private $_userInfo = [];
    private $_dao;
    private $_msg = '';
    private $_accessField = ['id', 'account', 'create_time'];

    public function __construct()
    {
        $this->_dao = UserModel::getInstance();
    }

    /**
     * 登录业务
     * 
     * @param array $params
     * @return
     */
    public function checkAccount(string $account) : bool {
        $result = $this->_dao->findByAccount($account);
        if(!empty($result) && $result['status'] === '1') {
            $this->_userInfo = $result;
            return true;
        } elseif(!empty($result)) {
            $this->_msg = '账号已被禁用，请联系站长或管理员';
            return false;
        } else {
            $this->_msg = '账号不存在，请检查你的电邮地址';
            return false;
        }
    }

    /**
     * 获取模型内处理的提示信息
     */
    public function getMessage() : string {
        return $str = (string) $this->_msg;
    }

    public function checkPassword(string $password, int $id = null) : bool {
        if (is_null($id) && !empty($this->_userInfo['id'])) {
            $id = $this->_userInfo['id'];
        } elseif (is_null($id)) {
            $this->_msg = '业务错误：没有载入用户信息！';
            return false;
        }

        $result = $this->_dao->findByUserId($id);

        if (!empty($result) && md5($password) === $result['password']) {
            $this->_userInfo = $this->_dao->findByUserId($id);
            return true;
        } elseif (empty($result)) {
            $this->_msg = '账号信息不正确';
            return false;
        } else {
            $this->_msg = '密码不正确';
            return false;
        }
    }

    public function fetchUserInfo() : array {
        if (empty($this->_userInfo)) {
            return [];
        }
        $info = [];
        foreach($this->_accessField as $field) {
            $info[$field] = $this->_userInfo[$field] ?? '';
        }
        return $info;
    }
}
