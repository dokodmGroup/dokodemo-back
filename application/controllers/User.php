<?php

class UserController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = \Business\Mail\SendModel::init();
        echo json_encode(['result' => $result]);
    }
}