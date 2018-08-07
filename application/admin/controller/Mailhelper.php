<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/7/7
 * Time: 15:04
 */

namespace app\admin\controller;
use Nette\Mail\Message;
use Nette\Mail\SmtpMailer;
use think\Controller;
use think\Db;

class Mailhelper extends Controller
{
    function sendMail($to=[],$subject='',$content=''){
        $mail = new Message();
        //read email setting
        $emailSetting = Db::name('setting')
            ->where('type','email')
            ->value('content');
        $emailInfo = json_decode($emailSetting,true);
        $config = $emailInfo;
        if(empty($config['host']) || empty($config['username']) || empty($config['password'])){
            return false;
        }
        if (is_array($to)){
            foreach ($to as $v) {
                $mail->addTo($v);
            }
        } else {
            $mail->addTo($to);
        }
        $mail->setFrom($config['username'],$config['nickname']);
        $mail->setSubject($subject);
        $mail->setHTMLBody($content);
        $mailer = new SmtpMailer($config);
        return $mailer->send($mail);
    }
}