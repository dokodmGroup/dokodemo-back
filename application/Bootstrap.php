<?php

/**
 * 所有在Bootstrap类中, 以_init开头的方法, 都会被Yaf调用,
 * 这些方法, 都接受一个参数:\Yaf\Dispatcher $dispatcher
 * 调用的顺序, 和声明的顺序相同
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract {

    /**
     * Yaf的配置缓存
     *
     * @var \Yaf\Config\Ini
     */
    protected $_config = null;

    /**
     * 把配置存到注册表
     */
    public function _initConfig() {
        $config = \Yaf\Application::app()->getConfig();

        $this->_config = $config;
        \Yaf\Registry::set('config', $config);
    }

    /**
     * 自定义路由规则
     * 
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initRoute(\Yaf\Dispatcher $dispatcher) {
        $router = \Yaf\Dispatcher::getInstance()->getRouter();

        $config = new \Yaf\Config\Ini(APPLICATION_PATH . '/conf/route.ini', 'common');
        if ($config->routes) {
            $router->addConfig($config->routes);
        }
    }

    /**
     * 获取url.ini配置的地址
     * 
     * @param string $name
     * @return string 
     */
    public static function getUrlIniConfig($name) {
        static $config = null;
        if ($config === null) {
            $config = new \Yaf\Config\Ini(APPLICATION_PATH . '/conf/url.ini', ini_get('yaf.environ'));
        }

        $urlConf = $config->get('config.url');
        if ($urlConf === null) {
            return null;
        }
        return $urlConf === null ? null : $urlConf[$name];
    }

}
