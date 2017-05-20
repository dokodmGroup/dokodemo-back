<?php

namespace TKS;

/**
 * 响应辅助类
 *
 * 编写时参考了 TOP THINK 的 Response 类，Thanks!
 * Kanzaki Tsukasa
 */
class ResponseHelper {
    
    private static $extHeader = [];
    private static $header = [];
    private static $defaultTips = 'Done';
    private static $charset = 'utf-8';
    private static $version = '';

    /**
     * 以 JSON 形式响应
     *
     * @param int $stateCode
     * @param string $tips
     * @param array $body
     * @return void
     * Kanzaki Tsukasa
     */
    public static  function json(int $stateCode, string $tips = null, array $body = null)
    {
        is_null($body) && $body = [];
        is_null($tips) && $tips = '';
        http_response_code($stateCode);
        $tips = urlencode($tips);
        self::$extHeader[] = "X-Tips: {$tips}";
        self::headerResponse();
        $charset = self::$charset;
        $content_type = "Content-Type: application/json;charset={$charset}";
        if (!empty(self::$version)) {
            $version = self::$version;
            $content_type .= ",version={$version}";
        }
        header($content_type);
        echo json_encode($body);
        if(function_exists('fastcgi_finish_request')) {
            fastcgi_finish_request();
        }
    }

    /**
     * 扩展头部添加
     *
     * @param string $key
     * @param string $value
     * @return void
     * Kanzaki Tsukasa
     */
    public static function addExtHeader(string $key, string $value)
    {
        $tmp_key = str_replace('_', '-', $key);
        $tmp_key = ucwords(strtolower($tmp_key), '-');
        self::$extHeader[] = "X-{$tmp_key}: {$value}";
    }

    public static function setHeader(string $key, string $value) 
    {
        self::$header[] = "{$key}: {$value}";
    }

    public static function setVersion(string $version) 
    {
        self::$version = $version;
    }

    /**
     * 头部响应
     *
     * @return void
     * Kanzaki Tsukasa
     */
    private static function headerResponse() 
    {
        $headers = array_merge(self::$header, self::$extHeader);
        foreach($headers as $header) {
            header($header);
        }
    }
}