<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/3
 * Time: 15:25
 */

namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use app\common\model\Member as MemberModel;
use app\common\model\Article as ArticleModel;
use app\common\model\Notice as NoticeModel;

class Base extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        define('VIEW_PATH',APP_PATH.'admin/view/');
        $user_id = Session::get('member.uid');
        if (empty($user_id)){
            $this->redirect('/admin/login');
            return false;
        }
        //read userinfo
        $user_result = MemberModel::get(['id' => $user_id]);
        $user_info = [
            'id' => $user_result->id,
            'username' => $user_result->username,
            'email' => $user_result->email,
            'create_time' => $user_result->create_time,
            'update_time' => $user_result->update_time,
            'face_url' => $user_result->face_url,
            'nickname' => $user_result->nickname,
            'description' => $user_result->description,
            'group' => $user_result->group
        ];
        //read setting all
        $setting_data = Db::name('setting')
            ->where('type','admin')
            ->value('content');
        $setting = json_decode($setting_data,true);

        //count trash num
        $trash_article = Db::name('article')
            ->where('del' , 1)
            ->where('user_id',$this->getuid())
            ->count();
        $trash_category = Db::name('category')
            ->where('del',1)
            ->where('user_id',$this->getuid())
            ->count();
        $trash_notepad = Db::name('notepad')
            ->where('del',1)
            ->where('user_id',$this->getuid())
            ->count();
        $trash_num = $trash_article + $trash_category + $trash_notepad;
        $notice = NoticeModel::order('id','desc')
            ->paginate(10);

        //read email setting
        $emailSetting = Db::name('setting')
            ->where('type','email')
            ->value('content');
        $emailSetting = json_decode($emailSetting,true);
        $this->assign(
            [
                'userinfo' => $user_info,
                'setting' => $setting,
                'teashNum' => $trash_num,
                'notice' => $notice,
                'emailSetting' => $emailSetting
            ]
        );
    }

    public function Tips($msg){
        $this->assign('title',$msg);
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
        $this->assign('title',$msg);
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

    public function getuid(){
        $user_id = Session::get('member.uid');
        return $user_id;
    }

    public function getCategory(){
        $result = Db::name('category')
            ->where('user_id',$this->getuid())
            ->where('del' , 0)
            ->select();
        $category = [];
        foreach($result as $v){
            $category[$v['id']] = $v['name'];
            $category[$v['slug']] = $v['name'];
        }
        return $category;
    }

    //获取用户IP地址
    public function getip()
    {
        if(!empty($_SERVER["HTTP_CLIENT_IP"])) {
            $cip = $_SERVER["HTTP_CLIENT_IP"];
        }
        else if(!empty($_SERVER["HTTP_X_FORWARDED_FOR"])) {
            $cip = $_SERVER["HTTP_X_FORWARDED_FOR"];
        }
        else if(!empty($_SERVER["REMOTE_ADDR"])) {
            $cip = $_SERVER["REMOTE_ADDR"];
        }
        else {
            $cip = '';
        }
        preg_match("/[\d\.]{7,15}/", $cip, $cips);
        $cip = isset($cips[0]) ? $cips[0] : 'unknown';
        unset($cips);
        return $cip;
    }

    public function userCategory(){
        $GetCategoryAll = Db::name('category')
            ->where('user_id', $this->getuid())
            ->where('del',0)
            ->select();
        return $GetCategoryAll;
    }

    public function userArticle(){
        $GetArticleAll = ArticleModel::order('id','desc')
            ->where('user_id', $this->getuid())
            ->where('del',0)
            ->paginate(10);
        return $GetArticleAll;
    }

    public function state($result){
        if ($result){
            echo 1;
        }else{
            echo 2;
        }
    }

    public function noadmin(){
        $group = Session::get('member.group');
        if ($group != 'admin'){
            return 1;
        }
    }

    public function action($id){
        $data = input('post.');
        $type = $data['type'];
        $dbname = $data['dbname'];
        if ($type == 'del'){
            $result = Db::name($dbname)
                ->where('id',$id)
                ->where('user_id',$this->getuid())
                ->update([
                    'del' => 1
                ]);
            return $this->state($result);
        }
        if ($type == 'untrash'){
            $result = Db::name($dbname)
                ->where('id',$id)
                ->where('user_id',$this->getuid())
                ->update(
                    [
                        'del' => 0
                    ]
                );
            return $this->state($result);
        }
        if ($type == 'destroy'){
            $result = Db::name($dbname)
                ->where('id',$id)
                ->where('user_id',$this->getuid())
                ->delete();
            return $this->state($result);
        }
        if ($type == 'notice'){
            $result = Db::name($dbname)
                ->where('id',$id)
                ->delete();
            return $this->state($result);
        }
        if ($type == 'star'){
            $article = Db::name($dbname)
            ->where('user_id',$this->getuid())
            ->where('id',$id)
            ->find();
            $star = $article['star'];
            if ($star == 0){
                Db::name($dbname)
                    ->where('user_id',$this->getuid())
                    ->where('id',$id)
                    ->update([
                        'star' => 1
                    ]);
                return 1;
            }else{
                Db::name($dbname)
                    ->where('user_id',$this->getuid())
                    ->where('id',$id)
                    ->update([
                        'star' => 0
                    ]);
                return 2;
            }
        }
        if ($type == 'share'){
            $result = Db::name($dbname)
            ->where('id',$id)
            ->where('user_id',$this->getuid())
            ->find();
            $array = [];
            for ($i = 1; $i <= 2; $i++) {
                $array[$i] = chr(rand(97, 122));
            }
            $random = implode('',$array);
            $links = $random.$id;
            if ($result['links'] == 1){
                $res = Db::name($dbname)
                    ->where('id',$id)
                    ->where('user_id',$this->getuid())
                    ->update([
                        'links' => $links,
                        'status' => 1
                    ]);
                return $this->state($res);
            }else{
                Db::name($dbname)
                    ->where('id',$id)
                    ->where('user_id',$this->getuid())
                    ->update([
                        'links' => 1,
                        'status' => 1
                    ]);
                return 2;
            }
        }
        if ($type == 'link'){
            $article = Db::name($dbname)
            ->where('user_id',$this->getuid())
            ->where('id',$id)
            ->find();
            $link = $article['links'];
            $array = [];
            for ($i = 1; $i <= 2; $i++) {
                $array[$i] = chr(rand(97, 122));
            }
            $random = implode('',$array);
            $links = $random.$id;
            if ($link == 1){
                Db::name($dbname)
                    ->where('user_id',$this->getuid())
                    ->where('id',$id)
                    ->update(
                        [
                            'links' => $links,
                            'status' => 1
                        ]
                    );
                return 1;
            }else{
                return 2;
            }
        }
        if ($type == 'linkdel'){
            $result = Db::name($dbname)
                ->where('user_id',$this->getuid())
                ->where('id',$id)
                ->update(
                    [
                        'links' => 1,
                        'status' => 2
                    ]
                );
            return $this->state($result);
        }
        if ($type == 'star'){
            $result = Db::name($dbname)
                ->where('user_id',$this->getuid())
                ->where('id',$id)
                ->find();
            $star = $result['star'];
            if ($star == 0){
                Db::name($dbname)
                    ->where('user_id',$this->getuid())
                    ->where('id',$id)
                    ->update(
                        [
                            'star' => 1
                        ]
                    );
                return 1;
            }else{
                Db::name($dbname)
                    ->where('user_id',$this->getuid())
                    ->where('id',$id)
                    ->update(
                        [
                            'star' => 0
                        ]
                    );
                return 2;
            }
        }
    }

}