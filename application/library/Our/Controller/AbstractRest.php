<?php

namespace Our;

use \TKS\ResponseHelper;

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
                $action = '';
                break;
        }
        if (method_exists($this, $action)) {
            $result = $this->$action($this->_request);
            if (is_array($result)) {
                try {
                    if (!isset($result[0]) || !isset($result[1])) {
                        throw new \Exception();
                    }
                    ResponseHelper::json(
                        $result[0], 
                        $result[1], 
                        $result[2] ?? []
                    );
                } catch (\Exception $e) {
                    ResponseHelper::json(
                        500, 
                        'Error: The return result is array and do not validated'
                    );
                }
            } else {
                return $result;
            }
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }

    public function indexAction()
    {
        switch ($this->_method) {
            case 'GET':
                $action = 'index';
                break;
            case 'POST':
                $action = 'save';
                break;
            default:
                $action = '';
                break;
        }
        if (method_exists($this, $action)) {
            $result = $this->$action($this->_request);
            if (is_array($result)) {
                try {
                    if (!isset($result[0]) || !isset($result[1])) {
                        throw new \Exception();
                    }
                    ResponseHelper::json(
                        $result[0], 
                        $result[1], 
                        $result[2] ?? []
                    );
                } catch (\Exception $e) {
                    ResponseHelper::json(
                        500, 
                        'Error: The return result is array and do not validated'
                    );
                }
            } else {
                return $result;
            }
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }
}