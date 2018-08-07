<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/14
 * Time: 16:05
 */

namespace app\admin\controller;
use app\common\model\Member as MemberModel;
use app\common\model\Invitationcode as InvitationcodeModel;
use think\Request;
use think\Db;
use think\Session;


class Setting extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        if ($this->noadmin()){
            return $this->Tips('无权访问此页面!').exit;
        }
    }

    public function admin(){
        $invitationcode = InvitationcodeModel::all();
        $this->assign(
            [
                'title' => '网站设置',
                'adminActive' => 'active',
                'vcode' => $invitationcode
            ]
        );
        return view();
    }


    public function adminupdate(){
        $data = input('post.');
        $input = [
            'web_name' => $data['web_name'],
            'admin_email' => $data['admin_email'],
            'web_url' => $data['web_url'],
            'description' => $data['description'],
            'icp' => $data['icp']
        ];
        $data = json_encode($input);
        $result = Db::name('setting')
        ->where('type','admin')
        ->update([
            'content' => $data
        ]);
        if ($result){
            return $this->SuccessNow('/admin/setting/admin','更新站点信息成功');
        }
        else{
            return $this->Tips('/admin/setting/admin','更新站点信息失败',3);
        }
    }

    public function setEmail(){
        $data = input('post.');
        $result = Db::name('setting')
            ->where('type','email')
            ->update([
                'content' => json_encode($data)
            ]);
        if ($result){
            return $this->SuccessNow('/admin/setting/admin','邮箱更新成功!');
        }else{
            return $this->Tips('更新失败,请重试!');
        }
    }

    public function user(){
        $userAll = Db::name('member')
            ->paginate(20);
        $this->assign(
            [
                'userList' => $userAll
            ]
        );
        return view();
    }

    public function delUser($id){
        $data = input('post.');
        $type = $data['type'];
        $dbname = $data['dbname'];
        if ($type == 'del'){
            $result = Db::name($dbname)
                ->where('id',$id)
                ->delete();
            return $this->state($result);
        }
        if (!empty($data)){
            return $this->Tips('没有接收到任何的数据!');
        }
    }

}