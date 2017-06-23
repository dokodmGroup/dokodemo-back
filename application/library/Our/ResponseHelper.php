<?php

namespace Our;
use \TKS\ResponseHelper as TKSRH;

class ResponseHelper
{
    public static $format = 'json';

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
        TKSRH::json($statusCode, $xTips);
    }

    public static function permissionDenied()
    {
        TKSRH::json(403, 'PERMISSION DENIED');
    }

    public static function notLogin()
    {
        TKSRH::json(401, 'PLEASE LOGIN');
    }

    public static function authFailed()
    {
        TKSRH::json(401, 'Auth Failed');
    }

    public static function notValidateRequest()
    {
        TKSRH::json(400, 'Request Not Validate');
    }
}