<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/6/27
 * Time: 17:42
 */

namespace app\admin\controller;
use think\Db;

class Notice extends Base
{
    public function add(){
        $data = input('post.');
        $data['create_time'] = time();
        $data['user_id'] = $this->getuid();
        $result = Db::name('notice')
            ->insert($data);
        if ($result){
            return $this->SuccessNow('/admin/setting/admin','公告已经发布!');
        }
    }
    public function show($id){
        $result = Db::name('notice')
            ->where('id',$id)
            ->find();
        return json_encode($result);
    }

    public function edit($id){
        $result = Db::name('notice')
            ->where('id',$id)
            ->find();
        $result['update_time'] = time();
        $this->assign(
            [
                'title' => $result['title'],
                'noticeitem' => $result
            ]
        );
        return view();
    }
    public function update(){
        $data = input('post.');
        $result = Db::name('notice')
            ->where('id',$data['id'])
            ->update(
                $data
            );
        if ($result){
            return $this->SuccessNow('/admin/notice/edit/'.$data['id'] ,'公告更新成功啦!');
        }else{
            return $this->Tips('公告更新失败,请重试!');
        }
    }
}