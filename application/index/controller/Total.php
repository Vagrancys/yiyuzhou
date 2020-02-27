<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Total extends Comment
{
    public function total()
    {
        $this->header_nav();
        $this->nav_login();
        $data=input('get.');
        $month=Db::table('total_month')->order('total_month_number desc')->select();
        if(!array_key_exists('month',$data)){
            $data['month']=$month[0]['total_month_id'];
        }
        $this->assign('month',$month);
        $this->assign('month_id',$data['month']);
        $week=Db::table('total_week')->order('total_week_id desc')->select();
        if(!array_key_exists('week',$data)){
            if(date("w")==0){
                $data['week']=7;
            }else{
                $data['week']=date("w");
            }
        }
        $this->assign('week',$week);
        $this->assign('week_id',$data['week']);
        $where=array(
            'total_video_month'=>$data['month'],
            'total_video_week'=>$data['week']
        );
        $video=Db::table('total_video')->where($where)->select();
        $this->assign("ve",$video);
        return $this->fetch();
    }

    public function total_week(){
        $data=input('post.');
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
        $where=array(
            'total_video_month'=>$month['total_month_id'],
            'total_video_week'=>$data['week']
        );
        $video['video']=Db::table('total_video')->where($where)->select();
        if(!count($video['video'])==0){
            for($i=0;$i<count($video['video']); $i++){
                $part=Db::table('manga_part')->where('Uid',$video['video'][$i]['total_video_manga_id'])->order('manga_part_id desc')->select();
                if($video['video'][$i]['total_video_manga_id']==null){
                    $video['video'][$i]['text']="<span class='total-txt'>暂未更新</span>";
                }else{
                    $video['video'][$i]['text']="<span class='total-txt'>更新至:<a href='/play/".$part[0]['manga_part_id']."'>".$part[0]['Collect']."</a></span>";
                }
            }
        }
        $video['num']=count($video['video']);
        if($data){
            return json($video);
        }else{
            return json($video);
        }
    }
}