<?php

namespace Business\Mail;
use \PHPMailer;

class SendModel extends \Business\AbstractModel {

    public static function init()
    {
        $mail = new \PHPMailer();
        $mail->IsSMTP();
        $mail->Port = 25;
        $mail->Host = 'ssl://smtp.qq.com:465';
        $mail->SMTPAuth = true;
        $mail->Username = 'shengjie@sj33333.com';
        $mail->Password = 'sj2213@@!#';
        $mail->From = 'shengjie@sj33333.com';
        $mail->FromName = 'Sj Software';
        $mail->CharSet = 'UTF-8';
        $mail->Encoding = 'base64';
        $mail->AddAddress('1572990728@qq.com', null);
        $mail->Body = 'Hello Smtp Mail';
        return $mail->Send();
    }
}