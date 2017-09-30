<?php

namespace DAO;

/**
 * 数据读取模型抽象类
 *
 * @package DAO
 * @author iceup <sjlinyu@qq.com>
 * @re:creator Tsukasa Kanzaki <tsukasa.kzk@gmail.com>
 */
abstract class AbstractModel
{
    private static $_condition = [];
    private static $_order     = [];
    private static $_limit     = [];
    private static $_keyword   = '';
    private static $_instance  = null;

    /**
     * 获取类实例
     *
     * @return \DAO\AbstractModel
     */
    public static function getInstance()
    {
        $called_class = get_called_class();
        if (!(self::$_instance instanceof $called_class)) {
            self::$_instance = new $called_class();
        }
        return self::$_instance;
    }

    /**
     * 设置查询条件
     *
     * @param array $condition
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function setCondition(array $condition): bool
    {
        self::$_condition = array_merge(self::$_condition, $condition);
        return true;
    }

    /**
     * 获取查询条件
     *
     * @return array
     * Kanzaki Tsukasa
     */
    protected static function getCondition(): array
    {
        return self::$_condition;
    }

    /**
     * 设置搜索关键词
     *
     * @param string $keyword
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function setSearch(string $keyword): bool
    {
        self::$_keyword = $keyword;
        return true;
    }

    /**
     * 获取搜索关键词
     *
     * @return string
     * Kanzaki Tsukasa
     */
    protected static function getSearch(): string
    {
        return self::$_keyword;
    }

    /**
     * 追加或覆盖排序条件
     *
     * @param string $key
     * @param string $action
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function appendOrder(string $key, string $action): bool
    {
        if (isset(self::$_order[$key])) {
            unset(self::$_order[$key]);
        }
        self::$_order[$key] = $action;
        return true;
    }

    /**
     * 获取查询排序
     *
     * @return string
     * Kanzaki Tsukasa
     */
    protected static function getOrder(): string
    {
        $order = [];
        foreach (self::$_order as $key => $value) {
            $order[] = "{$key} {$value}";
        }
        return implode(',', $order);
    }

    /**
     * 获取列表
     *
     * @return array
     * Kanzaki Tsukasa
     */
    // abstract public static function getList(): array;

    /**
     * 处理搜索关键词
     *
     * @return bool
     * Kanzaki Tsukasa
     */
    // abstract protected static function dealSearch(): bool;

    /**
     * 捕获dao中没有的方法，直接访问mysql中相应的类的方法
     *
     * @param string $method
     * @param array $args
     * @return mixd
     */
    public function __call($method, $args)
    {
        $className      = get_class($this);
        $mysqlClassName = '$mysql = ' . str_replace('DAO', '\Mysql', $className) . '::getInstance();';
        eval($mysqlClassName);

        $excutePhp = '$result = $mysql->$method(_args_);';

        $string = '';
        foreach ($args as $key => $arg) {
            $string .= '$args[' . $key . '],';
        }

        $excutePhp = str_replace('_args_', rtrim($string, ','), $excutePhp);

        eval($excutePhp);

        return $result;
    }

}
