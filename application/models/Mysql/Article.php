<?php

namespace Mysql;

/**
 * 文章表连接类
 */
class ArticleModel extends \Mysql\AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = 'article';

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
