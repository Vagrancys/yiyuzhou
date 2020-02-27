<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Session;
use think\Image;
class Index extends Comment
{
    public function index()
    {
        $this->nav_login();
        $this->header_nav();
        $fo=Db::table('manage_push')
            ->alias('m')
            ->join('manga v','m.push_video=v.manga_id')
            ->where(array('m.push_index'=>0,'v.manga_static'=>1))
            ->limit(10)->select();
        if(count($fo)<10){
            $fo=Db::table('manga')->alias('v')
                ->where('v.manga_static',1)
                ->where('page',6)
                ->order('v.manga_time desc')->limit(8)->select();
        }
        $this->assign('fo',$fo);
        $form=Db::table('manage_guide')  ->alias('g')
            ->join('manga v','v.manga_id=g.guide_video')
            ->where(array('g.guide_index'=>0,'v.manga_static'=>1))->limit(10)->select();

        if(count($form)<10){
            $form=Db::table('manga')
                ->where('page',6)
                ->where('manga_static',1)
                ->order('manga_time desc')->limit(10)->select();
        }
        $this->assign('form',$form);
        //修仙
        $level=Db::table('mange_theme')->alias('v')
            ->join('level_theme_date l','v.mange_theme_id=l.level_date_mange')
            ->where('v.mange_theme_static',1)
            ->order('l.level_date_level desc')
            ->limit(12)->select();
        $this->assign('level',$level);
        $levelpai=Db::table('level')->alias('l')
            ->join('mange_theme m','m.mange_theme_id=l.level_manga')
            ->where('l.level_level',1)
            ->order('l.level_time desc')
            ->limit(10)->select();
        for($i=0;$i<count($levelpai);$i++){
            $levelpai[$i]['time']=$this->user_time($levelpai[$i]['level_time']);
        }
        $this->assign("levelpai",$levelpai);
        //动漫
        $mange=Db::table('manga')
            ->where('page',6)
            ->where('manga_static',1)
            ->order('manga_time desc')
            ->limit(12)->select();
        for($j=0;$j<count($mange);$j++){
            $Collect=Db::table('manga_part')->where('Uid',$mange[$j]['manga_id'])->order('manga_part_id desc')->find();
            $mange[$j]['part_id']=$Collect['manga_part_id'];
            $mange[$j]['Collect']=$Collect['Collect'];
        }
        $mangapai=Db::table('manga')
            ->where('page',6)
            ->where('manga_static',1)
            ->order('manga_time desc')
            ->limit(10)->select();
        for($i=0;$i<count($mangapai);$i++){
            $mangapai[$i]['time']=$this->user_time($mangapai[$i]['manga_time']);
        }
        $this->assign("mangapai",$mangapai);
        //杂
        $mang=Db::table('mange_part')
            ->where('mange_part_static',1)
            ->order('mange_part_time desc')
            ->limit(12)->select();
        $this->assign('mang',$mang);
        $mangeking=Db::table('mange_part')
            ->where('mange_part_static',1)
            ->order('mange_part_time desc')
            ->limit(10)->select();
        for($i=0;$i<count($mangeking);$i++){
            $mangeking[$i]['time']=$this->user_time($mangeking[$i]['mange_part_time']);
        }
        $this->assign("mangeking",$mangeking);
        //淘宝
        $w=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_static',1)
            ->join('level_theme_date t','t.level_date_mange=v.mange_theme_id')
            ->where('t.level_date_dao',0)->order('v.mange_theme_time desc')->limit(12)->select();
        $number=count($w);
        $this->assign('w',$w);
        $this->assign('number',$number);
        $this->assign('mange',$mange);
        //新番
        $month=date("m");
        $age=date("Y");
        if($month>=1&&$month<4){
            $month=$age.'01';
        }elseif($month>=4&&$month<7){
            $month=$age.'04';
        }elseif($month>=7&&$month<10){
            $month=$age.'07';
        }elseif($month>=10&&$month<=12){
            $month=$age.'10';
        }
        $month=Db::table('total_month')->where('total_month_number',$month)->find();
        $week=Db::table('total_week')->order('total_week_id desc')->select();
        if(date('w')==0){
            $data['week']=7;
        }else{
            $data['week']=date("w");
        }
        $this->assign('week',$week);
        $this->assign('week_id',$data['week']);
        $where=array(
            'total_video_month'=>$month['total_month_id'],
            'total_video_week'=>$data['week']
        );
        $video=Db::table('total_video')->where($where)->select();
        if($video){
            for($i=0;$i<count($video);$i++){
                if(!$video[$i]['total_video_manga_id']==null){
                    $video[$i]['video_static']=1;
                    $video[$i]['one']=Db::table('manga')->where('manga_id',$video[$i]['total_video_manga_id'])->find();
                    $video[$i]['two']=Db::table('manga_part')->where('Uid',$video[$i]['one']['manga_id'])->order('manga_part_id desc')->find();
                }else{
                    $video[$i]['video_static']=0;
                }
            }
            $static['static']=1;
        }else{
            $static['static']=0;
        }
        $this->assign("static",$static);
        $this->assign("ve",$video);
        //淘宝完排行
        $taopai=Db::table('level_theme_date')->alias('t')
            ->join('mange_theme m','m.mange_theme_id=t.level_date_mange')
            ->where('t.level_date_dao','>',0)
            ->order('t.level_date_level desc')->limit(10)->select();
        for($i=0;$i<count($taopai);$i++){
            $taopai[$i]['time']=$this->user_time($taopai[$i]['mange_theme_time']);
        }
        $this->assign("taopai",$taopai);

        $num=1;
        $this->assign('num',$num);
        $num1=1;
        $this->assign('num1',$num1);
        $num2=1;
        $this->assign('num2',$num2);
        $num3=1;
        $this->assign('num3',$num3);
        return $this->fetch();
    }

    public function home(){
        $this->nav_login();
        $this->header_nav();
        $data['nav']=input('nav');
        if(empty($data)){
            abort(404);
        }else{
            $data=Db::table('video_nav')->where('english',$data['nav'])->find();
            if($data){
                $data['nav']=$data['id'];
            }else{
                abort(404);
            }
        }

        $pan=Db::table('manga')->where('page',$data['nav'])->select();
        if(!$pan){
           abort(404);
        }
        /*轮播系统*/
        $fo=Db::table('manga')
            ->where('manga_static',1)
            ->where('page',$data['nav'])
            ->order('manga_time desc')->limit(8)->select();

        $this->assign('fo',$fo);
        /*推荐系统*/
        $form=Db::table('manga')->alias('v')
            ->where('v.manga_static',1)
            ->where('page',$data['nav'])
            ->order('v.manga_time desc')->limit(10)->select();
        $this->assign('form',$form);
        /*次分类系统*/
        $q=Db::table('video_nav')->where('level',3)->where('single',$data['nav'])->select();
        for($i=0;$i<count($q);$i++){
            $q[$i]['one']=Db::table('manga')
                ->where('page',$q[$i]['single'])
                ->where('manga_static',1)
                ->where('single','like','%'.$q[$i]['id'].'%')
                ->order('manga_time desc')
                ->limit(12)->select();
            $q[$i]['two']=Db::table('manga')
                ->where('page',$q[$i]['single'])
                ->where('manga_static',1)
                ->where('single','like','%'.$q[$i]['id'].'%')
                ->order('manga_time desc')
                ->limit(10)->select();
        }
        for($i=0;$i<count($q);$i++){
            for($s=0;$s<count($q[$i]['two']);$s++){
                $q[$i]['two'][$s]['time']=$this->user_time($q[$i]['two'][$s]['manga_time']);
            }
        }
        for($i=0;$i<count($q);$i++){
            for($j=0;$j<count($q[$i]['one']);$j++){
                $Collect=Db::table('manga_part')->where('Uid',$q[$i]['one'][$j]['manga_id'])->order('manga_part_id desc')->find();
                $q[$i]['one'][$j]['part_id']=$Collect['manga_part_id'];
                $q[$i]['one'][$j]['Collect']=$Collect['Collect'];
            }
            $q[$i]['there']['num']=1;
        }
        /*最新更新*/
        $time=time()-30*24*60*60;
        $w=Db::table('manga')
            ->where('page',$data['nav'])
            ->where('manga_time','>',$time)
            ->where('manga_static',1)
            ->order('manga_time desc')
            ->limit(12)->select();

            for($z=0;$z<count($w);$z++){
                $Collect=Db::table('manga_part')->where('Uid',$w[$z]['manga_id'])->order('manga_part_id desc')->find();
                $w[$z]['part_id']=$Collect['manga_part_id'];
                $w[$z]['Collect']=$Collect['Collect'];
                $w[$z]['time']=$this->user_time($w[$z]['manga_time']);
            }
        $this->assign('title',$data['name']);
        $this->assign('w',$w);
        $this->assign('q',$q);
        $num=1;
        $this->assign('num',$num);
        $num2=1;
        $this->assign('num2',$num2);
        $num3=1;
        $this->assign('num3',$num3);
        return $this->fetch();
    }

    public function search(){
        $this->nav_login();
        $this->header_nav();
        $data=array();
        $data=input('post.');
        $date=input('get.');
        if(empty($data)){
            if(array_key_exists('keyword',$date)){
                $data['keyword']=$date['keyword'];
            }else{
                $data['keyword']='';
            }

        }else{
            if(!array_key_exists('keyword',$data)){
                return $this->fetch('search');
            }
        }
        if($data['keyword'] == ''){
            return $this->fetch('search');
        }else{
            $keyword ='%'.$data['keyword'].'%';
            $form=Db::table('manga')
              ->where('title|manga_text','like',$keyword)->where('manga_static',1)->order('manga_id desc')
                ->paginate(10,false,[
                    'query'=>array('keyword'=>$data['keyword'])
                ]);
            $number=count($form);
            $form->toArray();
            foreach ($form as$k => $v){
                $text=$v['manga_text'];
                $text=$this->cut($text,180);
                $text1="<em class='keyword'>".$data['keyword']."</em>";
                $v['title']=preg_replace("/".$data['keyword']."/",$text1,$v['title']);
                $v['text']=preg_replace("/".$data['keyword']."/",$text1,$text);
                $form->offsetSet($k,$v);
            }
            $page=$form->render();
            $this->assign('page',$page);
            $this->assign('text',$data['keyword']);
            $this->assign('number',$number);
            if($form){
                $da="";
                $this->assign('da',$da);
                $this->assign('form',$form);
                return $this->fetch('searchs');
            }else{
                $da="对不起，没有找到匹配结果。";
                $form='';
                $this->assign('form',$form);
                $this->assign('da',$da);
                return $this->fetch('searchs');
            }
        }
    }
}
