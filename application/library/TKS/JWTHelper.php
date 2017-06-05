<?php

namespace TKS;

/**
 * 增强型的 JWT 辅助类
 * 采用基于 OpenSSL 的 RSA 签名方案以优化校验
 */

class JWTHelper
{
    // 配置型变量
    private static $hashMethod = 'SHA256';
    private static $ttl = 3600;
    private static $privateKeyPath = '../private.pem';
    private static $issuer = 'Dokodemo Content Manage System';
    // 检测 JWT 是否过期，仅内部使用
    private static $outOfTtl = false;
    // JWT 实体
    private static $jwt = '';

    // 错误集
    private $_errors = [];

    public static function setTtl(int $ttl)
    {
        self::$ttl = $ttl;
    }

    /**
     * 创建 Token
     *
     * @param array $extData 扩展数据
     * @return string
     * Kanzaki Tsukasa
     */
    public static function createJWT(array $extData): string 
    {
        $header = self ::encodeHeader();
        $payload = self::encodePayload($extData);
        $body = "{$header}.{$payload}";
        $signature = self::sign($body);
        self::$jwt = "{$body}.{$signature}";
        return $jwt = self::$jwt;
    }

    /**
     * 校验 Token
     * Token 格式不正确，或签名不对返回假
     * @param string $jsonWebTokenString
     * @param array $extData
     * @return bool
     * Kanzaki Tsukasa
     */
    public static function checkJWT(string $jsonWebTokenString, array &$extData = null): bool
    {
        $info = explode('.', $jsonWebTokenString);
        if (count($info) ===  3) {
            $body = "{$info[0]}.{$info[1]}";
            if (self::sign($body) !== $info[2]) {
                return false;
            } else {
                $payload = self::decodePayload($info[1]);
                $extData = $payload['ext'];
                return true;
            }
        } else {
            return false;
        }
    }

    // 基础函数 START
    /**
     * 编码 JWT 头部
     *
     * @return string
     * Kanzaki Tsukasa
     */
    private static function encodeHeader(): string 
    {
        return base64_encode(json_encode([
            'alg' => 'OPENSSL_' . self::$hashMethod,
            'typ' => 'JWT'
        ]));
    }
    /**
     * 解码 JWT 头部
     *
     * @param string $headerString
     * @return array
     * Kanzaki Tsukasa
     */
    private static function decodeHeader(string $headerString): array 
    {
        return json_decode(base64_decode($headerString), 1);
    }

    /**
     * 编码 JWT 载荷
     *
     * @param array $extData
     * @return string
     * Kanzaki Tsukasa
     */
    private static function encodePayload(array $extData): string
    {
        return (string) base64_encode(json_encode([
            'iss' => self::$issuer,
            'sub' => $extData['id'] ?? ($extData['uid'] ?? 0),
            'ext' => $extData,
            'jti' => self::createMd5(),
            'exp' => time() + self::$ttl
        ]));
    }
    /**
     * 解码 JWT 载荷
     *
     * @param string $payLoadString
     * @return array
     * Kanzaki Tsukasa
     */
    private static function decodePayload(string $payLoadString): array
    {
        $payload = json_decode(base64_decode($payLoadString), 1);
        if (self::checkPayload($payload) === false) {
            self::$outOfTtl = true;
        }
        return $payload;
    }

    /**
     * 创建随机 MD5
     *
     * @return string
     * Kanzaki Tsukasa
     */
    private static function createMd5(): string 
    {
        return md5(uniqid(time()));
    }

    /**
     * 签名
     *
     * @param string $body
     * @return string
     * Kanzaki Tsukasa
     */
    private static function sign(string $body): string
    {
        $pkeyid = openssl_pkey_get_private('file://' . realpath(self::$privateKeyPath));
        $digest = openssl_digest($body, self::$hashMethod);
        // 私钥不正确会产生 warning 提示
        // 暂未知如何捕获，这样处理吧
        @openssl_sign($digest, $signature, $pkeyid);
        $this->errors[] = error_get_last();
        return base64_encode($signature);
    }

    /**
     * 检查 payload 是否过期
     *
     * @param array $payload
     * @return bool
     * Kanzaki Tsukasa
     */
    private static function checkPayload(array $payload): bool
    {
        return (bool) (isset($payload['exp']) && (time() < $payload['exp']));
    }
    // 基础函数 STOP
}
