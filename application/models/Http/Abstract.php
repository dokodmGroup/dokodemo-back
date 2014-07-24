<?php

namespace Http;

/**
 * http数据来源抽象类
 */
class AbstractModel {

    /**
     * 访问的host
     * 
     * @var string
     */
    protected $_host = '';

    /**
     * 发起HTTP请求
     * 
     * @param string $url
     * @param string $method
     * @param array $params
     * @param int $timeout
     * @return boolean
     */
    protected function _request($url, $method = "GET", $params = array(), $timeout = 30) {
        $paramString = http_build_query($params, '', '&', PHP_QUERY_RFC3986);
        if (strtoupper($method) == "GET") {
            $url .= "?" . $paramString;
        }

        $ch = curl_init($url);

        if (strtoupper($method) == "POST") {
            curl_setopt($ch, CURLOPT_POST, 1);
            curl_setopt($ch, CURLOPT_POSTFIELDS, $paramString);
        }

        curl_setopt($ch, CURLOPT_TIMEOUT, $timeout);
        curl_setopt($ch, CURLOPT_IPRESOLVE, CURL_IPRESOLVE_V4);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_BINARYTRANSFER, true);

        //检测是否是https访问
        if (strpos($url, 'https') === 0) {
            curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, FALSE);
            curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, FALSE);
        }

        $result = curl_exec($ch);

        //请求失败的处理方法
        if (curl_errno($ch)) {
            \Our\Log::getInstance()->write('请求http接口失败，请求url:' . $url . '，Curl error: ' . curl_error($ch));
            return false;
        }
        curl_close($ch);

        return $result;
    }

    public function __clone() {
        trigger_error('Clone is not allow!', E_USER_ERROR);
    }

}
