<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Article extends Commons{
    public function article_list(){
        $form=Db::table('article')->alias('a')->join('admin_user u','u.id=a.author')->paginate(10);
        $page=$form->render();
        $this->assign('page',$page);
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function article_add(){
        $form=Db::table('article_column')->select();
        $data=Db::table('article_type')->select();
        $this->assign('data',$data);
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function article_form(){
        $data=input('post.');
        $data['article_time']=time();
        $data['author']=Session::get('user');
        $form =  Db::table('article')->insert($data);
        if($form) {
            return $this->success("添加成功", "article_list");
        } else {
            return $this->success("添加失败", "article_list");
        }
    }

    public function article_updata(Request $request){
        $data=$request->param();
        $fid=Db::table('article_column')->select();
        $lid=Db::table('article_type')->select();
        $this->assign('lid',$lid);
        $this->assign('fid',$fid);
        $form=Db::table('article')->where('article_id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function updata_article(){
        $data=input('post.');
        $data['article_time']=time();
        $form =  Db::table('article')->where('article_id',$data['article_id'])->update($data);
        if($form) {
            return $this->success("修改成功", "article_list");
        } else {
            return $this->success("修改失败", "article_list");
        }
    }

    public function article_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('article')->where('article_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","article_list");
            }else{
                return $this->error("批量删除失败","article_list");
            }
        }
        $form=Db::table('article')->where('article_id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }

    public function article_status(){
        $data=input('post.');
        $form=Db::table('article')->where('article_id',$data['id'])->update(array('status'=>$data['status']));
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function column_list(){
        $data=Db::table('article_column')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function column_add(){
        return $this->fetch();
    }

    public function column_form(){
        $data=input('post.');
        $form=Db::table('article_column')->insert($data);
        if($form){
            return $this->success("添加分类成功","column_list.html",1);
        }else{
            return $this->error("添加分类失败","column_list.html",1);
        }
    }

    public function column_updata(Request $request){
        $data=$request->param();
        $data=Db::table('article_column')->where('id',$data['id'])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function updata_column(){
        $data=input('post.');
        $form=Db::table('article_column')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success("修改类型成功",'column_list.html',1);
        }else{
            return $this->error("修改类型失败",'column_list.html',1);
        }
    }

    public function column_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('article_column')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","column_list");
            }else{
                return $this->error("批量删除失败","column_list");
            }
        }
        $form=Db::table('article_column')->where('id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }

    public function type_list()
    {
        $data=Db::table('article_type')->paginate();
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function type_add(){
        return $this->fetch();
    }

    public function type_form(){
        $data=input('post.');
        $form=Db::table('article_type')->insert($data);
        if($form){
            return $this->success("添加分类成功","type_list.html",1);
        }else{
            return $this->error("添加分类失败","type_list.html",1);
        }
    }

    public function type_updata(Request $request){
        $data=$request->param();
        $data=Db::table('article_type')->where('id',$data['id'])->find();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function updata_type(){
        $data=input('post.');
        $form=Db::table('article_type')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success("修改分类成功",'type_list.html',1);
        }else{
            return $this->error("修改分类失败",'type_list.html',1);
        }
    }

    public function type_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('article_type')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","type_list");
            }else{
                return $this->error("批量删除失败","type_list");
            }
        }
        $form=Db::table('article_type')->where('id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }

}

