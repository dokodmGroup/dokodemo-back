<?php
use \TKS\ResponseHelper;
/**
 * 当有未捕获的异常, 则控制流会流到这里
 */
class ErrorController extends \Our\Controller_Abstract {

    public function init() {
        \Yaf\Dispatcher::getInstance()->disableView();
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
            ResponseHelper::json(500, '作业失败，请检查请求是否正确', $data);
        } else {
            ResponseHelper::json(404, '作业失败，请检查请求是否正确', $data);
        }
        return;
    }
}
