<?php
/**
 * Created by PhpStorm.
 * User: lx
 * Date: 2017/7/31
 * Time: 16:20
 */
namespace app\extend\wemail;
use PHPMailer;
require 'class.phpmailer.php';
require "mailConfig.php";
date_default_timezone_set("PRC");
class EmailSender{
    protected $code;
    protected $email;


    public function __construct($email)
    {
        $this->email = $email;
        $this->code = rand(100000,999999);
    }
    private function send_em(){
        $mail=new PHPMailer();

        $mailConfig=include ("mailConfig.php");

        // 设置PHPMailer使用SMTP服务器发送Email
        $mail->IsSMTP();
        // 设置邮件的字符编码，若不指定，则为'UTF-8'
        $mail->CharSet='UTF-8';
        // 添加收件人地址，可以多次使用来添加多个收件人
        $mail->AddAddress($this->email);
        // 设置邮件正文
        $mail->Body="验证码：".$this->code;
        // 设置邮件头的From字段。
        $mail->From=$mailConfig["MAIL_ADDRESS"];
        // 设置发件人名字
        $mail->FromName=$mailConfig["MAIL_SENDNAME"];
        // 设置邮件标题
        $mail->Subject="验证码";


        // 设置SMTP服务器。
        $mail->Host=$mailConfig["MAIL_SMTP"];
        // 设置为"需要验证"
        $mail->SMTPAuth=true;
        // 设置用户名和密码。
        $mail->Username=$mailConfig["MAIL_LOGINNAME"];
        $mail->Password=$mailConfig["MAIL_PASSWORD"];
        // 发送邮件。
        if(!$mail->Send()){
            return false;
        }else{
            return true;
        }
    }

    public function send(){
        if($this->send_em()){
            return ['verify_code'=>$this->code,'verify_time'=>time()+60*15,'email'=>$this->email];
        }else{
            return null;
        }
    }
}
?>



