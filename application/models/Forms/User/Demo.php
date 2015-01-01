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
            'require'  => true,
            "validate" => array(
                array("type" => "int", "min" => "1", "max" => "3", "msg" => "demo1不正确")
            ),
        ),
        'demo2' => array(
            'label'    => 'demo2',
            'name'     => 'demo2',
            'require'  => false,
            'default'  => "default value",
            "validate" => array(
                array("type" => "string", "min" => "6", "max" => "18", "msg" => "demo2不正确")
            ),
        ),
    );

}
