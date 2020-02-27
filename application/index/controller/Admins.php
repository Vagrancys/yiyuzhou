<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;
use think\Image;
use think\Validate;
use think\Loader;
class Admins extends Comment
{
    public function admins(){
        $this->header_nav();
        $this->nav_login();
        $dat=Session::get('user');
        $form=Db::table('system_front')->where('cid',0)->select();
        if(!$dat == 1){
            $group=Db::table('front_auth_group_access')->where('uid',$dat)->find();
            $auth=Db::table('front_auth_group')->where('id',$group['group_id'])->find();
            $power=explode(",",$auth['item']);
            for($z=0;$z<count($power);$z++) {
                $power[$z]= Db::table('front_auth_power')->where('id', $power[$z])->find();
            }
            for($i=0;$i<count($form);$i++){
                $form[$i]['data']=Db::table('system_front')->where('cid',$form[$i]['id'])->select();
                if(!count($form[$i]['data'])==0) {
                    for ($j = 0; $j < count($form[$i]['data']); $j++) {
                        for ($x = 0; $x < count($power); $x++) {

                            if (!$form[$i]['data'][$j]['dyid'] == $power[$x]['name']) {
                                $form[$i]['power'][$j] = $form[$i]['data'][$j];
                            }
                        }
                    }
                    if(!count($form[$i]['power'])==0){
                        $admin[$i]=$form[$i];
                    }
                }
            }
        }else{
            for($i=0;$i<count($form);$i++) {
                $form[$i]['power'] = Db::table('system_front')->where('cid', $form[$i]['id'])->select();
            }
            $admin=$form;
        }
        $this->assign('dat',$dat);
        $this->assign('form',$admin);
        $admins['name']=Session::get('admins_name');
        if($admins['name']==''){
            $admins['static']=0;
        }else{
            $admins['static']=1;
        }
        $this->assign('admins',$admins);
        return $this->fetch();
    }

    public function welcomes(){
        $user=Session::has('admins');
        if(!$user){
            $this->error('您还没有登陆!','front_login');
        }
        $array1=array(
            'zi',
            'video',
            'member',
            'news_comment',
            'admin_static',
            'admin_user'
        );

        $report=Session::get('admins_page');
        for($i=0;$i<count($array1);$i++){
            if($array1[$i]=="zi"){
                $video[0][$i]['number']="总数";
                $video[1][$i]['number']="待审核";
            }elseif($array1[$i]=="video"){
                $video[0][$i]['number']=count(Db::table('video')->select());
                $video[1][$i]['number']=count(Db::table('video')->alias('v')->join('video_collect c','c.Uid=v.id')->where('v.page',$report)->where('c.Static',0)->select());
            }else{
                $video[0][$i]['number']=count(Db::table($array1[$i])->select());
                $video[1][$i]['number']=0;
            }
        }
        $this->assign("video",$video);
        return $this->fetch();
    }

    public function front_login(){
        $this->nav_login();
        $this->header_nav();
        return $this->fetch();
    }

    public function front_do_login()
    {
        $data= input('post.');
        if(!empty($data)){
            if (filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
                $da = array(
                    'email' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('email')->check($da)){
                    return $this->fetch('front_login');
                }
            } elseif ($this->isMobile($data['username'])){
                $da = array(
                    'phone' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('phone')->check($da)){
                    return $this->fetch('front_login');
                }
            } else{
                $da = array(
                    'name' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('name')->check($da)){
                    return $this->fetch('front_login');
                }
            }
        }else{
            return $this->fetch('front_login');
        }
        $da['password'] = md5($da['password']);
        $form = Db::table('member')->where($da)->find();
        if ($form) {
            $admin=Db::table('admin_front')->where('admin_id',$form['id'])->find();
            if($admin){
                Session::set('admins', $form['id']);
                Session::set('admins_page', $admin['admin_page']);
                Session::set('admins_name',$admin['admin_name']);
                return $this->fetch('welcomes');
            }else{
                return $this->fetch('front_login');
            }
        } else {
            return $this->fetch('front_login');
        }
    }

    public function front_login_del(){
        if(Session::get('admins')){
            Session::delete('admins');
            Session::delete('admins_name');
            Session::delete('admins_page');
            return $this->fetch('front_login');
        }
    }

    public function admins_video(){
        $user=Session::has('admins');
        if(!$user){
            $this->error('您还没有登陆!','front_login');
        }
        $data=Db::table('video')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function admins_video_collects(){
        $user=Session::has('admins');
        if(!$user){
            $this->error('您还没有登陆!','front_login');
        }
        $data=input('get.');
        $data=Db::table('video_collect')->alias('c')->join('video v','v.id=c.Uid')->where('c.Uid',$data['id'])->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function admins_video_static(){
        $user=Session::has('admins');
        if(!$user){
            $this->error('您还没有登陆!','front_login');
        }
        $report=Db::table('web_report')->where('report_static',1)->paginate(10);
        $page=$report->render();
        $this->assign('page',$page);
        $this->assign('data',$report);
        $number=Db::table('web_report')->count();
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function admins_video_comment(){
        $user=Session::has('admins');
        if(!$user){
            $this->error('您还没有登陆!','front_login');
        }
        $report=Db::table('web_report')->where('report_status',2)->paginate(10);
        $page=$report->render();
        $this->assign('page',$page);
        $this->assign('data',$report);
        $number=Db::table('web_report')->count();
        $this->assign('number',$number);
        return $this->fetch();
    }
}