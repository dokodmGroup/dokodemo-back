<?php

class IndexController extends \Our\Controller_AbstractIndex {

    public function indexAction() {
        $userRedis = \Redis\Db0\UserModel::getInstance();
        $userRedis->getRedis();
        $userRedis->update("1", array("hehe"));
        var_dump($userRedis->find(1));

//        sleep(3);
//        
//        var_dump($testRedis->find(1));
    }

}
