<?php

namespace Our;
use \TKS\ResponseHelper as TKSRH;

class ResponseHelper
{
    public static function json(int $status, string $xTips, array $data = null)
    {
        if (empty($data) && self::isDebug()) {
            $data = ['tips' => $xTips];
        }
        return TKSRH::json($status, $xTips, $data);
    }

    public static function list(array $data, int $pnum, int $psize)
    {
        if (empty($data)) {
            TKSRH::json(204, 'NO DATA');
        } else {
            TKSRH::json(200, 'SUCCESS', [
                'pnum' => $pnum,
                'psize' => $psize,
                'data' => $data
            ]);
        }
    }

    public static function item(array $info) 
    {
        if (empty($info)) {
            TKSRH::json(204, 'NO DATA');
        } else {
            TKSRH::json(200, 'SUCCESS', $info);
        }
    }

    public static function tips(int $statusCode, string $xTips)
    {
        if (self::isDebug()) {
            $data = ['tips' => $xTips];
        } else {
            $data = [];
        }
        TKSRH::json($statusCode, $xTips, $data);
    }

    public static function permissionDenied()
    {
        TKSRH::tips(403, 'PERMISSION DENIED');
    }

    public static function notLogin()
    {
        TKSRH::tips(401, 'PLEASE LOGIN');
    }

    public static function authFailed()
    {
        TKSRH::tips(401, 'Auth Failed');
    }

    public static function notValidateRequest()
    {
        TKSRH::tips(400, 'Request Not Validate');
    }

    private static function isDebug()
    {
        return isset($_GET['debug']) && intval($_GET['debug']) === 1;
    }
}