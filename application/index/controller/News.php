<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Filter;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
class News extends Comment
{
    public function user_new(){
        $this->header_nav();
        $this->nav_login();
        $data=input('get.');
        $id=Session::get('users');
        $this->user_fans($id);
        if($id==""){
            return $this->fetch('login/login');
        }
        if(empty($data)){
            $data['static']="new";
        }
        $num=0;
        $form=Db::table('member')->where('id',$id)->find();
        $this->assign('form',$form);
        $this->assign('nu',$num);
        $this->user_number($id);
        if($data['static']=="new"){
            $news=Db::table('user_new_vip')
                ->alias('n')
                ->join('news_classify c','n.user_new_vip_class=c.id')
                ->where('n.user_new_vip_name',1)
                ->paginate(10);
            $page=$news->render();
            $this->assign('news',$news);
            $this->assign('page',$page);
            return $this->fetch("user_new");
        }elseif($data['static']=="reply"){
            return $this->fetch("user_reply");
        }elseif($data['static']=="i"){
            return $this->fetch("user_i");
        }elseif($data['static']=="praise"){
            return $this->fetch("user_praise");
        }elseif($data['static']=="system"){
            $news=Db::table('user_new_system')
                ->alias('n')
                ->join('member m','m.id=n.user_new_system_name')
                ->join('news_classify c','n.user_new_system_vip=c.id')
                ->where('n.user_new_system_name',$id)
                ->paginate(10,false,[
                    'query'=>array('static'=>'system')
                ]);

            Db::table('user_new_static')->where('user_new_static_name',$id)->setField('user_new_static_number',0);
            Db::table('user_new_system_static')->where('user_new_system_name',$id)->setField('user_new_system_static',0);
            $page=$news->render();
            $this->assign('news',$news);
            $this->assign('page',$page);
            return $this->fetch("user_system");
        }
    }
}