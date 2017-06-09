<?php
$user_info = posix_getpwuid(posix_geteuid());
header('X-Php-User: ' . json_encode($user_info));
error_reporting(E_ALL);
date_default_timezone_set("Asia/Shanghai");
mb_internal_encoding("UTF-8");
define("APPLICATION_PATH", realpath(dirname(__FILE__) . '/../'));
$app = new \Yaf\Application(APPLICATION_PATH . "/conf/application.ini", ini_get('yaf.environ'));
$app->bootstrap()->run();
