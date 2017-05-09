<?php
$user_info = posix_getpwuid(posix_geteuid());
header('X-Php-User: ' . json_encode($user_info));
error_reporting(E_ALL);
ini_set('display_errors', '1'); // 显示错误信息
if (isset($_SERVER['HTTP_ORIGIN'])) {
    header('Access-Control-Allow-Origin: ' . $_SERVER['HTTP_ORIGIN']);    
}
header('Access-Control-Allow-Credentials: true');
header('Access-Control-Allow-Methods: GET,POST,OPTIONS,PUT,DELETE');
header('Access-Control-Allow-Headers: DNT,X-Mx-ReqToken,Keep-Alive,User-Agent,X-Requested-With,If-Modified-Since,Cache-Control,Content-Type,X-Token');
if ($_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
    http_response_code(204);
    exit();
}
date_default_timezone_set("Asia/Shanghai");
mb_internal_encoding("UTF-8");
define("APPLICATION_PATH", realpath(dirname(__FILE__) . '/../'));
$app = new \Yaf\Application(APPLICATION_PATH . "/conf/application.ini", ini_get('yaf.environ'));
$app->bootstrap()->run();
