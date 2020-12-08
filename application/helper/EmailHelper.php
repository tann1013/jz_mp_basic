<?php
/**
 * Created by PhpStorm.
 * User: PVer
 * Date: 2018-12-26
 * Time: 10:34
 */

namespace app\helper;

use Exception;
use PHPMailer\PHPMailer\PHPMailer;
use think\Cache;

class EmailHelper
{
    /**
     * @param $email String 邮箱
     * @param $signName String 签名
     * @param $content String 短信参数对象
     * @return bool 是否发送成功
     */
    public static function sendEmail($email, $signName, $content,$title)
    {
        $mail = new PHPMailer;

        //$mail->SMTPDebug = 3;                               // Enable verbose debug output
        try {

            $mail->isSMTP();                                      // Set mailer to use SMTP
            $mail->Host = 'smtp.exmail.qq.com';
            // Specify main and backup SMTP servers  设置邮箱服务器，根据自己邮箱的类型而不同，比如163邮箱： $mail->Host = "smtp.163.com";
            $mail->SMTPAuth = true;                               // Enable SMTP authentication
            $mail->Username = 'weikang@weikangtouzi.com';                 // SMTP username          你自己的邮箱账号
            $mail->Password = 'Wktz123456';                           // SMTP password　　　　　　你自己的账号密码
            $mail->SMTPSecure = 'ssl';                            // Enable TLS encryption, `ssl` also accepted   邮箱服务器所使用的机密协议  163邮箱可以注释此项
            $mail->Port = 465;                                    // TCP port to connect to　　      163邮箱服务器端口为： 25
            $mail->CharSet = 'UTF-8';
            $mail->setFrom('weikang@weikangtouzi.com', $signName);
            //$mail->addAddress('joe@example.net', 'Joe User');     // Add a recipient    可以设置发给多个用户
            $mail->addAddress($email);               // Name is optional


            //$mail->addAttachment('/var/tmp/file.tar.gz');         // Add attachments    添加附件，没有附件的话，注释即可
            //$mail->addAttachment('/tmp/image.jpg', 'new.jpg');    // Optional name
            $mail->isHTML(true);                                  // Set email format to HTML

            $mail->Subject = $signName . $title;
            //邮件的主题
            $mail->Body = $content;//邮件的内容
            $mail->AltBody = $content;

            if (!$mail->send()) {
                return false;
            } else {
                return true;
            }
        } catch (Exception $e) {
            return false;
        }
    }

    /**
     * @param $type String 短信类型
     * @param $email String 邮箱
     * @param $code String 验证码
     * @param $clear boolean 是否马上清除记录
     * @return mixed
     */
    public static function checkCode($type, $email, $code,$clear)
    {
        $checkResult = Cache::get($code . '_email_' . $type . $email);
        if ($checkResult) {
            if($clear){
                Cache::rm($code . '_email_' . $type . $email);
            }
            return true;
        } else {
            return false;
        }
    }

}