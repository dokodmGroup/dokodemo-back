<?php

/**
 * 用户中心控制器
 */
class UserController extends \Our\Controller_AbstractApi {

    /**
     * 表单测试
     */
    public function loginAction() {
        $form = new \Forms\User\LoginModel($this->getRequest()->getParams());
        if (!$form->validate()) {
            echo "表单校验没有通过，相关字段的错误信息：";
            var_dump($form->getFieldMessage());
            exit();
        }
        echo "表单校验通过，所有字段的值：";
        var_dump($form->getFieldValue());
        
    }

}
