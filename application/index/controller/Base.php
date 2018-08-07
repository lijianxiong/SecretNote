<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/9
 * Time: 20:55
 */

namespace app\index\controller;
use think\Controller;

class Base extends Controller
{
    public function _initialize()
    {
        parent::_initialize();
        define('VIEW_PATH', APP_PATH . 'index/view/');
    }
}