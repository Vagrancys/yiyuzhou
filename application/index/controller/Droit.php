<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Droit extends Comment{
    public function Droit(){
        $name=Session::get("users");
        $this->nav_login();
        $this->header_nav();
        if(!$name){
            return $this->fetch("login/login");
        }else{
            $form=Db::table('member')->where('id',$name)->find();
        }
        $droit=Db::table('droit_list')->where('droit_list_name',$name)->find();
        if(!$droit){
            Db::table('droit_list')->insert(array('droit_list_name'=>$name));
            $droit['droit_list_sign']=1;
            $droit['droit_list_sign_prompt']=1;
        }
        $this->assign('form',$form);
        $this->assign('droit',$droit);
        return $this->fetch();
    }

    public function droit_static(){
        $data=input('post.');
        if($data['text']=="all"){
            $where=array(
                'droit_list_sign'=>1
            );
        }elseif($data['text']=="follow"){
            $where=array(
                'droit_list_sign'=>0
            );
        }elseif($data['text']=="al"){
            $where=array(
                'droit_list_sign_prompt'=>1
            );
        }elseif($data['text']=="no"){
            $where=array(
                'droit_list_sign_prompt'=>0
            );
        }
        $user=Session::get('users');
        $form=Db::table('droit_list')->where('droit_list_name',$user)->update($where);
        if($form){
            $da['msg']=1;
            $da['text']="修改成功！";
            return json($da);
        }else{
            $da['msg']=0;
            $da['text']="修改失败！";
            return json($da);
        }
    }
}