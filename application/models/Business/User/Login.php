<?php

namespace Business\User;

use \DAO\UserModel;

/**
 * 用户登录业务
 */
class LoginModel extends \Business\AbstractModel {

    private $_userInfo = [];
    private $_dao;

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
        if(!empty($result)) {
            $this->_userInfo = $result;
            return true;
        } else {
            return false;
        }
    }

    

}
