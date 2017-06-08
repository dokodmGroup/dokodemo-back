<?php

use \TKS\ResponseHelper;

/**
 * Bootstrap引导程序
 * 所有在Bootstrap类中定义的, 以_init开头的方法, 都会被依次调用
 * 而这些方法都可以接受一个Yaf_Dispatcher实例作为参数.
 */
class Bootstrap extends \Yaf\Bootstrap_Abstract {

    /**
     * 把配置存到注册表
     */
    public function _initConfig() {
        $config = \Yaf\Application::app()->getConfig();
        \Yaf\Registry::set('config', $config);
    }

    /**
     * 路由规则定义，如果没有需要，可以去除该代码
     * 
     * @param Yaf_Dispatcher $dispatcher
     */
    public function _initRoute(\Yaf\Dispatcher $dispatcher) {
        $router = \Yaf\Dispatcher::getInstance()->getRouter();
        $config = new \Yaf\Config\Ini(APPLICATION_PATH . '/conf/route.ini', 'common');
        if ($config->routes) {
            $router->addConfig($config->routes);
        }
        $route = new \Yaf\Route\Rewrite(
            '/portal/Hello', 
            ['controller' => 'Index', 'action' => 'index']
        );
        $router->addRoute('portal-hello', $route);
        $resource_index = new \Yaf\Route\Rewrite(
            '/portal/:target',
            ['module' => 'Index','controller' => ':target', 'action' => 'index']
        );
        $router->addRoute('portal-resource-index', $resource_index);
        $resource_info = new \Yaf\Route\Rewrite(
            '/portal/:target/:id',
            ['module' => 'Index','controller' => ':target', 'action' => 'info']
        );
        $router->addRoute('portal-resource-info', $resource_info);
    }

    public function _initAutoload(\Yaf\Dispatcher $dispatcher) {
        // Composer 支援
        \Yaf\Loader::import(APPLICATION_PATH . '/vendor/autoload.php');
    }

    /**
     * PHP输入流处理支援，针对 PUT 请求和数据格式为 json 的请求
     */
    public function _initPhpinput(\Yaf\Dispatcher $dispatcher) {
        // PHP INPUT判定和预处理
        // 兼容Content Type为application/json的数据接收
        if (isset($_SERVER['CONTENT_TYPE']) && preg_match("/\w+\/\w+([\-\+]\w+)*/", $_SERVER['CONTENT_TYPE'], $result)) {
            $content_type = $result[0];
            switch ($content_type) {
                case 'multipart/form-data':
                case 'application/x-www-form-urlencoded':
                    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        try {
                            @$this->parsePut();
                        } catch (\Exception $e) {
                            
                        }
                        global $_PUT;
                        $_POST = $_PUT;
                    }
                    break;
                case 'application/json':
                    if ($_SERVER['REQUEST_METHOD'] == 'PUT') {
                        global $_PUT;
                        $php_input = file_get_contents('php://input');
                        $_PUT = (array)json_decode($php_input, 1);
                        $_POST = $_PUT;
                    } else {
                        $php_input = file_get_contents('php://input');
                        $_POST = (array)json_decode($php_input, 1);
                    }

                    break;
                case 'application/xml':
                case 'text/xml':
                    $php_input = file_get_contents('php://input');
                    // 听说并没有成熟的xml解析工具？
                    break;
                default:
                    if (!is_array($_POST)) {
                        $_POST = [];
                    }
                    break;
            } 
        } elseif (!is_array($_POST)) {
            $_POST = [];
        }
    }

    public function _initCrossAccess(\Yaf\Dispatcher $dispatcher) 
    {
        ResponseHelper::setHeader('Access-Control-Allow-Origin', $_SERVER['HTTP_ORIGIN'] ?? '');
        ResponseHelper::setHeader('Access-Control-Allow-Credentials', 'true');
        ResponseHelper::setHeader('Access-Control-Allow-Methods', 'GET,POST,OPTIONS,PUT,DELETE');
        ResponseHelper::setHeader(
            'Access-Control-Allow-Headers', 
            implode(',',[
                'DNT',
                'X-Mx-ReqToken',
                'Keep-Alive',
                'User-Agent',
                'X-Requested-With',
                'If-Modified-Since',
                'Cache-Control',
                'Content-Type',
                'X-Token',
                'X-Tips'
            ])
        );
        if (isset($_SERVER['REQUEST_METHOD']) &&
        $_SERVER['REQUEST_METHOD'] === 'OPTIONS') {
            ResponseHelper::json(204);
        }
    }

    public function _initResponse()
    {
        ResponseHelper::setVersion('0.0.1');
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
            throw new \Exception("config.url is not exist");
        }
        if ($urlConf[$name] === null) {
            throw new \Exception("config.url" . $name . " is not exist");
        }
        return $urlConf[$name];
    }

    /**
     * PUT Data Support
     * Source From
     * http://stackoverflow.com/questions/9464935/php-multipart-form-data-put-request
     * Thanks!
     */
    private function parsePut()
    {
        global $_PUT;

        /* PUT data comes in on the stdin stream */
        $putdata = fopen("php://input", "r");

        /* Open a file for writing */
        // $fp = fopen("myputfile.ext", "w");

        $raw_data = '';

        /* Read the data 1 KB at a time
        and write to the file */
        while ($chunk = fread($putdata, 1024)) {
            $raw_data .= $chunk;
        }

        /* Close the streams */
        fclose($putdata);

        // Fetch content and determine boundary
        $boundary = substr($raw_data, 0, strpos($raw_data, "\r\n"));

        if (empty($boundary)) {
            parse_str($raw_data, $data);
            $GLOBALS['_PUT'] = $data;
            return $data;
        }

        // Fetch each part
        $parts = array_slice(explode($boundary, $raw_data), 1);
        $data = array();

        foreach ($parts as $part) {
            // If this is the last part, break
            if ($part == "--\r\n") {
                break;
            }

            // Separate content from headers
            $part = ltrim($part, "\r\n");
            list($raw_headers, $body) = explode("\r\n\r\n", $part, 2);

            // Parse the headers list
            $raw_headers = explode("\r\n", $raw_headers);
            $headers = array();
            foreach ($raw_headers as $header) {
                list($name, $value) = explode(':', $header);
                $headers[strtolower($name)] = ltrim($value, ' ');
            }

            // Parse the Content-Disposition to get the field name, etc.
            if (isset($headers['content-disposition'])) {
                $filename = null;
                $tmp_name = null;
                preg_match(
                    '/^(.+); *name="([^"]+)"(; *filename="([^"]+)")?/',
                    $headers['content-disposition'],
                    $matches
                );
                list(, $type, $name) = $matches;

                //Parse File
                if (isset($matches[4])) {

                    //if labeled the same as previous, skip
                    if (isset($_FILES[$matches[2]])) {
                        continue;
                    }

                    //get filename
                    $filename = $matches[4];

                    //get tmp name
                    $filename_parts = pathinfo($filename);
                    $tmp_name = tempnam(ini_get('upload_tmp_dir'), 'php');
                    // $tmp_name       = tempnam(ini_get('upload_tmp_dir'), $filename_parts['filename']);

                    //populate $_FILES with information, size may be off in multibyte situation
                    if (strstr($matches[2], '[]') === false) {
                        $file_key = $matches[2];
                        $_FILES[$file_key] = array(
                            'error' => 0,
                            'name' => $filename,
                            'tmp_name' => $tmp_name,
                            'size' => strlen($body),
                            'type' => $value,
                        );
                    } else {
                        $file_key = str_replace('[]', '', $matches[2]);
                        $_FILES[$file_key]['error'][] = 0;
                        $_FILES[$file_key]['name'][] = $filename;
                        $_FILES[$file_key]['tmp_name'][] = $tmp_name;
                        $_FILES[$file_key]['size'][] = strlen($body);
                        $_FILES[$file_key]['type'][] = $value;
                    }

                    //place in temporary directory
                    file_put_contents($tmp_name, $body);
                } //Parse Field
                else {
                    if (strstr($name, '[]') === false) {
                        $data[$name] = substr($body, 0, strlen($body) - 2);
                    } else {
                        $sr_name = str_replace('[]', '', $name);
                        $data[$sr_name][] = substr($body, 0, strlen($body) - 2);
                    }
                }
            }

        }
        $GLOBALS['_PUT'] = $data;
        return $data;
    }

}
