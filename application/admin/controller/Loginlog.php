<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/10
 * Time: 17:41
 */

namespace app\admin\controller;
use think\Session;
use think\Db;

class Loginlog extends Base
{
    public function index(){
        $loginLog = Db::name('loginlog')
            ->where('user_id', $this->getuid())
            ->order('create_time', 'desc')
            ->limit(100)
            ->select();
//        $num = Db::name('loginlog')
//            ->where('user_id',$this->getuid())
//            ->count();
//        if ($num >= 100 ){
//            Db::name('loginlog')
//                ->where('user_id',$this->getuid())
//                ->delete();
//        }
        $this->assign(
            [
                'title' => '登录日志',
                'loginlog' => $loginLog
            ]
        );
        return view();
    }
}