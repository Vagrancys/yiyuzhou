<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Develop extends Commons{
    public function admin_develop_text(){
        $data=Db::table('develop_text')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }

    public function admin_develop_add(){
        $data=input('get.');
        if($data['static']=='text'){
            return $this->fetch('admin_develop_text_add');
        }elseif($data['static']=='central'){
            return $this->fetch('admin_develop_central_add');
        }elseif($data['static']=='function'){
            return $this->fetch('admin_develop_function_add');
        }elseif($data['static']=='header'){
            return $this->fetch('admin_develop_texts_add');
        }
    }

    public function admin_develop_edit(){
        $data=input('get.');
        if($data['static']=='text'){
            $if=Db::table('develop_text')->where('develop_text_id',$data['id'])->find();
            $this->assign('if',$if);
            return $this->fetch('admin_develop_text_edit');
        }elseif($data['static']=='central'){
            $if=Db::table('develop_data_central')->where('develop_data_central_id',$data['id'])->find();
            $this->assign('if',$if);
            return $this->fetch('admin_develop_central_edit');
        }elseif($data['static']=='function'){
            $if=Db::table('develop_function')->where('develop_function_id',$data['id'])->find();
            $this->assign('if',$if);
            return $this->fetch('admin_develop_function_edit');
        }elseif($data['static']=='header'){
            $if=Db::table('develop_data_header')->where('develop_data_header_id',$data['id'])->find();
            $this->assign('if',$if);
            return $this->fetch('admin_develop_texts_edit');
        }elseif($data['static']=='comment'){
            $if=Db::table('develop_comment')->where('develop_comment_id',$data['id'])->find();
            $this->assign('if',$if);
            return $this->fetch('admin_develop_comment_edit');
        }
    }

    public function admin_develop_form(){
        $data=input('post.');
        if($data['static']=='text'){
            array_splice($data,0,1);
            $data['develop_text_time']=time();
            $if=Db::table('develop_text')->insert($data);
            if($if){
                return $this->success('添加网站版本公告成功！','admin_develop_text');
            }else{
                return $this->error('添加网站版本公告失败！','admin_develop_text');
            }
        }elseif($data['static']=='central'){
            array_splice($data,0,1);
            $data['develop_data_central_time']=time();
            $if=Db::table('develop_data_central')->insert($data);
            if($if){
                return $this->success('添加网站数据成功！','admin_develop_central');
            }else{
                return $this->error('添加网站数据失败！','admin_develop_central');
            }
        }elseif($data['static']=='function'){
            array_splice($data,0,1);
            $data['develop_function_time']=time();
            $if=Db::table('develop_function')->insert($data);
            if($if){
                return $this->success('添加网站功能成功！','admin_develop_function');
            }else{
                return $this->error('添加网站功能失败！','admin_develop_function');
            }
        }elseif($data['static']=='header'){
            array_splice($data,0,1);
            $data['develop_data_header_time']=time();
            $if=Db::table('develop_data_header')->insert($data);
            if($if) {
                return $this->success('添加网站数据形式成功！', 'admin_develop_texts');
            }else{
                return $this->error('添加网站数据形式失败！','admin_develop_texts');
            }
        }
    }

    public function admin_develop_update(){
        $data=input('post.');
        if($data['static']=='text'){
            array_splice($data,0,1);
            $if=Db::table('develop_text')->where('develop_text_id',$data['develop_text_id'])->update($data);
            if($if){
                return $this->success('修改网站版本公告成功！','admin_develop_text');
            }else{
                return $this->error('修改网站版本公告失败！','admin_develop_text');
            }
        }elseif($data['static']=='central'){
            array_splice($data,0,1);
            $if=Db::table('develop_data_central')->where('develop_data_central_id',$data['develop_data_central_id'])->update($data);
            if($if){
                return $this->success('修改网站数据成功！','admin_develop_central');
            }else{
                return $this->error('修改网站数据失败！','admin_develop_central');
            }
        }elseif($data['static']=='function'){
            array_splice($data,0,1);
            $if=Db::table('develop_function')->where('develop_function_id',$data['develop_function_id'])->update($data);
            if($if){
                return $this->success('修改网站功能成功！','admin_develop_function');
            }else{
                return $this->error('修改网站功能失败！','admin_develop_function');
            }
        }elseif($data['static']=='header') {
            array_splice($data, 0, 1);
            $if = Db::table('develop_data_header')->where('develop_data_header_id', $data['develop_data_header_id'])->update($data);
            if ($if) {
                return $this->success('修改网站数据形式成功！', 'admin_develop_texts');
            } else {
                return $this->error('修改网站数据形式失败！', 'admin_develop_texts');
            }
        }elseif($data['static']=='comment'){
            array_splice($data,0,1);
            $if=Db::table('develop_comment')->where('develop_comment_id',$data['develop_comment_id'])->update($data);
            if($if){
                return $this->success('修改网站意见成功！','admin_develop_comment');
            }else{
                return $this->error('修改网站意见失败！','admin_develop_comment');
            }
        }
    }

    public function admin_develop_del(){
        $data=input('get.');
        if($data['static']=='text') {
            $if = Db::table('develop_text')->where('develop_text_id', $data['id'])->delete();
        }elseif($data['static']=='central'){
            $if=Db::table('develop_data_central')->where('develop_data_central_id',$data['id'])->delete();
        }elseif($data['static']=='function'){
            $if=Db::table('develop_function')->where('develop_function_id',$data['id'])->delete();
        }elseif($data['static']=='header') {
            $if = Db::table('develop_data_header')->where('develop_data_header_id', $data['id'])->delete();
        }elseif($data['static']=='support') {
            $if = Db::table('develop_support')->where('develop_support_id', $data['id'])->delete();
        }elseif($data['static']=='comment'){
            $if=Db::table('develop_comment')->where('develop_comment_id',$data['id'])->delete();
        }
        if($if){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function admin_develop_central(){
        $data=Db::table('develop_data_central')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }

    public function admin_develop_function(){
        $data=Db::table('develop_function')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }

    public function admin_develop_support(){
        $data=Db::table('develop_support')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }

    public function admin_develop_texts(){
        $data=Db::table('develop_data_header')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }

    public function admin_develop_comment(){
        $data=Db::table('develop_comment')->select();
        $this->assign("data",$data);
        return $this->fetch();
    }
}