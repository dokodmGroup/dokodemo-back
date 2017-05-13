<?php

date_default_timezone_set("Asia/Shanghai");
mb_internal_encoding("UTF-8");
define("APPLICATION_PATH", realpath(dirname(__FILE__) . '/../'));

require_once(APPLICATION_PATH . '/vendor/autoload.php');