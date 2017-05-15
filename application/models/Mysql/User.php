<?php

namespace Mysql;

/**
 * 用户表连接类
 */
class UserModel extends \Mysql\AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = 'user';

    /**
     * 主键
     * 
     * @var string
     */
    protected $_primaryKey = 'id';

    /**
     * 类实例

     * @var \Mysql\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \Mysql\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

    /**
     * 使用DbTable网关来查询账户
     * 返回包含密码字段，请留意使用
     */
    public function findByAccount(string $account) : array {
        $resultSet = $this->_getDbTableGateway()->select(function($select) use ($account) {
            $select->columns(['id', 'account', 'password', 'create_time', 'status']);
            $select->where(['account' => $account]);
            $select->where('`delete_time` is null');
        });
        $first = $resultSet->current();
        if (!is_array($first)) {
            return [];
        } else {
            return $first;
        }
    }

    public function find($id) : array {
        $resultSet = $this->_getDbTableGateway()->select(function ($select) use ($id) {
            $select->columns(['id', 'account', 'password', 'create_time', 'status']);
            $select->where(['id' => $id]);
            $select->where('delete_time is null');
        });
        $result    = $resultSet->current();
        if ($result) {
            return (array) $result;
        } else {
            return [];
        }
    }
}
