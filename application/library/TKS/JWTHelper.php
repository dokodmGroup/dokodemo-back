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
     * @param mixed $body
     * @return string
     * Kanzaki Tsukasa
     */
    private static function sign(mixed $body): string
    {
        $key = openssl_pkey_get_private('file://' . realpath(self::$privateKeyPath));
        $digest = openssl_digest($body, self::$hashMethod);
        openssl_sign($digest, $signature, $key);
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
