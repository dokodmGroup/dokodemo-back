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
