<?php

namespace DAO;

/**
 * 测试数据层
 */
class TestModel extends \DAO\AbstractModel {

    private static $_instance = null;

    public static function getInstance() {
        if (!(self::$_instance instanceof self)) {
            self::$_instance = new self();
        }

        return self::$_instance;
    }

}
