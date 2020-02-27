<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Special extends Comment{

    //作品展示
    public function mange(){
        $this->header_nav();
        $this->nav_login();
        $data=input('main');
        $user=Session::get('users');
        $member=Db::table('member')->where('id',$user)->find();
        if(!$data==""){
            if(is_numeric($data)){
                $video=Db::table('manga')
                    ->alias('v')
                    ->where(array('v.manga_id'=>$data,'v.manga_static'=>1))
                    ->join('member m','v.user_id=m.id')
                    ->join('video_nav n','n.id=v.page')
                    ->field('v.manga_id,m.name,m.nickname,m.image,n.name page_id,v.region,v.page,v.single,v.manga_time,v.age,v.nature,v.user_id,v.manga_text,v.img,v.title,v.downClass,v.downLine,v.downPass,n.english')->find();
                $money=Db::table('manga_support_number')->where('manga_support_num_manga',$data)->find();
                $this->user_fans($video['user_id']);
                if(!$money){
                    $where=array(
                        'manga_support_num_manga'=>$data,
                        'manga_support_num_time'=>time()
                    );
                    Db::table('manga_support_number')->insert($where);
                    $money['manga_support_num_number']=0;
                }
                if(!$video){
                    abort(404);
                }
                if(!$video['nickname']==''){
                    $video['name']=$video['nickname'];
                }
                $video['support_money']=$money['manga_support_num_number'];
                if($clicks=Db::table('level_date')->where('level_date_manga',$data)->find()){
                    if(!$video['downLine']==""){
                        if($member['money']>0&&!($user==$video['user_id'])){
                            Db::table('level_date')->where('level_date_manga',$video['manga_id'])->setInc('level_date_click',1);
                            Db::table('member')->where('id',$user)->setDec('money',1);
                            $this->user_coins($user,$video['manga_id'],1);
                        }
                        $video['click']=$clicks['level_date_click'];
                        $video['click_static']=1;
                        $video['click']=$this->number_say($video['click']);
                    }
                    $video['clicks']=$clicks['level_date_click'];
                    $video['clicks_static']=1;
                    $video['assess']=$this->number_say($clicks['level_date_assess']);
                    $video['support']=$this->number_say($clicks['level_date_support']);
                    $video['just']=$this->number_say($clicks['level_date_just']);
                }else{
                    $video['assess']=0;
                    $video['support']=0;
                    $video['just']=0;
                    $video['clicks_static']=0;
                }
                if(empty($video['single'])){
                    $video['single']='';
                }
                $array=explode(",",$video['nature']);
                $video['natures']=array();
                for($i=0;$i<count($array);$i++){
                    $video['natures'][$i]=Db::table('video_nav')->where('id',$array[$i])->field('name')->find();
                }
                if(empty($video['region'])){
                    $video['region']='';
                }
                $region=Db::table('video_nav')->where('id',$video['region'])->field('name')->find();
                $video['region']=$region['name'];
                $single=Db::table('video_nav')->where('id',$video['single'])->find();
                $video['single_id']=$single['name'];
                $video['single']=$single['id'];
                if(empty($video['clicks'])){
                    $video['clicks']='1';
                }
                $video['clicks']=$this->number_say($video['clicks']);
                $justs=count(Db::table('manga_just')->where('manga_just_manga',$data)->select());
                $video['just_number']=$justs;
                $assess=Db::table('manga_assess')->where('manga_assess_member',$user)->where('manga_assess_manga_id',$data)->find();
                if($assess){
                    $video['assess_number']=$assess['manga_assess_number'];
                    $video['assess_width']=$assess['manga_assess_number']*10;
                }else{
                    $video['assess_number']=0;
                    $video['assess_width']=0;
                }
                $video['assess_width']="<span class='ystar' id='ystar' style='width:".$video['assess_width']."%'></span>";
                $page=24;
                $part=$this->Mobile();
                if($part){
                    $display=1;
                }else{
                    $display=0;
                }
                $this->assign('display',$display);
                $video['collect']=Db::table('manga_part')
                    ->alias('c')
                    ->where('c.part_static',1)
                    ->where('c.Uid',$data)
                    ->paginate($page);
                $video['collect']->toArray();
                $p=ceil($video['collect']->total()/$page);
                if($p>0){
                    $ul="<ul class='manga-num-ul'>";
                    for($z=0;$z<$p;$z++){
                        if($z==0){
                            $a=1;
                            $s=$page;
                        }elseif($z==$p-1){
                            $a=1+($z)*$page;
                            $s=$video['collect']->total();
                        }else{
                            $a=1+($z)*$page;
                            $s=($z+1)*$page;
                        }
                        if($z==0){
                            $ul .=" <li data-id='1' class='manga-num-li back-active'>".$a."-".$s."</li>";
                        }else{
                            $y=$z+1;
                            $ul .=" <li data-id='".$y."' class='manga-num-li manga-hover'>".$a."-".$s."</li>";
                        }
                    }
                    $ul .="</ul>";
                }else {
                    $ul="";
                }
                $this->assign('ul',$ul);
                if(count($video['collect'])==0){
                    abort(404);
                }
                //相关推荐
                $tui=Db::table('manga')
                    ->alias('v')
                    ->where('v.user_id',$video['user_id'])
                    ->where('v.page',$video['page'])
                    ->where('v.manga_static',1)
                    ->order('v.manga_time desc')
                    ->limit(8)
                    ->select();
                //作品主页评论
                $this->comment($data,0,1);
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
        $this->assign('member',$member);
        $honor_where=array(
            'manga_honor_manga'=>$data,
            'manga_honor_static'=>1
        );
        $honor=Db::table('manga_honor')->where($honor_where)->order('manga_honor_time desc')->limit(5)->select();
        $this->assign('honor',$honor);
        $article_where=array(
            'manga_article_manga'=>$data,
            'manga_article_static'=>1
        );
        $article=Db::table('manga_article')->where($article_where)->order('manga_article_time desc')->limit(5)->select();
        $this->assign('article',$article);
        $video['te']=$this->cut($video['manga_text'],270);
        $video['text']=$video['manga_text'];
        $this->assign('vi',$video);
        $s=count(Db::table('user_fans')->where('user_id',$video['user_id'])->select());
        if($s>10000){
            $s=intval($s/10000)."万";
        }
        $this->assign('s',$s);
        $v=Db::table('manga')
            ->alias('v')
            ->where('user_id',$video['user_id'])
            ->join('level_date d','v.manga_id=d.level_date_manga')
            ->where('v.manga_static',1)
            ->count();
        $this->assign('v',$v);
        $this->assign('tui',$tui);
        return $this->fetch();
    }

    public function assess(){
        $data=input('assess');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('manga')
            ->alias('v')
            ->where('v.manga_id',$data)
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->find();
        if(!$video){
            abort(404);
        }
        $assess=Db::table('manga_assess')
            ->alias('m')->join('member b','b.id=m.manga_assess_member')->where('m.manga_assess_manga_id',$data)->paginate(50);
        $assess->toArray();
        foreach ($assess as $k=>$v){
            $num=$v['manga_assess_number']*10;
            $v['text']="<span class='ystar'  style='width: ".$num."%;'></span>";
            $assess->offsetSet($k,$v);
        }
        $page=$assess->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('manga_assess')->where('manga_assess_manga_id',$data)->select());
        $video['numbers']=1;
        $this->assign('assess',$assess);
        $assess=Db::table('manga_assess')->where('manga_assess_member',$user)->where('manga_assess_manga_id',$data)->find();
        if($assess){
            $video['assess_number']=$assess['manga_assess_number'];
            $video['assess_width']=$assess['manga_assess_number']*10;
        }else{
            $video['assess_number']=0;
            $video['assess_width']=0;
        }
        $video['assess_width']="<span class='ystar' id='ystar' style='width:".$video['assess_width']."%'></span>";
        $this->assign('vi',$video);
        return $this->fetch();
    }

    //作品荣誉展示
    public function honor(){
        $data=input('honor');
        $this->header_nav();
        $this->nav_login();
        $video=Db::table('manga')
            ->alias('v')
            ->where('v.manga_id',$data)
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->find();
        if($video){
        $honor=Db::table('manga_honor')
            ->alias('m')->where('m.manga_honor_manga',$data)->paginate(50);
        $page=$honor->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('manga_honor')->where('manga_honor_manga',$data)->select());
        $video['Vip']=count(Db::table('manga_honor')->where('manga_honor_class',1)->where('manga_honor_manga',$data)->select());
        $video['numbers']=1;
        $this->assign('honor',$honor);
        $this->assign('vi',$video);
            return $this->fetch();
        }else{
            abort(404);
        }
    }

    public function support(){
        $data=input('support');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('manga')
            ->alias('v')
            ->where('v.manga_id',$data)
            ->join('manga_support_number a','v.manga_id=a.manga_support_num_manga')
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->where('v.manga_static',1)
            ->find();
        if(!$video){
            abort(404);
        }
        $assess=Db::table('manga_support')
            ->alias('m')
            ->join('member b','b.id=m.manga_support_member')
            ->where('m.manga_support_manga',$data)->paginate(50);
        $page=$assess->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('manga_support')->where('manga_support_manga',$data)->select());
        $video['numbers']=1;
        $this->assign('assess',$assess);
        $member=Db::table('manga_support')->where('manga_support_member',$user)->where('manga_support_manga',$data)->find();
        if($member){
            $member['money_static']=1;
        }else{
            $member['money_static']=0;
        }
        $this->assign('member',$member);
        $this->assign('vi',$video);
        return $this->fetch();
    }

    //作品粉丝关注
    public function just(){
        $data=input('just');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('manga')
            ->alias('v')
            ->where('v.manga_id',$data)
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->find();
        if(!$video){
            abort(404);
        }
        $just=Db::table('manga_just')
            ->alias('m')
            ->join('member b','b.id=m.manga_just_member')
            ->where('m.manga_just_manga',$data)->paginate(50);
        $page=$just->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('manga_just')->where('manga_just_manga',$data)->select());
        $video['numbers']=1;
        $this->assign('just',$just);
        $member=Db::table('manga_just')->where('manga_just_member',$user)->where('manga_just_manga',$data)->find();
        if($member){
            $member['just_static']=1;
        }else{
            $member['just_static']=0;
        }
        $this->assign('member',$member);
        $this->assign('vi',$video);
        return $this->fetch();
    }

    //主页分页显示ajax
    public function special_num_li(){
        $data=input('post.');
        $page=24;
        $video=Db::table('manga_part')
            ->alias('c')
            ->where('c.part_static',1)
            ->where('c.Uid',$data['aid'])
            ->paginate($page);
        $video->toArray();
        $html['html']="";
        foreach ($video as $k=>$v){
            if($v['manga_part_id']==$data['id']){
                $html['html'] .="<li class='special-collection-li'>
                <a href='/play/".$v['manga_part_id']."' title='".$v['Collect']."' target='_blank' class='special-text'>
                    <div class='special-collection-text'>
                        <p class='special-collection-title active'>".$v['Collect']."</p>
                    </div>
                </a>
            </li>";
            }else{
                $html['html'] .="<li class='special-collection-li'>
                <a href='/play/".$v['manga_part_id']."' title='".$v['Collect']."' target='_blank' class='special-text'>
                    <div class='special-collection-text'>
                        <p class='special-collection-title'>".$v['Collect']."</p>
                    </div>
                </a>
            </li>";
            }
        }
        $html['msg']=1;
        return json($html);
    }
}