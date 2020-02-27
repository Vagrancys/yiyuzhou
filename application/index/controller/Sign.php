<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
//签到函数处理封装
class Sign extends Comment
{
    //处理1级 签到排行展示处理
    public function sign(){
        $data=input('get.');
        $this->header_nav();
        $this->nav_login();
        $name['id']=Session::get('users');
        $page_num=10;
        if(array_key_exists('page',$data)){
        if($data['page']=="1"){
            $num=1;
        }else{
            $num=$page_num*($data['page']-1)+1;
        }}else{
            $num=1;
        }
        $this->assign('num',$num);
        if(empty($data)||array_key_exists('rank',$data)||array_key_exists('page',$data)){
             $start_time = strtotime(date('Y-m-d'));
             if(array_key_exists('rank',$data)){
                if($data['rank']==1){
                    $rank=1;
                    $where=array('sign_time' => array('EGT', $start_time));
                    $order='sign_time asc';
                }elseif($data['rank']==2){
                    $rank=2;
                    $where="";
                    $order='sign_number desc';
                }
            }else{
                $rank=1;
                $order='sign_time asc';
                $where=array('sign_time' => array('EGT', $start_time));
            }
            $numbers = Db::table('member_sign')
                ->alias('s')
                ->join('member m','s.sign_uid=m.id')
                ->where($where)
                ->order($order)->paginate($page_num,false,
                    ['query'=>array('rank'=>$rank)]);
            $signs=Db::table('member_sign')->where('sign_uid',$name['id'])->find();
            $this->assign('rank',$rank);
            $names=Db::table('member')->where('id',$name['id'])->find();
            $number=count($numbers);
            $page=$numbers->render();
            $money=Db::table('system_money')->where('money_id',1)->find();
            $this->assign('money',$money);
            $this->assign('page',$page);
            $this->assign('number',$number);
            $this->assign('names',$names);
            $this->assign('numbers',$numbers);
            $this->assign('signs',$signs);
            return $this->fetch();
        }elseif(array_key_exists('static',$data)){
            $capital=Db::table('system_capital')->where('system_capital_static',$data['static'])->paginate(20);
            if($data['static']==1){
                $title="直接注资";
            }elseif($data['static']==2){
                $title="广告费注资";
            }elseif($data['static']==3){
                $title="其他种类收入注资";
            }
            $this->assign('title',$title);
            $page=$capital->render();
            $this->assign('page',$page);
            $this->assign('numbers',$capital);
            return $this->fetch('capital');
        }
    }

    //处理1级 处理签到硬币处理函数
    public function sign_up(){
        $sign=input('post.');
        if(!empty($sign)){
            if(array_key_exists('id',$sign)){
                $text['msg']=$this->sign_in($sign['id']);
                $y = date("Y");
                $m = date("m");
                $d = date("d");
                $todayTime= mktime(0,0,0,$m,$d,$y);
                $uid=Db::table('member_sign')->where('sign_uid',$sign['id'])->find();
                $text['num']=Db::table("member_sign")->where('sign_time','EGT',$todayTime)->count();
                if($uid['sign_con']>=10){
                    $text['money']=10;
                }else{
                    $text['money']=$uid['sign_con'];
                }
                $text['year']=$uid['sign_number'];
                $text['month']=$uid['sign_month'];
                return json($text);
            }else{
                $data=0;
                return json($data);
            }
        }else{
            $data=0;
            return json($data);
        }
    }

}