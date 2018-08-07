<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/17
 * Time: 11:30
 */

namespace app\admin\controller;
use think\Request;
use think\Db;

class Person extends Base
{
    public function index(){
        $Twitter = Db::name('twitter')
            ->where('user_id', $this->getuid())
            ->order('create_time', 'desc')
            ->limit(20)
            ->select();
        $this->assign(
            [
                'title' => '仪表盘',
                'twitter' => $Twitter
            ]
        );
        return view();
    }

    public function setting(){
        $user = Db::name('member')
        ->where('id',$this->getuid())
        ->find();
        $this->assign(
            [
                'title' => '个人设置',
                'user' => $user
            ]
        );
        return view();
    }

    public function uploadface(Request $request){
        // 获取表单上传文件
        //$file = $request->file('file');
        $file = \think\Image::open(request()->file('file'));
        $dirTime = iconv("UTF-8", "GBK", ROOT_PATH . 'public' . DS . 'upload/face/'.date('Ym',time()));
        if (file_exists($dirTime));
        else
            mkdir ($dirTime,0777,true);
        $fileUrl = $dirTime. DS .md5(time()).'.jpg';
        $info = $file->thumb(200, 200)->save($fileUrl);
        if ($info) {
            $faceurl = strstr($fileUrl,"/upload");
            $result = Db::name('member')
                ->where('id',$this->getuid())
                ->update(
                    [
                        'face_url' => $faceurl
                    ]
                );
            if ($result){
                return $this->SuccessNow('/admin/person/setting','更新成功',1);
            }
        } else {
            // 上传失败获取错误信息
            $this->error($file->getError());
        }
    }

    public function updata(){
        $data = input('post.');
        $update_time = time();
        $data['update_time'] = time();
        if (!empty($data)){
            $userNicknameIn = Db::name('member')
                ->where('nickname',@$data['nickname'])
                ->value('nickname');
            $userNameIn = Db::name('member')
                ->where('username',@$data['username'])
                ->value('username');

//            $userNicknameNow = Db::name('member')
//                ->where('id',$this->getuid())
//                ->value('nickname');
//            $userNameNow = Db::name('member')
//                ->where('id',$this->getuid())
//                ->value('username');

            if ($userNameIn == $data['username']){
                $result = Db::name('member')
                    ->where('id', $this->getuid())
                    ->update(
                        [
                            'nickname' => @htmlspecialchars($data['nickname']),
                            'email' => @$data['email'],
                            'update_time' => $update_time,
                            'description' => @htmlspecialchars($data['description'])
                        ]
                    );
                if ($result){
                    if ($userNameIn == $data['username']){
                        return $this->SuccessNow('/admin/person/setting','用户名已经存在，其他信息更新成功!');
                    }
                    else{
                        return $this->SuccessNow('/admin/person/setting','用户信息更新成功!');
                    }
                }else{
                    return $this->Tips('用户信息更新失败!');
                }
            }
            if ($userNicknameIn == $data['nickname']){
                $result = Db::name('member')
                    ->where('id', $this->getuid())
                    ->update(
                        [
                            'username' => @$data['username'],
                            'email' => @$data['email'],
                            'update_time' => $update_time,
                            'description' => @$data['description']
                        ]
                    );
                if ($result){
                    if ($userNicknameIn == $data['nickname']){
                        return $this->SuccessNow('/admin/person/setting','个性名称已经存在，其他信息更新成功!');
                    }else{
                        return $this->SuccessNow('/admin/person/setting','用户信息更新成功!');
                    }
                }else{
                    return $this->Tips('用户信息更新失败!');
                }
            }
        }

    }

    public function updatePassword(){
        $data = input('post.');
        $password = md5(sha1($data['password']));
        $repassword = md5(sha1($data['repassword']));
        if ($password == $repassword){
            $result = Db::name('member')
                ->where('id',$this->getuid())
                ->update([
                    'password' => $repassword
                ]);
            if ($result){
                $userEmail = Db::name('member')
                    ->where('id',$this->getuid())
                    ->value('email');
                $content = '您于'.date('Y-m-d H:i:s' ,time()).'修改了密码，当前新密码为【'.$data['repassword'].'】请妥善保管，如非本人操作，请从登录页面处根据邮箱找回密码。';
                $sendMail = new Mailhelper();
                $sendMail->sendMail($userEmail,'密码修改成功，系统邮件请勿直接回复',$content);
                return $this->SuccessNow('/admin/person/setting','密码修改成功!新密码已经通过邮件发送到您邮箱，请注意查收',3);
            }else{
                return $this->Tips('与原密码相同!');
            }
        }

    }

}