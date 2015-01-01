<?php

namespace Forms\User;

/**
 * 表单demo
 */
class DemoModel extends \Forms\AbstractModel {

    /**
     * 表单字段
     * 
     * @var array
     */
    protected $_fields = array(
        'demo1' => array(
            'label'    => 'demo1',
            'name'     => 'demo1',
            'require'  => true, //表示字段是否必须
            "validate" => array(
                array("type" => "int", "min" => "1", "max" => "3", "msg" => "demo1不正确")
            ),
        ),
        'demo2' => array(
            'label'    => 'demo2',
            'name'     => 'demo2',
            'require'  => false, //字段非必须
            'default'  => "123456", //如果字段没有传入则设置该默认值，require为false时可以设置
            "validate" => array(
                array("type" => "string", "min" => "6", "max" => "18", "msg" => "demo2不正确")
            ),
        ),
    );

    /**
     * 校验demo2字段
     * 
     * @return boolean
     */
    public function validateDemo2() {
        $demo2 = $this->getFieldValue("demo2");
        //这里可以进行更加复杂的校验

        return true;
    }

}
