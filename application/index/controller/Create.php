<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Create extends Comment{
    public function create(){
        $this->nav_login();
        $this->header_nav();
        $user=Session::get("users");
        $data=input('get.');
        if(!$user){
            return $this->fetch("login/login");
        }else{
            $form=Db::table('member')->where('id',$user)->find();
        }
        $this->assign('form',$form);
        if(empty($data)){
            return $this->fetch();
        }elseif($data['url']=="article"){
            if(array_key_exists('static',$data)){
                if($data['rank']==1){
                    $wh='mange_part';
                    $name=array('mange_part_id'=>$data['video']);
                    $wh1=array('mange_part_user'=>$user);
                    $num=1;
                }
                elseif($data['rank']==2){
                    $wh='mange_theme';
                    $name=array('mange_theme_user'=>$user);
                    $wh1=array('mange_theme_id'=>$data['video']);
                    $num=2;
                }
                elseif($data['rank']==3){
                    $wh='mange_part_copy';
                    $name=array('mange_part_user'=>$user);
                    $wh1=array('mange_part_id'=>$data['video']);
                    $num=1;
                }
                elseif($data['rank']==4){
                    $wh='mange_theme_copy';
                    $name=array('mange_theme_user'=>$user);
                    $wh1=array('mange_theme_id'=>$data['video']);
                    $num=2;
                }
                $number=$data['rank'];
                $this->assign('number',$number);
                if($data['static']=="check"){
                    $video=Db::table($wh)->where($wh1)->where($name)->find();
                    if(!$video){
                        return $this->fetch('create');
                    }
                    if($num==1){
                        $page=Db::table('mange_theme')->where('mange_theme_id',$video['mange_part_level'])->find();
                        $video['page']=$page['title'];
                        $this->assign('video',$video);
                        return $this->fetch('check');
                    }else{
                        $nav=Db::table('video_nav')->where('id',$video['mange_theme_page'])->find();
                        $video['page']=$nav['name'];
                        $this->assign('video',$video);
                        return $this->fetch('check_theme');
                    }
                }
                elseif($data['static']=="revise"){
                    if($name==""){
                        return $this->fetch('login/login');
                    }
                    $video=Db::table($wh)->where($wh1)->where($name)->find();
                    if(!$video){
                        return $this->fetch('create');
                    }
                    if($num==1){
                        $da=Db::table('mange_theme')->where('mange_theme_user',$user)->select();
                        $this->assign('collect',$da);
                        $page=Db::table('mange_theme')->where('mange_theme_id',$video['mange_part_level'])->find();
                        $video['mange_theme_page']=$page['title'];
                        $this->assign('works',$video);
                        return $this->fetch('revise');
                    }else{
                        $data=Db::table('video_nav')->where('page',1)->where('level',2)->select();
                        $this->assign('collect',$data);
                        $nav=Db::table('video_nav')->where('id',$video['mange_theme_page'])->find();
                        $video['id']=$nav['id'];
                        $video['name']=$nav['name'];
                        $this->assign('works',$video);
                        return $this->fetch('revise_theme');
                    }

                }
                elseif($data['static']=="data"){
                    $video=Db::table($wh)->where($wh1)->where($name)->find();
                    if(!$video){
                        return $this->fetch('create');
                    }
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['video'])->find();
                    if(!$level){
                        $level['level_date_click']=0;
                        $level['level_date_comment']=0;
                    }
                    $this->assign("level",$level);
                    $this->assign('video',$video);
                    return $this->fetch('data');
                }
                else{
                    return $this->fetch('create');
                }
            }
            else{
                if(array_key_exists('rank',$data)){
                    if($data['rank']==1){
                        $wh='mange_part';
                        $wh1=array('mange_part_user'=>$user);
                        $order='mange_part_time desc';
                    }elseif($data['rank']==2){
                        $wh='mange_theme';
                        $wh1=array('mange_theme_user'=>$user);
                        $order= 'mange_theme_time desc';
                    }elseif($data['rank']==3){
                        $wh='mange_part_copy';
                        $wh1=array('mange_part_user'=>$user);
                        $order='mange_part_time desc';
                    }elseif($data['rank']==4){
                        $wh='mange_theme_copy';
                        $wh1=array('mange_theme_user'=>$user);
                        $order='mange_theme_time desc';
                    }
                    $rank=$data['rank'];
                    $video=Db::table($wh)->where($wh1)->order($order)->paginate(30,false,[
                        'query'=>array('url'=>'article','rank'=>$rank)
                    ]);
                }
                else{
                    $rank=1;
                    $video=Db::table('mange_part')->where('mange_part_user',$user)->order( 'mange_part_time desc')->paginate(30,false,[
                        'query'=>array('url'=>'article','rank'=>$rank)
                    ]);
                }
                $this->assign('rank',$rank);
                $page=$video->render();
                $this->assign('page',$page);
                $this->assign('video',$video);
                if($rank==1||$rank==3){
                    return $this->fetch("article");
                }else{
                    return $this->fetch('article_theme');
                }
            }
        }else{
            return $this->fetch('create');
        }
    }
}