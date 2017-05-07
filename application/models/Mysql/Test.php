<?php

namespace Mysql;

class TestModel extends \Mysql\AbstractModel {

    /**
     * 表名
     * 
     * @var string
     */
    protected $_tableName = 'test';

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

}
