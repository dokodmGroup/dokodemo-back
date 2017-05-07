<?php

namespace Our;

abstract class Controller_AbstractRest extends \Our\Controller_AbstractApi {

    private $_method;

    public function init() {
        parent::init();
        $request = \Yaf\Dispatcher::getInstance()->getRequest();
        $this->_method = $request->getMethod();
    }

    public function infoAction()
    {
        switch ($this->_method) {
            case 'GET':
                $action = 'read';
                break;
            case 'PUT':
                $action = 'update';
                break;
            case 'DELETE':
                $action = 'delete';
                break;
            default:
                break;
        }
        if (method_exists($this, $action)) {
            return $this->$action;
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }
}