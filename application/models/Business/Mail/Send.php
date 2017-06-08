<?php

namespace Business\Mail;
use \PHPMailer;

class SendModel extends \Business\AbstractModel {

    private static $_instance = null;

    public static function init(array $config)
    {
        if (!self::configValidate($config)) {
            throw new Exception('EMAIL CONFIG NOT VALIDATE.');
            return;
        }
        $mail = self::getInstance();
        $mail->IsSMTP();
        $mail->Port = $config['port'];
        $mail->Host = $config['host'];
        $mail->CharSet = $config['charset'];
        $mail->Encoding = $config['encoding'];
    }


    public static function getInstance(): PHPMailer
    {
        if (!(self::$_instance instanceof PHPMailer)) {
            self::$_instance = new PHPMailer();
        }

        return self::$_instance;
    }

    private static function configValidate(array $config): bool
    {
        $field = [
            'host', 'port', 'charset', 'encoding'
        ];
        $keys = array_keys($config);
        return empty(array_diff($field, $keys));
    }
}