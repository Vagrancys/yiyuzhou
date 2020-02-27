<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Taobao extends Comment{
    public function taobao(){
        $this->nav_login();
        $this->header_nav();
        $video=Db::table('mange_theme')
            ->alias('v')->join('level_theme_date t','t.level_date_mange=v.mange_theme_id')
            ->where('t.level_date_dao',0)
            ->where('v.mange_theme_static',1)
            ->order('v.mange_theme_time desc')
            ->paginate(24);
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('zx',$video);
        return $this->fetch();
    }

    public function tutor(){
        $this->nav_login();
        $this->header_nav();
        $video=Db::table('level_theme_date')
            ->alias('v')->join('mange_theme t','t.mange_theme_id=v.level_date_mange')
            ->where('t.mange_theme_static',1)
            ->where('v.level_date_dao','>',0)
            ->order('t.mange_theme_time desc')
            ->paginate(24);
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('zx',$video);
        return $this->fetch();
    }

    public function tor(){
        $this->nav_login();
        $this->header_nav();
        $tao=input('tor');
        $user=Session::get('users');
        if(!$tao==""){
            if(is_numeric($tao)){
                $video=Db::table('mange_theme')
                    ->alias('v')
                    ->where('v.mange_theme_id',$tao)
                    ->where('v.mange_theme_static',1)
                    ->join('member m','v.mange_theme_user=m.id')
                    ->find();
                if(!$video){
                    abort(404);
                }
                if(!$video['nickname']==''){
                    $video['name']=$video['nickname'];
                }
                $clicks=Db::table('level_theme_date')->where('level_date_mange',$video['mange_theme_id'])->find();
                if($clicks){
                    $number=Db::table('level_number')->where('level_number_manga',$tao)->find();
                    if($number){
                        Db::table('level_number')->where('level_number_manga',$tao)->setInc('level_number_taobao',1);
                        $video['clicks']=$number['level_number_taobao'];
                    }else{
                        Db::table('level_number')->insert(array('level_number_manga'=>$tao));
                        $video['clicks']=1;
                    }

                }
                if($level=Db::table('level_tutor')->where('level_tutor_manga',$tao)->where('level_tutor_level',$clicks['level_date_level'])->find()){
                    $video['member_t']=1;
                    $member=Db::table('member')->where('id',$level['level_tutor_user'])->find();
                    if(!$member['nickname']==''){
                        $member['name']=$member['nickname'];
                    }
                    $this->assign('member',$member);
                    $this->assign('level',$level);
                }else{
                    $video['member_t']=0;
                }
                if($video['clicks']>10000){
                    $video['clicks']=($video['clicks']/10000)."万";
                }
                $li=Db::table('level_tutor')->alias('t')
                    ->join('member m','m.id=t.level_tutor_user')
                    ->join('level_require r','r.level_require_level=t.level_tutor_level')
                    ->where('t.level_tutor_manga',$tao)->limit(10)->select();
                $num=1;
                $this->assign('num',$num);
                $this->assign('li',$li);
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
        $video['manga_text']=$this->cut($video['mange_theme_text'],100);
        $this->assign('vi',$video);
        return $this->fetch();
    }
}
