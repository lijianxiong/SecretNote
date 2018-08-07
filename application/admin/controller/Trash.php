<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/6/12
 * Time: 17:52
 */

namespace app\admin\controller;
use app\common\model\Article as ArticleModel;
use think\Db;


class Trash extends Base
{
    public function index(){
        $article = ArticleModel::order('id','desc')
            ->where('user_id', $this->getuid())
            ->where('del',1)
            ->select();
        $categoryAll = Db::name('category')
            ->order('id','desc')
            ->where('user_id', $this->getuid())
            ->where('del',1)
            ->order('id','desc')
            ->select();
        $notepad = Db::name('notepad')
            ->where('user_id',$this->getuid())
            ->where('del',1)
            ->select();
        $this->assign(
            [
                'title' => '回收站',
                'article' => $article,
                'category' => $categoryAll,
                'notepad_list' => $notepad,
                'trashActive' => 'active'
            ]
        );
        return $this->fetch('trash/index');
    }

}