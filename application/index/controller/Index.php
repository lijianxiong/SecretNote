<?php
namespace app\index\controller;
use think\Db;
use app\common\model\Category as CategoryModel;
use app\common\model\Notepad as TwitterModel;
class Index extends Base
{
    public function index()
    {
        return $this->redirect('/admin');
    }

    public function openlinks($code){
        $getArticle = Db::name('article')
            ->where('links', $code)
            ->select();
        if ($getArticle){
            $article = $getArticle['0'];
            $id = $getArticle['0']['id'];
            $status = $getArticle['0']['status'];
            if ($status == 1){
                //$result = ArticleModel::get($id);
                $categoryAll = $this->getCategory();
                $this->assign(
                    [
                        'title' => $article['title'],
                        'article' => $article,
                        'category' => $categoryAll
                    ]
                );
                return view();
            }
        }
        else{
            return $this->Tips('/admin/login','文章分享地址不存在');
        }
    }

    public function opentwitter($code){
        $getArticle = Db::name('twitter')
            ->where('links', $code)
            ->select();
        if ($getArticle){
            $article = $getArticle['0'];
            $status = $getArticle['0']['status'];
            $userId = $getArticle['0']['user_id'];
            $userInfo = Db::name('member')
                ->where('id' , $userId)
                ->select();
            if ($status == 1){
                $this->assign(
                    [
                        'title' => '碎语分享',
                        'article' => $article,
                        'userinfo' => $userInfo['0']
                    ]
                );
                return view();
            }
        }
        else{
            return $this->Tips('/admin/login','文章分享地址不存在');
        }
    }

    public function twitterstar(){
        $data = input('post.');
        $id = $data['id'];
        $count = Db::name('twitter')
            ->where('id', $id)
            ->value('like');
        $num = ++$count;
        $twitter = [
            'id' => $id,
            'like' => $num
        ];
        TwitterModel::update($twitter);
        return $num;
    }

    public function getCategory(){
        $result = CategoryModel::all();
        $category = [];
        foreach($result as $v){
            $category[$v['id']] = $v['name'];
        }
        return $category;
    }

    public function Tips($url,$msg,$time=1){
        $this->assign('title','提示信息');
        echo <<<jump
        <!DOCTYPE html>
<html lang="en">
<head>
    <meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>跳转错误 - 404</title>
    <link type="text/css" href="/theme/admin/bootstrap/css/bootstrap.min.css" rel="stylesheet">
    <link type="text/css" href="/theme/admin/bootstrap/css/bootstrap-responsive.min.css" rel="stylesheet">
    <link type="text/css" href="/theme/admin/css/theme.css" rel="stylesheet">
    <link type="text/css" href="/theme/admin/images/icons/css/font-awesome.css" rel="stylesheet">
    <link type="image/vnd.microsoft.icon" href="/favicon.png" rel="shortcut icon">
</head>
<body>
<style>
    .footer{
        text-align: center;
    }
</style>
            <meta name="viewport" content="width=device-width,user-scalable=0,initial-scale=1,maximum-scale=1,minimum-scale=1">
            <meta http-equiv="refresh" content="{$time};$url"><title>$msg</title>
<div class="wrapper">
    <div class="container">
        <div class="row">
            <div class="login-tips offset4 span4">
                <div class="alert">
                    <button type="button" class="close" data-dismiss="alert">×</button>
                    <strong>Warning!</strong> $msg $time 秒后跳转
                </div>
            </div>
        </div>
    </div>
</div>
<div class="footer">
    <div class="container">
        <b class="copyright">&copy; 2018 Nobita, a deveploment enginner. All Rights Reserved.</b>
    </div>
</div>
</body>
jump;
    }
}
