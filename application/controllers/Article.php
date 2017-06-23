<?php

use \TKS\ResponseHelper;
use \Business\ArticleModel;

class ArticleController extends \Our\Controller_AbstractRest {

    public function read()
    {
        $result = [];
        ResponseHelper::json(200, '', ['result' => $result]);
    }

    public function index($request)
    {
        ArticleModel::$mode = ArticleModel::PORTAL;
        ArticleModel::$pnum = $request->get('pnum', '1');
        ArticleModel::$psize = $request->get('psize', '15');
        $result = ArticleModel::getList();
        if (empty($result)) {
            \TKS\ResponseHelper::json(204, '没有数据');
        } else {
            \TKS\ResponseHelper::json(200, 'success', [
                'pnum' => \Business\ArticleModel::$pnum,
                'psize' => \Business\ArticleModel::$psize,
                'data' => $result
            ]);
        }
    }
}