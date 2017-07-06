<?php

namespace Mysql;

/**
 * 用户个人信息表连接类
 */
class UserProfileModel extends \Mysql\AbstractModel {

    protected $_tableName = 'user_profile';
    protected $_primaryKey = 'user_id';
    private static $_instance = null;
    
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    public function find($id) : array {
        $resultSet = $this->_getDbTableGateway()->select(function ($select) use ($id) {
            $select->columns([
                'avatar'
            ]);
            $select->where(['user_id' => $id]);
        });
        $result    = $resultSet->current();
        if ($result) {
            return (array) $result;
        } else {
            return [];
        }
    }
}
