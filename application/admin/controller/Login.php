<?php
namespace app\admin\controller;
use think\cache\driver\Redis;
use think\Controller;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\validate;
class Login extends Common
{
    public function login(){
        $this->token('cy');
        return $this->fetch();
    }

    public function do_login(){
        $request=Request::instance();
        $data=input('post.');
        if(empty($data)){
            $this->Sensitive();
        }else{
            if(!empty($data['token'])){
                $this->verify_token($data['token'],'login');
            }else{
                return $this->error('您的操作有误！','login');
            }
            $array=array(
                'admin_name','admin_password','code'
            );
            for($i=0;$i<count($array);$i++){
                if($array[$i] == 'code'){
                    $form[$array[$i]]=$this->infusion($array[$i],$data);
                }else{
                    $form[$array[$i]]=$this->infusion($array[$i],$data);
                }
            }
        }
        $validate=Loader::validate('User');
        if(!$validate->check($form)){
            $ti=$validate->getError();
            return $this->error($ti,'login');
        }
        $form['admin_password']=md5($form['admin_password']);
        $where=array(
           'admin_name'=>$form['admin_name'],
            'admin_password'=>$form['admin_password']
        );
        $form=Db::table('admin_user')->where($where)->find();
        if($form){
            Session::set('user',$form['id']);
            Session::set('admin_name',$form['admin_name']);
            $time=Db::table('admin_static')->where('admin_id',$form['id'])->find();
            if($time){
                $user=array(
                    'admin_id'=>$form['id'],
                    'ip'=>$request->ip(),
                    'time'=>time(),
                    'go_time'=>$time['time']
                );
                Db::table('admin_static')->where('admin_id',$form['id'])->update($user);
            }else{
                $user=array(
                    'admin_id'=>$form['id'],
                    'ip'=>$request->ip(),
                    'time'=>time()
                );
                Db::table('admin_static')->insert($user);
            }
            return $this->success('登录成功','admin');
        }else{
            return $this->error('登录失败');
        }
    }

    public function login_del(){
        if(Session::get('admin_name')){
            Session::delete('admin_name');
            Session::delete('user');
            return $this->success('退出成功','login');
        }
    }
}