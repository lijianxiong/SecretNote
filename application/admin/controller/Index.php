<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/3
 * Time: 15:18
 */

namespace app\admin\controller;
use think\Db;
class Index extends Base
{
    public function Index(){
        $article = $this->userArticle();
        $GetCategoryAll = $this->userCategory();
        $categoryAll = $this->getCategory();
        $page = $article->render();
        $this->assign(
            [
                'title' => '创作列表',
                'article' => $article,
                'category' => $categoryAll,
                'getcategory' => $GetCategoryAll,
                'page' => $page,
                'homeActive' => 'active'
            ]
        );
        return view();
    }

    public function category($category){
        $article = Db::name('article')
        ->where('category_id',$category)
        ->where('user_id',$this->getuid())
        ->select();
        $GetCategoryAll = $this->userCategory();
        $categoryAll = $this->getCategory();
        $this->assign(
            [
                'title' => $categoryAll[$category].'分类列表',
                'article' => $article,
                'category' => $categoryAll,
                'getcategory' => $GetCategoryAll,
                'homeActive' => 'active'
            ]
        );
        return $this->fetch('index/index');
    }

    public function search(){
        $data = input('get.');
        $keyword = $data['keyword'];
        $article = Db::name('article')
            ->where('user_id',$this->getuid())
            ->where('content', 'like','%'.$keyword.'%')
            ->where('title', 'like', '%'.$keyword.'%')
            ->where('del',0)
            ->paginate(20);
        $GetCategoryAll = Db::name('category')
            ->where('user_id', $this->getuid())
            ->select();
        $categoryAll = $this->getCategory();
        $this->assign(
            [
                'title' => $keyword.'的搜索结果',
                'article' => $article,
                'category' => $categoryAll,
                'getcategory' => $GetCategoryAll,
                'homeActive' => 'active'
            ]
        );
        return $this->fetch('index/index');
    }
}