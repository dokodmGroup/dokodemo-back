<?php

/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends \Our\Controller_Abstract {

    public function init() {
        \Yaf\Dispatcher::getInstance()->disableView();
        header('Content-Type: application/json;charset=utf-8,version=0.0.1');
    }

    public function errorAction($exception) {
        $dispatcher = \Yaf\Dispatcher::getInstance();
        $data = [
            'code' => $exception->getCode(),
            'infomation' => '路由分发失败', 
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ];
        if ($exception->getCode() > 100000) {
            //这里可以捕获到应用内抛出的异常
            http_response_code(500);
            return;
        } else {
            http_response_code($exception->getCode());
        }
        echo json_encode($data);
        return;
    }

}
