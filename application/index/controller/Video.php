<?php
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Video extends Comment{

    public function play(){
        $this->header_nav();
        $this->nav_login();
        $id['av']=input('play');
        $users=Session::get("users");
        $member=Db::table('member')->where('id',$users)->find();
        if(array_key_exists('av',$id)){
            if(is_numeric($id['av'])){
                $video=Db::table('manga')->alias('v')
                    ->join('manga_part c','c.Uid=v.manga_id')
                    ->join('member m','v.user_id=m.id')
                    ->where(array('c.manga_part_id'=>$id['av'],'c.part_static'=>1))
                    ->find();
                if(!$video){
                    abort(404);
                }else{
                    $clicks=Db::table('level_date')->where('level_date_manga',$video['manga_id'])->find();
                    if($clicks&&!$users==""){
                        if($member['money']>0&&!($users==$video['user_id'])){
                            Db::table('level_date')->where('level_date_manga',$video['manga_id'])->setInc('level_date_click',1);
                            Db::table('member')->where('id',$users)->setDec('money',1);
                            $this->user_coins($users,$video['manga_id'],1);
                        }
                        $video['click']=$clicks['level_date_click'];
                        $video['click_static']=1;
                        $video['click']=$this->number_say($video['click']);
                    }else{
                        $video['click_static']=0;
                    }
                    if(!$users==''){
                        if(!Db::table('user_browse')->where(array('user_id'=>$users,'data_id'=>$video['manga_part_id']))->find()){
                            $where=array(
                                'user_id'=>$users,
                                'data_id'=>$video['manga_part_id']
                            );
                            $where['user_Time']=time();
                            Db::table('user_browse')->insert($where);
                        }else{
                            $where=array(
                                'user_id'=>$users,
                                'data_id'=>$video['manga_part_id']
                            );
                            $data['user_Time']=time();
                            Db::table('user_browse')->where($where)->update($data);
                        }
                    }
                    $video['Line']=htmlspecialchars_decode($video['Line']);
                    if(empty($video['Line'])){
                        abort(404);
                    }
                    if (!preg_match("/url=/i", $video['Line'], $matches)&&!preg_match("/<iframe/i", $video['Line'], $matches))
                    {
                        if(preg_match("/bilibili/i", $video['Line'], $matches)){
                            $video['play_line']=1;
                        }else{
                            $video['play_line']=0;
                        }
                        $video['play_static']=1;
                    }else{
                        $video['play_static']=0;
                        $video['play_line']=0;
                    }
                }
                if($users==$video['user_id']){
                    $video['user']=1;
                    $video['coin_coin']=1;
                }else{
                    $video['video_coin']=1;
                    $video['user']=1;
                    $video['coin_coin']=0;
                }
                $support_money=Db::table('manga_support')->where('manga_support_manga',$video['manga_id'])->where('manga_support_member',$users)->find();
                if($support_money){
                    $video['money_static']=1;
                }else{
                    $video['money_static']=0;
                }
                $justs=count(Db::table('manga_just')->where('manga_just_manga',$video['manga_id'])->select());
                $video['just_number']=$justs;
                $money=Db::table('manga_support_number')->where('manga_support_num_manga',$video['manga_id'])->find();
                $video['support_money']=$money['manga_support_num_number'];
                $video['collect_number']=$clicks['level_date_shou'];
                $video['collection']=Db::table('manga_collect')->where('manga_collect_manga',$video['manga_id'])->count();
                $nav=Db::table('video_nav')->where('id',$video['page'])->find();
                $video['video_page']=$nav['name'];
                $video['english']=$nav['english'];
                $this->user_fans($video['user_id']);
            }else{
                return $this->fetch('login/login');
            }
        }else{
            return $this->fetch('login/login');
        }
        $this->comment(0,$id['av'],2);
        $this->assign('vi',$video);
        $s=count(Db::table('user_fans')->where('user_id',$video['user_id'])->select());
        if($s>10000){
            $s=intval($s/10000)."万";
        }
        $this->assign('s',$s);
        $v=Db::table('manga')
            ->where('user_id',$video['user_id'])
            ->count();
        $form=Db::table('manga_collect')->where(array('manga_collect_member'=>$users,'manga_collect_manga'=>$video['Uid']))->find();
        if($form){
            $coll=1;
        }else{
            $coll=0;
        }
        $page=24;
        $w=Db::table('manga_part')
            ->alias('c')
            ->where('c.part_static',1)
            ->where('c.Uid',$video['manga_id'])
            ->select();
        foreach($w as $ks=>$vs){
            if($vs['Collect']==$video['Collect']){
                $collect=$ks+1;
            }
        }
        $pages=ceil($collect/$page);
        $play=Db::table('manga_part')
            ->alias('c')
            ->where('c.part_static',1)
            ->where('c.Uid',$video['manga_id'])
            ->paginate($page,false,['page'=>$pages]);
        $play->toArray();
        $p=ceil($play->total()/$page);
        if($p>0){
            $ul="<ul class='manga-num-ul'>";
            for($z=0;$z<$p;$z++){
                if($z==0){
                    $a=1;
                    $s=$page;
                }elseif($z==$p-1){
                    $a=1+($z)*$page;
                    $s=$play->total();
                }else{
                    $a=1+($z)*$page;
                    $s=($z+1)*$page;
                }
                if($z==$pages-1){
                    $y=$pages;
                    $ul .=" <li data-id='".$y."' class='manga-num-li back-active'>".$a."-".$s."</li>";
                }else{
                    $d=$z+1;
                    $ul .=" <li data-id='".$d."' class='manga-num-li manga-hover'>".$a."-".$s."</li>";
                }
            }
            $ul .="</ul>";
        }else {
            $ul="";
        }
        $this->assign('ul',$ul);
        if(count($play)==0){
            abort(404);
        }
        $just=Db::table('manga_just')->where('manga_just_manga',$video['manga_id'])->where('manga_just_member',$users)->find();
        if($just){
            $just['just_static']=1;
        }else{
            $just['just_static']=0;
        }

        $this->assign('just',$just);
        $this->assign('member',$member);
        $this->assign("play",$play);
        $this->assign("coll",$coll);
        $this->assign("v",$v);
        return $this->fetch('play');
    }

    public function lists(){
        $path=input();
        $data=explode('-',$path[0]);
        if(empty($data[4])){
            $data[4]="";
        }
        if(empty($data[5])){
            $data[5]="";
        }
        if(empty($data[6])){
            $data[6]="";
        }
        if(empty($data[7])){
            $data[7]="";
        }
        $date=array();
        for($i=0;$i<8;$i++){
            $date[$i]=$data[$i];
        }
        $url=implode('-',$date);
        //0=>page,1=>id,2=>level,3=>single,4=>area,5=>time,6=>nature,7=>age
        $this->videos_nav($data[0],$data[1],$data[2],$data[3],$data[4],$data[5],$data[6],$data[7]);
        $this->video_screen($data,$url);
        $this->nav_login();
        $this->header_nav();
        if($data[1]==6){
            return $this->fetch('list');
        }else{
            return $this->fetch();
        }

    }

}