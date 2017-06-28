<?php

namespace Business;

use \DAO\ArticleModel as DAO;

class ArticleModel extends AbstractModel
{
    use \TKS\traits\LabelHelper;

    const PORTAL = 1;
    const ADMIN = 2;

    public static $pnum = 1;
    public static $psize = 15;
    public static $condition = [];
    public static $mode = 1;

    private static $_adminField = [
        'id',
        'uid',
        'title',
        'subtitle',
        'source',
        'source_url',
        'comment',
        'like',
        'context',
        'create_time',
        'publish_time',
        'status'
    ];

    private static $_portalField = [
        'id',
        'uid',
        'title',
        'subtitle',
        'cover',
        'source',
        'source_url',
        'comment',
        'like',
        'context',
        'publish_time',
    ];

    private static $_softDeleteCondition = [
        'status > -1',
        'id < 10',
    ];

    private static $_adminOrder = 'id desc';


    public static function getList(): array
    {
        $pnum = is_numeric(self::$pnum) ? (int)self::$pnum : 1;
        $psize = is_numeric(self::$psize) ? (int)self::$psize : 15;
        $offset = $pnum * self::$psize - $psize;
        $condition = self::$_softDeleteCondition;
        if (!empty(self::$condition) && is_array(self::$condition)) {
            $condition += self::$condition;
        }

        if (self::$mode === self::PORTAL) {
            $field = self::$_portalField;
        } else {
            $field = self::$_adminField;
        }
        return DAO::getInstance()->fetchAll(
            $field,
            $condition,
            self::$_adminOrder,
            self::$psize,
            $offset
        );
    }

    public static function getItem(int $id): array
    {
        
    }
}