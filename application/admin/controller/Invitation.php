<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/6/20
 * Time: 17:11
 */

namespace app\admin\controller;
use app\common\model\Invitationcode as InvitationcodeModel;
use think\Db;
use think\Session;
use think\Request;


class Invitation extends Base
{
    public function index(){
        if ($this->noadmin()){
            return $this->Tips('无权访问此页面!');
        }
        $invitationcode = InvitationcodeModel::all();
        $this->assign(
            [
                'title' => '邀请码中心',
                'vcode' => $invitationcode
            ]
        );
        return view();
    }

    public function autocode(){
        if ($this->noadmin()){
            return $this->Tips('无权访问此页面!');
        }
        $data = input('post.');
        $value = [
            'group' => $data['group'],
            'number' => $data['number'],
            'create_time' => time(),
            'prefix' => $data['prefix']
        ];
        if ($value['number'] > 1000)
            return $this->Tips('/admin/invitation','生成邀请码失败!');
        for ($i=1;$i<=$value['number'];$i++){
            $result = Db::name('invitationcode')
                ->insert([
                    'code' => $value['prefix'].md5(rand(1000,9999)),
                    'user_group' => $value['group'],
                    'create_time' => $value['create_time']
                ]);
        }
        if ($result){
            return $this->SuccessNow('/admin/invitation','生成邀请码成功!');
        }else{
            return $this->Tips('/admin/invitation','生成邀请码失败!');
        }
    }

    public function del($id){
        if ($this->noadmin()){
            return $this->Tips('无权访问此页面!');
        }
        $result = Db::name('invitationcode')
            ->where('id',$id)
            ->delete();
        if($result){
            return $this->SuccessNow('/admin/invitation','邀请码删除成功!');
        }
        else{
            return $this->Tips('邀请码删除失败!');
        }
    }
}