<?php
/**
 * Created by PhpStorm.
 * User: nobita
 * Date: 2018/5/10
 * Time: 15:48
 */

namespace app\admin\controller;
use think\Db;
use app\common\model\Category as CategoryModel;
use think\Request;

class Category extends Base
{
    public function __construct(Request $request = null)
    {
        parent::__construct($request);
        $this->assign(
            [
                'categoryActive' => 'active'
            ]
        );
    }

    public function index(){
        $CategoryAll = Db::name('category')
            ->where('user_id', $this->getuid())
            ->where('del',0)
            ->order('id','desc')
            ->select();
        $this->assign(
            [
                'title' => '创作分类',
                'category' => $CategoryAll
            ]
        );
        return view();
    }

    public function edit($id){
        $getCategory = CategoryModel::get($id);
        return $getCategory;
    }

    public function add(){
        $data = input('post.');
        $category['name'] = @$data['name'];
        $category['slug'] = @$data['slug'];
        $category['user_id'] = $this->getuid();
        if (!empty($category['name'])){
            if (CategoryModel::create($category)){
                return $this->SuccessNow('/admin/category','分类创建成功');
            }else{
                return $this->Tips('/admin/category','分类创建失败',3);
            }
        }
        else{
            return $this->Tips('/admin/category','分类名为空创建失败',3);
        }
        //return view();
    }

    public function update($id){
        $data = input('post.');
        $name = $data['name'];
        $slug = $data['slug'];
        $Category = CategoryModel::get($id);
        $Category->name = $name;
        $Category->slug = $slug;
        if ($Category->save()){
            return $this->SuccessNow('/admin/category','分类更新成功');
        }else{
            return $this->Tips('/admin/category','分类更新失败');
        }
    }

    public function del($id){
        $data = input('post.');
        $type = $data['type'];
        if ($type == 'softdel'){
            $result = Db::name('category')
                ->where('id',$id)
                ->update([
                    'del' => '1'
                ]);
            return $this->state($result);
        }
        if ($type == 'destroy'){
            $result = Db::name('category')
                ->where('id',$id)
                ->delete();
            return $this->state($result);
        }
    }

}