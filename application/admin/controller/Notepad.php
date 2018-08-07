<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/10
 * Time: 17:52
 */

namespace app\admin\controller;
use app\common\model\Notepad as NotepadModel;
use think\Db;

class Notepad extends Base
{
    public function index(){
        $result = Db::name('notepad')
            ->order('id','desc')
            ->where('user_id',$this->getuid())
            ->where('del',0)
            ->paginate(10);
        $nickname = Db::name('member')
            ->where('id',$this->getuid())
            ->value('nickname');
        $this->assign(
            [
                'title' => '便签条',
                'notepad_list' => $result,
                'nickname' => $nickname,
                'noteActive' => 'active'
            ]
        );
        return view();
    }
    public function add(){
        $data = input('post.');
        $ip = $this->getip();
        $data = [
            'user_id' => $this->getuid(),
            'content' => htmlspecialchars($data['content']),
            'ip' => ip2long($ip),
            'create_time' => time()
        ];
        if (NotepadModel::create($data)){
            $this->redirect('/admin/notepad');
        }else{
            return $this->Tips('/admin/notepad' , '未知错误');
        }
    }

    public function update(){
        $data = input('post.');
        $id = $data['id'];
        $content = $data['content'];
        $result = Db::name('notepad')
            ->where('id',$id)
            ->where('user_id',$this->getuid())
            ->update(
                [
                    'content' => $content
                ]
            );
        if ($result){
            return $this->SuccessNow('/admin/notepad','更新成功!');
        }else{
            return $this->Tips('更新失败!');
        }
    }

    public function edit($id){
        $getCategory = Db::name('notepad')
        ->where('user_id',$this->getuid())
        ->where('id',$id)
        ->find();
        return $getCategory;
    }

}