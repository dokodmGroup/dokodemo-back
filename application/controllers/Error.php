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
            'infomation' => '作业失败，请检查请求是否正确', 
            'message' => $exception->getMessage(),
            'trace' => $exception->getTrace(),
        ];
        if ($exception->getCode() > 1000) {
            //这里可以捕获到应用内抛出的异常
            http_response_code(500);
        } else {
            http_response_code('404');
        }
        echo json_encode($data);
        return;
    }

}
