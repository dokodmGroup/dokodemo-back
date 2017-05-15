<?php



class UserController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        echo json_encode(['result' => $result]);
    }
}