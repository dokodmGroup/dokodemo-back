<?php

use \Our\ResponseHelper as OURRH;
use \Business\ArticleModel;

class ArticleController extends \Our\Controller_AbstractRest {

    public function index($request)
    {
        ArticleModel::$mode = ArticleModel::PORTAL;
        ArticleModel::$pnum = $request->get('pnum', '1');
        ArticleModel::$psize = $request->get('psize', '15');
        $result = ArticleModel::getList();
        foreach($result as &$item) {
            isset($item['cover']) && $item['cover'] = [$item['cover']];
        }
        OURRH::list($result, ArticleModel::$pnum, ArticleModel::$psize);
    }

    public function read($request)
    {
        $id = $request->getParam('id');
        if(!is_numeric($id)) {
            OURRH::tips(400, 'BAD REQUEST');
        }
        ArticleModel::$mode = ArticleModel::PORTAL;
        ArticleModel::$pnum = $request->get('pnum', '1');
        ArticleModel::$psize = $request->get('psize', '15');
        $result = ArticleModel::getList();
        OURRH::item($result[0]);
    }
}