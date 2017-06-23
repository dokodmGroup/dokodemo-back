<?php

namespace Business;

use \DAO\ArticleModel as DAO;

class ArticleModel extends AbstractModel
{
    public static $pnum = 1;
    public static $psize = 15;

    private static $_adminField = [
        'id',
        'uid',
        'title',
        'subtitle',
        'source',
        'source_url',
        'context',
        'create_time',
        'publish_time',
        'status'
    ];

    private static $_softDeleteCondition = [
        'status > -1',
        'id < 10',
    ];

    private static $_adminOrder = 'id desc';


    public static function getList()
    {
        $pnum = is_numeric(self::$pnum) ? (int)self::$pnum : 1;
        $psize = is_numeric(self::$psize) ? (int)self::$psize : 15;
        $offset = $pnum * self::$psize - $psize;
        return DAO::getInstance()->fetchAll(
            self::$_adminField,
            self::$_softDeleteCondition,
            self::$_adminOrder,
            self::$psize,
            $offset
        );
    }
}