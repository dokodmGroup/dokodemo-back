<?php
use \TKS\ResponseHelper;

class IndexController extends \Our\Controller_AbstractRest {

    public function init()
    {
        parent::init();
    }

    public function indexAction() {
        ResponseHelper::json(
            202,
            'success',
            [
            'key' => '嘿，你的燚龘！', 
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'your_payload' => (array) json_decode(file_get_contents('php://input'), 1),
            'your_query_string' => (array) $_GET,
        ]);
    }

    public function listAction() {
        $dao = \DAO\TestModel::getInstance();
        $result = $dao->fetchAll([], function($where){
            $where->like('field1', '1')
                ->or
                ->like('field1', '0');
        });
        ResponseHelper::json(
            202,
            'success',
            $result);
    }

}
