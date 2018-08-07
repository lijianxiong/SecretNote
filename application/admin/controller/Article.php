<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/8
 * Time: 16:18
 */

namespace app\admin\controller;
use app\common\model\Article as ArticleModel;
use think\Db;

class Article extends Base
{
    public function Index(){
    }
    public function write(){
        $categoryAll = $this->userCategory();
        $this->assign(
            [
                'title' => '开始创作',
                'category' => $categoryAll
            ]
        );
        return view();
    }

    public function articlelist(){
        $article = ArticleModel::order('id','desc')
            ->where('user_id', $this->getuid())
            ->paginate(10);
        $GetCategoryAll = $this->userCategory();
        $categoryAll = $this->getCategory();
        $this->assign(
            [
                'title' => '所有创作',
                'list' => $article,
                'category' => $categoryAll,
                'getcategory' => $GetCategoryAll
            ]
        );
        return view();
    }

    public function add(){
        $data  = input('post.');
        $time = strtotime($data['create_time']);
        $insertData = [
            'category_id' => @$data['category_id'],
            'create_time' => $time,
            'title' => htmlspecialchars(@$data['title']),
            'description' => @$data['description'],
            'content' => htmlspecialchars(@$data['content']),
            'update_time' => htmlspecialchars($time),
            'user_id' => $this->getuid()
        ];
        if ($result = ArticleModel::create($insertData)) {
            return $this->redirect('/admin');
        } else {
            return $this->Tips('/admin/article/write','发布失败，请重新编辑',1);
        }
    }

    public function edit($id){
        $result = ArticleModel::get($id);
        $categoryAll = $this->userCategory();
        $this->assign(
            [
                'title' => '编辑创作',
                'article' => $result,
                'category' => $categoryAll
            ]
        );
        return view();
    }

    public function update(){
        $data  = input('post.');
        $id = $data['id'];
        $data = [
            'update_time' => time(),
            'title' => htmlspecialchars($data['title']),
            'description' => @$data['description'],
            'content' => @$data['content']
        ];
        $result = Db::name('article')
            ->where('user_id',$this->getuid())
            ->where('id',$id)
            ->update($data);
        if ($result) {
            return $this->redirect('/admin');
        } else {
            return $this->Tips('/admin/article/write','更新失败，请重新编辑',1);
        }
    }

    public function show($id){
        $data = input('post.');
        $id = $data['id'];
        $result = Db::name('article')
            ->where('id', $id)
            ->where('user_id', $this->getuid())
            ->find();
        $categoryAll = $this->getCategory();
        $item = [];
        $item['article'] = $result;
        $item['category'] = $categoryAll;
        return json_encode($result);
    }

    public function mark(){
        $article = Db::name('article')
        ->order('id','desc')
            ->where('user_id', $this->getuid())
            ->where('star',1)
            ->where('del',0)
            ->select();
        $GetCategoryAll = Db::name('category')
            ->where('user_id', $this->getuid())
            ->select();
        $categoryAll = $this->getCategory();
        $notepadList = Db::name('notepad')
            ->order('id','desc')
            ->where('user_id',$this->getuid())
            ->where('star',1)
            ->select();
        $this->assign(
            [
                'title' => '所有创作',
                'article' => $article,
                'category' => $categoryAll,
                'getcategory' => $GetCategoryAll,
                'notepad_list' => $notepadList,
                'markActive' => 'active'
            ]
        );
        return view();
    }
}