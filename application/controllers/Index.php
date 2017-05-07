<?php

class IndexController extends \Our\Controller_AbstractRest {

    public function init()
    {
        parent::init();
        header('Content-Type: application/json');
    }

    public function indexAction() {
        echo json_encode([
            'key' => '嘿，你的燚龘！', 
            'request_method' => $_SERVER['REQUEST_METHOD'],
            'your_payload' => (array) json_decode(file_get_contents('php://input'), 1),
            'your_query_string' => (array) $_GET,
        ]);
        http_response_code(202);
    }

    public function listAction() {
        $dao = \DAO\TestModel::getInstance();
        $result = $dao->fetchAll([], function($where){
            $where->like('field1', '1')
                ->or
                ->like('field1', '0');
        });
        echo json_encode($result);
        // $dao->debugDumpParams();
    }

}
