<?php

namespace Our\Test\PHPUnit;

class TestCase extends \PHPUnit_Framework_TestCase {

    /**
     * 构造方法，调用application实例化方法
     */
    public function __construct() {
        $this->getApplication();
        parent::__construct();
    }

    /**
     * 设置application
     */
    public function setApplication() {
        $application = new \Yaf\Application(APPLICATION_PATH . "/conf/application.ini");
        $application->bootstrap();
        \Yaf\Registry::set('application', $application);

        return $application;
    }

    /**
     * 获取application
     * 
     * @return \Yaf\Application
     */
    public function getApplication() {
        $application = \Yaf\Registry::get('application');
        if (!$application) {
            $application = $this->setApplication();
        }

        return $application;
    }

}
