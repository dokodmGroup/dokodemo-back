<?php

namespace Our;

abstract class Controller_AbstractRest extends \Our\Controller_AbstractApi {

    protected $_method;
    protected $_request;

    public function init() {
        parent::init();
        $this->_request = \Yaf\Dispatcher::getInstance()->getRequest();
        $this->_method = $this->_request->getMethod();
        header('Content-Type: application/json;charset=utf-8');
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
            $result = $this->$action($this->_request);
            if (is_array($result)) {
                try {
                    header('X-Info: ' . urlencode($result[1]));
                    http_response_code($result[0]);
                    echo json_encode($result[2]);
                } catch (\Exception $e) {
                    header('X-Info: ' . urlencode('Error: The return result is array and do not validated'));
                    http_response_code(500);
                    echo json_encode([]);
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
                break;
        }
        if (method_exists($this, $action)) {
            $result = $this->$action($this->_request);
            if (is_array($result)) {
                try {
                    if (!isset($result[0]) || !isset($result[1])) {
                        throw new \Exception();
                    }
                    header('X-Info: ' . urlencode($result[1]));
                    http_response_code($result[0]);
                    echo json_encode($result[2] ?? []);
                } catch (\Exception $e) {
                    header('X-Info: ' . urlencode('Error: The return result is array and do not validated'));
                    http_response_code(500);
                    echo json_encode([]);
                }
            } else {
                return $result;
            }
        } else {
            throw new \Yaf\Exception\LoadFailed\Action('请求方法响应动作不存在');
        }
    }
}