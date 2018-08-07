<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/3
 * Time: 15:31
 */

namespace app\admin\controller;
use think\Controller;
use think\Session;
use think\Db;
use app\common\model\Member as MemberModel;
use app\common\model\Invitationcode as InvitationModel;
class Login extends Controller
{

    public function _initialize()
    {
        parent::_initialize();
        define('VIEW_PATH',APP_PATH.'admin/view/');
    }

    public function Index(){
        //if login in return login
        if (!empty(Session::get('member.uid'))){
            $this->redirect('/admin');
            return false;
        }
        //obtain form data
        $data = input('post.');
        if (!empty($data)){
            $password = $data['password'];
            $validate = $data['username'];
            $user = Db::name('member')->where('username',$validate)->whereOr('email',$validate)->find();
            if (!empty($user)){
                if ($user['username'] === $validate || $user['email'] === $validate ){
                    if ($user['password'] == md5(sha1($password))){
                        Session::set('member.uid',$user['id']);
                        Session::set('member.username',$user['username']);
                        Session::set('member.group',$user['group']);
                        $ip = ip2long($this->getIp());
                        $os = $_SERVER['HTTP_USER_AGENT'];
                        $time = time();
                        $result = Db::name('loginlog')
                            ->insert(['ip' => $ip , 'create_time' => $time , 'os' => $os , 'login_name' => $validate , 'user_id' => $user['id']]);
                        if ($result){
                            return $this->SuccessNow('/admin','登录成功！',1);
                        }
                        else{
                            return $this->Tips('无法获取客户端信息！',2);
                        }
                        //return true;
                    }
                    else{
                        return $this->Tips('用户名或密码错误！',2);
                    }
                }
            }
            else{
                return $this->Tips('/admin/login','用户名或邮箱不存在！',2);
            }
        }
        $this->assign(
            [
                'title' => '开始登陆'
            ]
        );
        return view();
    }

    public function register(){
        $code = input('get.');
        $invcode = @$code['code'];
        $data = input('post.');
        if (!empty($data)){
            $username = $data['email'];
            $password = md5(sha1($data['password']));
            $repassword = md5(sha1($data['repassword']));
            if ($username == ''){
                return $this->Tips('邮箱不能为空!');
            }
            else if ($password != $repassword){
                return $this->Tips('两次输入密码不一致');
            }
            else{
                $code = $data['code'];
                if ($code == ''){
                    return $this->Tips('/admin/login/register','请填写邀请码',2);
                }
                else
                $haveCode = InvitationModel::get(
                    ['code' => $code]
                );
                if ($haveCode){
                    $codeId = $haveCode->id;
                    if (!empty($haveCode->user)){
                        return $this->Tips('邀请码已经被使用',3);
                    }
                    else{
                        $haveEmail = MemberModel::get(
                            ['email' => $username]
                        );
                        if (!empty($haveEmail)){
                            return $this->Tips('邮箱地址已经存在',3);
                        }
                        else{
                            $create_time = time();
                            $userInfo = [
                                'email' => $username,
                                'password' => $password,
                                'group' => 'user',
                                'create_time' => $create_time,
                                'update_time' => $create_time

                            ];
                            $result = MemberModel::create($userInfo);
                            $registerUser = [
                                'id' => $codeId,
                                'user' => $username,
                                'use_time' => time()
                            ];
                            $invitationResult = InvitationModel::update($registerUser);
                            if ($result && $invitationResult){
                                $userId = Db::name('member')
                                    ->where('email',$data['email'])
                                    ->value('id');
                                Db::name('category')
                                    ->insert(
                                        [
                                            'name' => '默认分类',
                                            'slug' => 'default',
                                            'user_id' => $userId
                                        ]
                                    );
                                return $this->SuccessNow('/admin/login','注册成功，返回登录页面',2);
                            }
                        }
                    }
                }else{
                    return $this->Tips('邀请码不存在');
                }
            }

        }

        $this->assign(
            [
                'title' => '注册账户',
                'invcode' => $invcode
            ]
        );
        return view();
    }

    public function forGetPassword(){
        $data = input('post.');
        $email = @$data['email'];
        if (!empty($email)){
            $code = md5(sha1(time().'nobita'));
            $setCode = Db::name('member')
                ->where('email',$email)
                ->update(
                    [
                        'safe_code' => $code
                    ]
                );
            if ($setCode){
                $content = '<meta http-equiv="Content-Type" content="text/html; charset=utf-8"><meta name="viewport" content="width=device-width,initial-scale=1"><meta http-equiv="X-UA-Compatible" content="IE=edge"><style type="text/css">body,table,td,a{-webkit-text-size-adjust:100%;-ms-text-size-adjust:100%}table,td{mso-table-lspace:0;mso-table-rspace:0}img{-ms-interpolation-mode:bicubic}img{border:0;height:auto;line-height:100%;outline:0;text-decoration:none}table{border-collapse:collapse!important}body{height:100%!important;margin:0!important;padding:0!important;width:100%!important}a[x-apple-data-detectors]{color:inherit!important;text-decoration:none!important;font-size:inherit!important;font-family:inherit!important;font-weight:inherit!important;line-height:inherit!important}@media screen and (max-width:600px){h1{font-size:32px!important;line-height:32px!important}}div[style*="margin: 16px 0;"]{margin:0!important}</style><body style="background-color:#f3f5f7;margin:0!important;padding:0!important"><div style="display:none;font-size:1px;color:#fefefe;line-height:1px;font-family:&apos;Lato&apos;,Helvetica,Arial,sans-serif;max-height:0;max-width:0;opacity:0;overflow:hidden">此邮件为系统邮件，如无请求，请忽略。</div><table border="0" cellpadding="0" cellspacing="0" width="100%"><tr><td bgcolor="#33cabb" align="center"><br><br><br><br></td></tr><tr><td bgcolor="#33cabb" align="center" style="padding:0 10px 0 10px"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
            <tr>
            <td align="center" valign="top" width="600"><![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px"><tr><td bgcolor="#ffffff" align="center" valign="top" style="padding:40px 20px 20px 20px;border-radius:4px 4px 0 0;color:#111;font-family:&apos;Lato&apos;,Helvetica,Arial,sans-serif;font-size:48px;font-weight:400;letter-spacing:4px;line-height:48px"><h1 style="font-size:42px;font-weight:400;margin:0">忘记了登录密码?</h1></td></tr></table><!--[if (gte mso 9)|(IE)]></td>
            </tr>
            </table><![endif]--></td></tr><tr><td bgcolor="#f4f4f4" align="center" style="padding:0 10px 0 10px"><!--[if (gte mso 9)|(IE)]><table align="center" border="0" cellspacing="0" cellpadding="0" width="600">
            <tr>
            <td align="center" valign="top" width="600"><![endif]--><table border="0" cellpadding="0" cellspacing="0" width="100%" style="max-width:600px"><tr><td bgcolor="#ffffff" align="left" style="padding:20px 30px 40px 30px;color:#666;font-family:&apos;Lato&apos;,Helvetica,Arial,sans-serif;font-size:16px;font-weight:400;line-height:25px"><p style="margin:0">你有一个更改密码的请求。重置密码很容易。只需按下下面的按钮，并按照指示。我们马上就让你跑起来。</p></td></tr><tr><td bgcolor="#ffffff" align="left"><table width="100%" border="0" cellspacing="0" cellpadding="0"><tr><td bgcolor="#ffffff" align="center" style="padding:20px 30px 30px 30px"><table border="0" cellspacing="0" cellpadding="0"><tr><td align="center" style="border-radius:3px" bgcolor="#33cabb"><a href="http://note.199508.com/admin/login/setpassword?safe_code='.$code.'" target="_blank" style="font-size:18px;font-family:Helvetica,Arial,sans-serif;color:#fff;text-decoration:none;color:#fff;text-decoration:none;padding:12px 50px;border-radius:2px;border:1px solid #33cabb;display:inline-block">重置账户密码</a></td></tr></table></td></tr></table></td></tr><tr><td bgcolor="#ffffff" align="left" style="padding:0 30px 20px 30px;color:#aaa;font-family:&apos;Lato&apos;,Helvetica,Arial,sans-serif;font-size:13px;font-weight:400;line-height:25px"><p style="margin:0;text-align:center">如果您没有提出这个请求，请忽略这封电子邮件。否则，请点击上面的按钮来更改密码。</p></td></tr><tr><td bgcolor="#ffffff" align="left" style="padding:0 30px 40px 30px;border-radius:0 0 4px 4px;color:#666;font-family:&apos;Lato&apos;,Helvetica,Arial,sans-serif;font-size:14px;font-weight:400;line-height:25px"><p style="margin:0">Nobita,<br>a deveploment enginner</p></td></tr></table><!--[if (gte mso 9)|(IE)]></td>
            </tr>
            </table><![endif]--></td></tr><tr><td bgcolor="#f4f4f4" align="center" style="padding:30px 10px 0 10px"></td></tr><tr><td bgcolor="#f4f4f4" align="center" style="padding:0 10px 0 10px"></td></tr></table></body>';
                $sendMail = new Mailhelper();
                $sendMail->sendMail($email,'找回密码，系统邮件请勿直接回复',$content);
                return $this->SuccessNow('/admin/login','邮件已经发送到邮箱，请注意查收',3);
            }
            else{
                return $this->Tips('邮箱不存在，请重新输入!');
            }
        }
//        else{
//            $email = Db::name('member')
//                ->where('email',$email)
//                ->value('email');
//        }
        $this->assign(
            [

                'title' => '找回密码'
            ]
        );
        return view();
    }

    public function setPassword(){
        $dataGet = input('get.');
        $dataPost = input('post.');
        $safeCode = @$dataGet['safe_code'];
        if (!empty($safeCode)){
            $userInfo = Db::name('member')
                ->where('safe_code',$safeCode)
                ->find();
            $this->assign(
                [
                    'title' => '重置密码',
                    'userInfo' => $userInfo,
                    'safeCode' => $safeCode
                ]
            );
            return view();
        }
        elseif (!empty($dataPost)){
            $result = Db::name('member')
                ->where('safe_code',$dataPost['safe_code'])
                ->update(
                    [
                        'password' => md5(sha1($dataPost['password']))
                    ]
                );
            if ($result){
                return $this->SuccessNow('/admin/login','密码重置成功,请妥善保管!');
            }
            else{
                return $this->Tips('未知原因,密码重置失败!');
            }
        }
        else{
            return $this->Tips('这不是你该来的地方!');
        }
    }

    public function Tips($msg){
        $this->assign('title','提示信息');
        echo $this->fetch('login/header');
        echo <<<jump
            <meta name="viewport" content="width=device-width,user-scalable=0,initial-scale=1,maximum-scale=1,minimum-scale=1">
            
            <title>$msg</title>
<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="login-tips col-md-12" style="margin-top: 30%;">
                <div class="alert alert-success" style="max-width: 350px;margin: auto;font-weight: 500;text-align: center;">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Notice!</strong> $msg <a href="Javascript:window.history.go(-1)" style="color: #33cabb;">点击返回上一页</a>
				</div>
            </div>
        </div>
    </div>
</div>
jump;
        echo $this->fetch('login/footer');
    }

    public function SuccessNow($url,$msg,$time=1){
        $this->assign('title','提示信息');
        echo $this->fetch('login/header');
        echo <<<jump
            <meta name="viewport" content="width=device-width,user-scalable=0,initial-scale=1,maximum-scale=1,minimum-scale=1">
            <meta http-equiv="refresh" content="{$time};$url"><title>$msg</title>
<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="login-tips col-md-12" style="margin-top: 30%;">
                <div class="alert alert-success" style="max-width: 300px;margin: auto;">
										<button type="button" class="close" data-dismiss="alert">×</button>
										<strong>Well done!</strong> $msg $time 秒后跳转 
				</div>
            </div>
        </div>
    </div>
</div>
jump;
        echo $this->fetch('login/footer');
    }

    public function Logout(){
        Session::clear();
        return $this->SuccessNow('/admin','退出成功！',1);
    }

    public function getIp()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"]))
        {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"]))
        {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"]))
        {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else
        {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

    public function get_broswer(){
        $sys = $_SERVER['HTTP_USER_AGENT'];  //获取用户代理字符串
        if (stripos($sys, "Firefox/") > 0) {
            preg_match("/Firefox\/([^;)]+)+/i", $sys, $b);
            $exp[0] = "Firefox";
            $exp[1] = $b[1];  //获取火狐浏览器的版本号
        } elseif (stripos($sys, "Maxthon") > 0) {
            preg_match("/Maxthon\/([\d\.]+)/", $sys, $aoyou);
            $exp[0] = "傲游";
            $exp[1] = $aoyou[1];
        } elseif (stripos($sys, "MSIE") > 0) {
            preg_match("/MSIE\s+([^;)]+)+/i", $sys, $ie);
            $exp[0] = "IE";
            $exp[1] = $ie[1];  //获取IE的版本号
        } elseif (stripos($sys, "OPR") > 0) {
            preg_match("/OPR\/([\d\.]+)/", $sys, $opera);
            $exp[0] = "Opera";
            $exp[1] = $opera[1];
        } elseif(stripos($sys, "Edge") > 0) {
            //win10 Edge浏览器 添加了chrome内核标记 在判断Chrome之前匹配
            preg_match("/Edge\/([\d\.]+)/", $sys, $Edge);
            $exp[0] = "Edge";
            $exp[1] = $Edge[1];
        } elseif (stripos($sys, "Chrome") > 0) {
            preg_match("/Chrome\/([\d\.]+)/", $sys, $google);
            $exp[0] = "Chrome";
            $exp[1] = $google[1];  //获取google chrome的版本号
        } elseif(stripos($sys,'rv:')>0 && stripos($sys,'Gecko')>0){
            preg_match("/rv:([\d\.]+)/", $sys, $IE);
            $exp[0] = "IE";
            $exp[1] = $IE[1];
        }else {
            $exp[0] = "未知浏览器";
            $exp[1] = "";
        }
        return $exp[0].'('.$exp[1].')';
    }

}