<?php

namespace Our;

abstract class Controller_AbstractRest extends \Our\Controller_AbstractApi {

    protected $_method;
    protected $_request;

    public function init() {
        parent::init();
        $this->_request = \Yaf\Dispatcher::getInstance()->getRequest();
        $this->_method = $this->_request->getMethod();
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
            return $this->$action($this->_request);
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }
}