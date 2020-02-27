<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Theme extends Comment
{
    //作品展示
    public function theme(){
        $this->header_nav();
        $this->nav_login();
        $data=input('theme');
        $user=Session::get('users');
        $member=Db::table('member')->where('id',$user)->find();
        if(!$data==""){
            if(is_numeric($data)){
                $video=Db::table('mange_theme')
                    ->alias('v')
                    ->where(array('v.mange_theme_id'=>$data,'v.mange_theme_static'=>1))
                    ->join('member m','v.mange_theme_user=m.id')
                    ->join('video_nav n','n.id=v.mange_theme_page')
                    ->field('v.mange_theme_id,m.name,m.nickname,m.image,n.name page_id,v.mange_theme_page,v.mange_theme_time,v.mange_theme_user,v.mange_theme_text,v.mange_theme_img,v.mange_theme_title,v.mange_theme_class,v.mange_theme_line,v.mange_theme_pass,n.english')->find();

                $money=Db::table('mange_support_number')->where('mange_support_num_mange',$data)->find();
                $this->user_fans($video['mange_theme_user']);
                if(!$video){
                    abort(404);
                }
                if(!$video['nickname']==''){
                    $video['name']=$video['nickname'];
                }
                $video['support_money']=$money['mange_support_num_number'];
                if($clicks=Db::table('level_theme_date')->where('level_date_mange',$data)->find()){
                    if(!$video['mange_theme_line']==""){
                        if($member['money']>0&&!($user==$video['user_id'])){
                            Db::table('level_theme_date')->where('level_date_mange',$video['mange_theme_id'])->setInc('level_date_click',1);
                            Db::table('member')->where('id',$user)->setDec('money',1);
                            $this->user_coins($user,$video['mange_theme_id'], 1);
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
                if($user==$video['mange_theme_user']){
                    $video['coin_coin']=1;
                }else{
                    $video['coin_coin']=0;
                }
                $support_money=Db::table('mange_support')->where('mange_support_mange',$data)->where('mange_support_member',$user)->find();
                if($support_money){
                    $video['money_static']=1;
                }else{
                    $video['money_static']=0;
                }
                if(empty($video['clicks'])){
                    $video['clicks']='1';
                }
                $video['clicks']=$this->number_say($video['clicks']);
                $justs=count(Db::table('mange_just')->where('mange_just_mange',$data)->select());
                $video['just_number']=$justs;
                $assess=Db::table('mange_assess')->where('mange_assess_member',$user)->where('mange_assess_mange_id',$data)->find();
                if($assess){
                    $video['assess_number']=$assess['mange_assess_number'];
                    $video['assess_width']=$assess['mange_assess_number']*10;
                }else{
                    $video['assess_number']=0;
                    $video['assess_width']=0;
                }
                $video['assess_width']="<span class='ystar' id='ystar' style='width:".$video['assess_width']."%'></span>";
                $page=24;
                $video['collect']=Db::table('mange_part')
                    ->where('mange_part_static',1)
                    ->where('mange_part_level',$data)
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

                //相关推荐
                $tui=Db::table('mange_theme')
                    ->where('mange_theme_user',$video['mange_theme_user'])
                    ->where('mange_theme_page',$video['mange_theme_page'])
                    ->where('mange_theme_static',1)
                    ->order('mange_theme_time desc')
                    ->limit(8)
                    ->select();
                //作品主页评论
                $this->comment_theme($data,0,1);
            }else{
                abort(404);
            }
        }else{
            abort(404);
        }
        $form=Db::table('mange_collect')->where(array('mange_collect_member'=>$user,'mange_collect_mange'=>$video['mange_theme_id']))->find();
        if($form){
            $video['coll']=1;
        }else{
            $video['coll']=0;
        }
        $this->assign('member',$member);
        $honor_where=array(
            'mange_honor_mange'=>$data,
            'mange_honor_static'=>1
        );
        $honor=Db::table('mange_honor')->where($honor_where)->order('mange_honor_time desc')->limit(5)->select();
        $this->assign('honor',$honor);
        $article_where=array(
            'mange_article_mange'=>$data,
            'mange_article_static'=>1
        );
        $article=Db::table('mange_article')->where($article_where)->order('mange_article_time desc')->limit(5)->select();
        $this->assign('article',$article);
        $offer=Db::table('mange_offer')->where('mange_offer_mange',$video['mange_theme_id'])->where('mange_offer_member',$user)->find();
        if($offer){
            $offer['offer_static']=1;
        }else{
            $offer['offer_static']=0;
        }
        if($member['level']>=8){
            $offer['offer_level']=1;
        }else{
            $offer['offer_level']=0;
        }
        $offer['ul']=Db::table('mange_offer')->alias('v')
            ->join('member m','m.id=v.mange_offer_member')
            ->join('level_require l','m.level=l.level_require_id')->where('v.mange_offer_mange',$video['mange_theme_id'])
            ->select();
        $this->assign('offer',$offer);
        $video['te']=$this->cut($video['mange_theme_text'],270);
        $video['text']=$video['mange_theme_text'];
        $this->assign('vi',$video);
        $just=Db::table('mange_just')->where('mange_just_mange',$data)->where('mange_just_member',$user)->find();
        if($just){
            $just['just_static']=1;
        }else{
            $just['just_static']=0;
        }
        $s=count(Db::table('user_fans')->where('user_id',$video['mange_theme_user'])->select());
        if($s>10000){
            $s=intval($s/10000)."万";
        }
        $this->assign('s',$s);
        $v=Db::table('mange_theme')
            ->alias('v')
            ->where('mange_theme_user',$video['mange_theme_user'])
            ->join('level_theme_date d','v.mange_theme_id=d.level_date_mange')
            ->where('v.mange_theme_static',1)
            ->count();
        $this->assign('v',$v);
        $this->assign('just',$just);
        $this->assign('tui',$tui);
        return $this->fetch();
    }

    //评价
    public function graded(){
        $data=input('graded');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$data)
            ->join('member m','v.mange_theme_user=m.id')
            ->join('video_nav n','n.id=v.mange_theme_page')
            ->find();
        if(!$video){
            abort(404);
        }
        $assess=Db::table('mange_assess')
            ->alias('m')->join('member b','b.id=m.mange_assess_member')->where('m.mange_assess_mange_id',$data)->paginate(50);
        $assess->toArray();
        foreach ($assess as $k=>$v){
            $num=$v['mange_assess_number']*10;
            $v['text']="<span class='ystar'  style='width: ".$num."%;'></span>";
            $assess->offsetSet($k,$v);
        }
        $page=$assess->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('mange_assess')->where('mange_assess_mange_id',$data)->select());
        $video['numbers']=1;
        $this->assign('assess',$assess);
        $assess=Db::table('mange_assess')->where('mange_assess_member',$user)->where('mange_assess_mange_id',$data)->find();
        if($assess){
            $video['assess_number']=$assess['mange_assess_number'];
            $video['assess_width']=$assess['mange_assess_number']*10;
        }else{
            $video['assess_number']=0;
            $video['assess_width']=0;
        }
        $video['assess_width']="<span class='ystar' id='ystar' style='width:".$video['assess_width']."%'></span>";
        $this->assign('vi',$video);
        return $this->fetch();
    }

    //作品荣誉展示
    public function award(){
        $data=input('award');
        $this->header_nav();
        $this->nav_login();
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$data)
            ->join('member m','v.mange_theme_user=m.id')
            ->join('video_nav n','n.id=v.mange_theme_page')
            ->find();
        if($video){
            $honor=Db::table('mange_honor')
                ->alias('m')->where('m.mange_honor_mange',$data)->paginate(50);
            $page=$honor->render();
            $this->assign('page',$page);
            $video['number']=count(Db::table('mange_honor')->where('mange_honor_mange',$data)->select());
            $video['Vip']=count(Db::table('mange_honor')->where('mange_honor_class',1)->where('mange_honor_mange',$data)->select());
            $video['numbers']=1;
            $this->assign('honor',$honor);
            $this->assign('vi',$video);
            return $this->fetch();
        }else{
            abort(404);
        }
    }

    //支持
    public function sustain(){
        $data=input('sustain');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$data)
            ->join('mange_support_number a','v.mange_theme_id=a.mange_support_num_mange')
            ->join('member m','v.mange_theme_user=m.id')
            ->join('video_nav n','n.id=v.mange_theme_page')
            ->where('v.mange_theme_static',1)
            ->find();
        if(!$video){
            abort(404);
        }
        $assess=Db::table('mange_support')
            ->alias('m')
            ->join('member b','b.id=m.mange_support_member')
            ->where('m.mange_support_mange',$data)->paginate(50);
        $page=$assess->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('mange_support')->where('mange_support_mange',$data)->select());
        $video['numbers']=1;
        $this->assign('assess',$assess);
        $member=Db::table('mange_support')->where('mange_support_member',$user)->where('mange_support_mange',$data)->find();
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
    public function attention(){
        $data=input('attention');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$data)
            ->join('member m','v.mange_theme_user=m.id')
            ->join('video_nav n','n.id=v.mange_theme_page')
            ->find();
        if(!$video){
            abort(404);
        }
        $just=Db::table('mange_just')
            ->alias('m')
            ->join('member b','b.id=m.mange_just_member')
            ->where('m.mange_just_mange',$data)->paginate(50);
        $page=$just->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('mange_just')->where('mange_just_mange',$data)->select());
        $video['numbers']=1;
        $this->assign('just',$just);
        $member=Db::table('mange_just')->where('mange_just_member',$user)->where('mange_just_mange',$data)->find();
        if($member){
            $member['just_static']=1;
        }else{
            $member['just_static']=0;
        }
        $this->assign('member',$member);
        $this->assign('vi',$video);
        return $this->fetch();
    }

    public function special_coin(){
        $user=Session::get('users');
        $data=input('post.');
        if($user==""){
            return $this->fetch('login/login');
        }
        if(empty($data)){
            $text="你的操作有误！";
            $this->assign('text',$text);
            return $this->fetch('comment/404');
        }else{
            if(array_key_exists('id',$data)){
                $video=Db::table('video_collect')
                    ->alias('c')->join('video v','v.id=c.Uid')
                    ->where('collectionId',$data['id'])->find();
                $array=array(
                    'user_id'=>$user,
                    'video_id'=>$video['Uid'],
                    'coin'=>$video['Coin'],
                    'coin_data'=>$video['collectionId'],
                    'coin_time'=>time(),
                    'users_id'=>$video['user_id'],
                    'classify'=>1,
                );
                $Coin=Db::table('member')->where('id',$user)->find();
                if($Coin['money']>=$video['Coin']){
                    $coin=Db::table('user_coin')->insert($array);
                    if($coin){
                        $this->user_coin($user,$video['user_id'],$video['Coin']);
                        return json(1);
                    }else{
                        return json(0);
                    }
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
    }

    //作品收藏
    public function mange_collect(){
        $data=input('post.');
        $id=Session::get('users');
        $form=Db::table('mange_collect')->insert(array('mange_collect_member'=>$id,'mange_collect_mange'=>$data['mange'],'mange_collect_list'=>$data['val'],'mange_collect_time'=>time()));
        if($clicks=Db::table('level_theme_date')->where('level_date_mange',$data['mange'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$data['mange'])->setInc('level_date_shou',1);
        }
        if($form){
            $text['msg']=1;
            $text['id']=$data['mange'];
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    //收藏列表查询
    public function mange_collect_list(){
        $id=Session::get('users');
        $form=Db::table('mange_collect_list')->where('mange_collect_list_uid',$id)->field('mange_collect_list_id,mange_collect_list_name')->select();
        if($form){
            $data['msg']=1;
            $data['form']=$form;
            return json($data);
        }else{
            $where=array(
                'mange_collect_list_uid'=>$id,
                'mange_collect_list_time'=>time()
            );
            Db::table('mange_collect_list')->insert($where);
            $form=Db::table('mange_collect_list')->where('mange_collect_list_uid',$id)->field('mange_collect_list_id,mange_collect_list_name')->select();
            $data['msg']=1;
            $data['form']=$form;
            return json($data);
        }
    }

    //收藏夹创建
    public function mange_collect_new(){
        $data=input('post.');
        $id=Session::get("users");
        $where=array(
            'mange_collect_list_name'=>$data['val'],
            'mange_collect_list_uid'=>$id,
            'mange_collect_list_time'=>time()
        );
        $form=Db::table('mange_collect_list')->insert($where);
        $num=Db::table('mange_collect_list')->getLastInsID();
        if($form){
            $text['msg']=1;
            $text['name']=$data['val'];
            $text['id']=$num;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    //作品取消收藏
    public function mange_collect_del(){
        $data=input('post.');
        if($clicks=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$data['id'])->setDec('level_date_shou',1);
        }
        if(Db::table('mange_collect')->where(array('mange_collect_mange'=>$data['id'],'mange_collect_member'=>Session::get('users')))->find()){
            $form=Db::table('mange_collect')->where(array('mange_collect_mange'=>$data['id'],'mange_collect_member'=>Session::get('users')))->delete();
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }else{
            return json(0);
        }
    }

    public function mange_assess(){
        $data=input('post.');
        $where=array(
            'manga_assess_member'=>Session::get('users'),
            'manga_assess_manga_id'=>$data['id'],
            'manga_assess_time'=>time(),
            'manga_assess_text'=>$data['text'],
            'manga_assess_number'=>$data['num']
        );
        if(Db::table('manga_assess')->where('manga_assess_member',Session::get('users'))->where('manga_assess_manga_id',$data['id'])->find()){
            $form=Db::table('manga_assess') ->where('manga_assess_member',Session::get('users'))->where('manga_assess_manga_id',$data['id'])->update($where);
        }else{
            if(Db::table('level_date')->where('level_date_manga',$data['id'])->find()){
                Db::table('level_date')->where('level_date_manga',$data['id'])->setInc('level_date_assess',1);
            }
            $form=Db::table('manga_assess')->insert($where);
        }
        if($form){
            return json(1);
        }else{
            return json(0);
        }

    }

    //作品评论处理
    public function mange_comment(){
        $data=input('post.');
        if(array_key_exists('user',$data)){
            $num=$data['number'];
        }else{
            if($data['num']==1){
                $num=count(Db::table('mange_comment')->where('mange_comment_class',1)->where('mange_comment_reply',0)->where('mange_comment_mange',$data['id'])->select());
            }else{
                $num=count(Db::table('mange_comment')->where('mange_comment_class',2)->where('mange_comment_reply',0)->where('mange_comment_part',$data['part'])->select());
            }
            $num=$num+1;
            $data['user']=0;
        }
        if(array_key_exists('nums',$data)){
            $nums=$data['nums'];
        }else{
            $nums=0;
        }
        $where=array(
            'mange_comment_class'=>$data['num'],
            'mange_comment_mange'=>$data['id'],
            'mange_comment_part'=>$data['part'],
            'mange_comment_member'=>Session::get('users'),
            'mange_comment_text'=>htmlspecialchars($data['value']),
            'mange_comment_time'=>time(),
            'mange_comment_static'=>1,
            'mange_comment_floor'=>$num,
            'mange_comment_num'=>$nums,
            'mange_comment_reply'=>$data['user']
        );
        if(Db::table('level_theme_date')->where('level_date_mange',$data['id'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$data['id'])->setInc('level_date_comment',1);
        }
        if(Db::table('member_level_date')->where('member_level_member',Session::get('users'))->find()){
            Db::table('member_level_date')->where('member_level_member',Session::get('users'))->setInc('member_level_comment',1);
        }
        $form=Db::table('mange_comment')->insert($where);
        $member=Db::table('member')->where('id',Session::get('users'))->find();
        if($member['image']==''){
            $member['image']="default.jpg";
        }
        $where['images']=$member['image'];
        if(!$member['nickname']==''){
            $member['name']=$member['nickname'];
        }
        $where['name']=$member['name'];
        $where['level']=$member['level'];
        if($form){
            $json['msg']=1;
            $json['text']=$where;
            return json($json);
        }else{
            $json['msg']=0;
            return json($json);
        }
    }

    public function mange_ajax_comment(){
        $data=input('post.');
        $form=$this->comments($data);
        $comment=$form->toArray();
        $page=$form->render();
        $data['data']=$comment['data'];
        $data['text']=$page;
        return json($data);
    }

    //处理1级 用户ajax可用硬币展示
    public function support_ajax(){
        $user=Session::get('users');
        $form=Db::table('member')->where('id',$user)->find();
        if($form){
            $data['num']=$form['money'];
            $data['msg']=1;
            return json($data);
        }else{
            $data['msg']=0;
            return json($data);
        }
    }

    //处理1级 用户作品硬币打赏处理
    public function mange_support(){
        $data=input('post.');
        $use=Session::get('users');
        $user=Db::table('member')->where('id',$use)->find();
        if($user['money']>=$data['money']){
            Db::table('member')->where('id',$use)->setDec('money',$data['money']);
            $where=array(
                'mange_support_mange'=>$data['mange'],
                'mange_support_member'=>$use,
                'mange_support_number'=>$data['money'],
                'mange_support_time'=>time()
            );
            if(Db::table('member_level_date')->where('member_level_member',$use)->find()){
                Db::table('member_level_date')->where('member_level_member',$use)->setInc('member_level_money',$data['money']);
            }
            if(Db::table('mange_support')->where('mange_support_mange',$data['mange'])->where('mange_support_member',$use)->find()){
                $form=Db::table('mange_support')->where('mange_support_mange',$data['mange'])->where('mange_support_member',$use)->setInc('mange_support_number',$data['money']);
            }else{
                if(Db::table('level_theme_date')->where('level_date_mange',$data['mange'])->find()){
                    Db::table('level_theme_date')->where('level_date_mange',$data['mange'])->setInc('level_date_support',1);
                }
                $form=Db::table('mange_support')->insert($where);
            }
            if(Db::table('mange_support_number')->where('mange_support_num_mange',$data['mange'])->find()){
                Db::table('mange_support_number')->where('mange_support_num_mange',$data['mange'])->setInc('mange_support_num_number',$data['money']);
            }else{
                $number=array(
                    'mange_support_num_mange'=>$data['mange'],
                    'mange_support_num_number'=>$data['money'],
                    'mange_support_num_time'=>time()
                );
                Db::table('mange_support_number')->insert($number);
            }
            $manga=Db::table('mange_theme')->where('mange_theme_id',$data['mange'])->find();
            $this->user_income($manga['mange_theme_user'],$use,$data['mange'],1,$data['money']);
            $this->user_coins($use,$data['mange'],$data['money']);
            if($form){
                $data['flag']=1;
                return json($data);
            }else{
                $data['flag']=0;
                $data['msg']="投币失败！";
                return json($data);
            }
        }else{
            $data['flag']=0;
            $data['msg']="超出上限，请重试！";
            return json($data);
        }

    }

    public function just_insert(){
        $id=input('post.');
        $user=Session::get('users');
        if($user==""){
            return json(0);
        }
        $where=array(
            'mange_just_mange'=>$id['id'],
            'mange_just_member'=>$user,
            'mange_just_time'=>time(),
        );
        if(Db::table('level_theme_date')->where('level_date_mange',$id['id'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$id['id'])->setInc('level_date_just',1);
        }
        $fans=Db::table('mange_just')->where('mange_just_member',$user)->where('mange_just_mange',$id['id'])->find();
        if(!$fans){
            $form=Db::table('mange_just')->insert($where);
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }
    }

    public function just_del(){
        $id=input('post.');
        if(Db::table('level_theme_date')->where('level_date_mange',$id['id'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$id['id'])->setDec('level_date_just',1);
        }
        if(Db::table('mange_just')->where(array('mange_just_mange'=>$id['id'],'mange_just_member'=>Session::get('users')))->find()){
            $form=Db::table('mange_just')->where(array('mange_just_mange'=>$id['id'],'mange_just_member'=>Session::get('users')))->delete();
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }else{
            return json(0);
        }
    }

    public function mange_offer(){
        $id=input('post.');
        $user=Session::get('users');
        if($user==""){
            return json(0);
        }
        $where=array(
            'mange_offer_mange'=>$id['id'],
            'mange_offer_member'=>$user,
            'mange_offer_time'=>time(),
        );
        if(Db::table('level_theme_date')->where('level_date_mange',$id['id'])->find()){
            Db::table('level_theme_date')->where('level_date_mange',$id['id'])->setInc('level_date_offer',1);
        }
        $fans=Db::table('mange_offer')->where('mange_offer_member',$user)->where('mange_offer_mange',$id['id'])->find();
        if(!$fans){
            $form=Db::table('mange_offer')->insert($where);
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }else{
            return json(0);
        }
    }

    //主页分页显示ajax
    public function special_num_li(){
        $data=input('post.');
        $page=24;
        $video=Db::table('mange_part')
            ->alias('c')
            ->where('c.mange_part_static',1)
            ->where('c.mange_part_level',$data['aid'])
            ->paginate($page);
        $video->toArray();
        $html['html']="";
        foreach ($video as $k=>$v){
            if($v['mange_part_id']==$data['id']){
                $html['html'] .="<li class='special-collection-li'>
                <a href='/video/".$v['mange_part_id']."' title='".$v['mange_part_name']."' target='_blank' class='special-text'>
                    <div class='special-collection-text'>
                        <img class='special-collection-img' src='".$v['mange_part_img']."'>
                    </div>
                    <p class='special-collection-ti active'>".$v['mange_part_name']."</p>
                </a>
            </li>";
            }else{
                $html['html'] .="<li class='special-collection-li'>
                <a href='/video/".$v['manga_part_id']."' title='".$v['mange_part_name']."' target='_blank' class='special-text'>
                    <div class='special-collection-text'>
                        <img class='special-collection-img' src='".$v['mange_part_img']."'>
                    </div>
                    <p class='special-collection-ti'>".$v['mange_part_name']."</p>
                </a>
            </li>";
            }
        }
        $html['msg']=1;
        return json($html);
    }

    public function video(){
        $this->header_nav();
        $this->nav_login();
        $id['av']=input('video');
        $users=Session::get("users");
        $member=Db::table('member')->where('id',$users)->find();
        if(array_key_exists('av',$id)){
            if(is_numeric($id['av'])){
                $video=Db::table('mange_part')
                    ->alias('p')
                    ->join('mange_theme t','t.mange_theme_id=p.mange_part_level')
                    ->join('member m','p.mange_part_user=m.id')
                    ->where(array('p.mange_part_id'=>$id['av'],'p.mange_part_static'=>1))
                    ->find();
                if(!$video){
                    abort(404);
                }else{
                    $clicks=Db::table('level_theme_date')->where('level_date_mange',$video['mange_part_level'])->find();
                    if($clicks&&!$users==""){
                        if($member['money']>0&&!($users==$video['mange_part_user'])){
                            Db::table('level_theme_date')->where('level_date_mange',$video['mange_part_level'])->setInc('level_date_click',1);
                            Db::table('member')->where('id',$users)->setDec('money',1);
                            $this->user_coins($users,$video['mange_part_level'],1);
                        }
                        $video['click']=$clicks['level_date_click'];
                        $video['click_static']=1;
                        $video['click']=$this->number_say($video['click']);
                    }else{
                        $video['click_static']=0;
                    }
                    if(!$users==''){
                        if(!Db::table('user_browse')->where(array('user_id'=>$users,'data_id'=>$video['mange_part_id']))->find()){
                            $where=array(
                                'user_id'=>$users,
                                'data_id'=>$video['mange_part_id']
                            );
                            $where['user_Time']=time();
                            Db::table('user_browse')->insert($where);
                        }else{
                            $where=array(
                                'user_id'=>$users,
                                'data_id'=>$video['mange_part_id']
                            );
                            $data['user_Time']=time();
                            Db::table('user_browse')->where($where)->update($data);
                        }
                    }
                    $video['mange_part_line']=htmlspecialchars_decode($video['mange_part_line']);
                    if(empty($video['mange_part_line'])){
                        abort(404);
                    }
                    if (!preg_match("/url=/i", $video['mange_part_line'], $matches)&&!preg_match("/<iframe/i", $video['mange_part_line'], $matches))
                    {
                        if(preg_match("/bilibili/i", $video['mange_part_line'], $matches)){
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
                if($users==$video['mange_part_user']){
                    $video['user']=1;
                    $video['coin_coin']=1;
                }else{
                    $video['video_coin']=1;
                    $video['user']=1;
                    $video['coin_coin']=0;
                }
                $support_money=Db::table('mange_support')->where('mange_support_mange',$video['mange_part_level'])->where('mange_support_member',$users)->find();
                if($support_money){
                    $video['money_static']=1;
                }else{
                    $video['money_static']=0;
                }
                $justs=count(Db::table('mange_just')->where('mange_just_mange',$video['mange_part_level'])->select());
                $video['just_number']=$justs;
                $money=Db::table('mange_support_number')->where('mange_support_num_mange',$video['mange_part_level'])->find();
                $video['support_money']=$money['mange_support_num_number'];
                $video['collect_number']=$clicks['level_date_shou'];
                $video['collection']=Db::table('mange_collect')->where('mange_collect_mange',$video['mange_part_level'])->count();
                $nav=Db::table('video_nav')->where('id',$video['mange_theme_page'])->find();
                $video['video_page']=$nav['name'];
                $video['english']=$nav['english'];
                $this->user_fans($video['mange_part_user']);
            }else{
                return $this->fetch('login/login');
            }
        }else{
            return $this->fetch('login/login');
        }
        $this->comment_theme(0,$id['av'],2);
        $this->assign('vi',$video);
        $s=count(Db::table('user_fans')->where('user_id',$video['mange_part_user'])->select());
        if($s>10000){
            $s=intval($s/10000)."万";
        }
        $this->assign('s',$s);
        $v=Db::table('mange_theme')
            ->where('mange_theme_user',$video['mange_theme_user'])
            ->count();
        $form=Db::table('mange_collect')->where(array('mange_collect_member'=>$users,'mange_collect_mange'=>$video['mange_part_level']))->find();
        if($form){
            $coll=1;
        }else{
            $coll=0;
        }
        $page=24;
        $w=Db::table('mange_part')
            ->alias('c')
            ->where('c.mange_part_static',1)
            ->where('c.mange_part_id',$video['mange_part_id'])
            ->select();
        foreach($w as $ks=>$vs){
            if($vs['mange_part_name']==$video['mange_part_name']){
                $collect=$ks+1;
            }
        }
        $pages=ceil($collect/$page);
        $play=Db::table('mange_part')
            ->alias('c')
            ->where('c.mange_part_static',1)
            ->where('c.mange_part_level',$video['mange_part_level'])
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
        $just=Db::table('mange_just')->where('mange_just_mange',$video['mange_part_level'])->where('mange_just_member',$users)->find();
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
        return $this->fetch();
    }
public function theme_num_li(){
        $data=input('post.');
        $page=24;
        $video=Db::table('manga_part')
            ->alias('c')
            ->where('c.part_static',1)
            ->where('c.Uid',$data['id'])
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