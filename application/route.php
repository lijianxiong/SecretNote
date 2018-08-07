<?php
// +----------------------------------------------------------------------
// | ThinkPHP [ WE CAN DO IT JUST THINK ]
// +----------------------------------------------------------------------
// | Copyright (c) 2006~2018 http://thinkphp.cn All rights reserved.
// +----------------------------------------------------------------------
// | Licensed ( http://www.apache.org/licenses/LICENSE-2.0 )
// +----------------------------------------------------------------------
// | Author: liu21st <liu21st@gmail.com>
// +----------------------------------------------------------------------

use think\Route;

Route::rule('openlink/[:code]','index/index/openlinks');
Route::rule('opentwitter/[:code]','index/index/opentwitter');
Route::rule('twitterstar/[:id]','index/index/twitterstar');
//Route::rule('admin/article/show/[:id]','admin/article/show');
Route::rule('admin/index/category/[:category]','admin/index/category');
Route::rule('admin/article/edit/[:id]','admin/article/edit');
Route::rule('admin/invitation/del/[:id]','admin/invitation/del');
Route::rule('admin/notice/edit/[:id]','admin/notice/edit');
//Route::rule('admin/article/del/[:id]','admin/article/del');
//Route::rule('admin/article/linkdel/[:id]','admin/article/linkdel');
//Route::rule('admin/category/edit/[:id]','admin/category/edit');
//Route::rule('admin/category/del/[:id]','admin/category/del');

Route::rule('admin/index','admin');
Route::rule('admin/search','admin/index/search');
Route::rule('admin/test','admin/index/test');
Route::rule('admin/del','admin/index/del');
Route::rule('admin/thum','admin/index/thum');
Route::rule('admin/untrash','admin/trash/untrash');