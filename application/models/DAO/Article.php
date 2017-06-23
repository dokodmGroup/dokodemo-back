<?php

namespace DAO;


/**
 * 文章数据层
 */
class ArticleModel extends AbstractModel {

    private $_dbModel;

    public function __construct() {
        $this->_dbModel = \Mysql\UserModel::getInstance();
    }
    
    /**
     * 根据一定条件查找用户
     */
    public function find(mixed $opt) : array {
        if (is_numeric($opt)) {
            return $this->findByUserId($opt);
        }
    }

    /**
     * 根据用户编号查找数据
     * 
     * @param int $userId
     * @return array
     */
    public function findByUserId(int $userId) : array {
        // $redis = \Redis\Db0\UserModel::getInstance();
        // $user  = $redis->find($userId);
        // if (!$user) {
            $mysql = \Mysql\UserModel::getInstance();
            $user  = $mysql->find($userId);
            // if ($user) {
                // $redis->update($userId, $user);
            // }
        // }
        return $user;
    }

    public function findByAccount(string $account) : array {
        return $this->_dbModel->findByAccount($account);
    }

    /**
     * 类实例
     * 
     * @var \DAO\UserModel
     */
    private static $_instance = null;

    /**
     * 获取类实例
     * 
     * @return \DAO\UserModel
     */
    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
