<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Level extends Comment{
    //处理1级 修仙等级排行处理
    public function level(){
        $this->nav_login();
        $this->header_nav();
        $deng=input('level');
        if(empty($deng)){
            $deng=1;
        }else{
            if(is_numeric($deng)){
                if($deng>10){
                    $deng=1;
                }
            }else{
                $deng=1;
            }
        }
        $level=Db::table('mange_theme')->alias('a')
            ->join('level_theme_date l','l.level_date_mange=a.mange_theme_id')
            ->where('a.mange_theme_static',1)
            ->where('l.level_date_level',$deng)
            ->paginate(40,false,[
                'query'=>$deng
            ]);
        $page=$level->render();
        $pai=Db::table('level_class')->order('level_class_id desc')->select();
        $paicolor=Db::table('level_class')->where('level_class_id',$deng)->find();
        $this->assign('paico',$paicolor);
        $this->assign('pai',$pai);
        $this->assign('level',$level);
        $this->assign('page',$page);
        return $this->fetch();
    }

    //处理1级 修仙等级详细说明
    public function grow(){
        $this->header_nav();
        $this->nav_login();
        $grow=input('grow');
        $video=Db::table('mange_theme')->alias('a')
            ->join('member m','m.id=a.mange_theme_user')->where('a.mange_theme_id',$grow)
            ->where('a.mange_theme_static',1)
            ->find();
        if(!$video){
            abort(404);
        }
        if(!$video['nickname']==''){
            $video['name']=$video['nickname'];
        }
        $user=Session::get('users');
        if($user==$video['mange_theme_user']){
            $video['member_static']=1;
        }else{
            $video['member_static']=0;
        }
        $this->assign('vi',$video);
        $open=Db::table('level_theme_date')->where('level_date_mange',$grow)->find();
        $level_text="";
        for($i=0;$i<=$open['level_date_level'];$i++){
            if($i==0){
                $level_text .="<p class='level-p-radius level-click'>底层</p>";
            }else{
                if($i==$open['level_date_level']){
                    $name=Db::table('level_class')->where('level_class_id',$open['level_date_level'])->find();
                    $level_text .="<p class='level-click level-p-color'>".$name['level_class_name']."级</p>";
                }else{
                    $name=Db::table('level_class')->where('level_class_id',$i)->find();
                    $level_text .="<p class='level-click'>".$name['level_class_name']."级</p>";
                }
            }

        }
        $taonumber=Db::table('level_number')->where('level_number_manga',$grow)->find();
        if($taonumber){
            Db::table('level_number')->where('level_number_manga',$grow)->setInc('level_number_level',1);
            $number['number']=$taonumber['level_number_level'];
        }else{
            Db::table('level_number')->insert(array('level_number_manga'=>$grow));
            $number['number']=1;
        }
        $number['level']=$open['level_date_level'];
        $this->assign('number',$number);
        $this->assign('l_text',$level_text);
        if($open){
            $level_text="";
            $this->assign('level_text',$level_text);
            $this->level_comment($grow);
            if($open['level_date_level']>=1&&$open['level_date_level']<10)
            {
                return $this->fetch('grow1');
            }elseif($open['level_date_level']==10){
                return $this->fetch('grow10');
            }
        }else{
            if(!$open['level_date_dao']==0){
                $name=Db::table('member')->where('id',$open['level_date_dao'])->find();
                if(!$name['nickname']==''){
                   $name['name']=$name['nickname'];
                }
                $dao['member_name']=$name['name'];
                $dao['member_id']=$name['id'];
                if($user==$video['user_id']){
                    $dao['member_static']=1;
                }else{
                    $dao['member_static']=0;
                }
                $dao['level_static']=1;
            }else{
                $dao['level_static']=0;
            }
            $this->assign('dao',$dao);
            return $this->fetch();
        }
    }

    //处理1级 修仙导师入住给予帮助
    public function level_form(){
        $user=Session::get('users');
        $member=Db::table('member')->where('id',$user)->where('status',1)->find();
        $data=input('post.');
        $video=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->find();
        Db::table('level_theme_date')->where('level_date_mange',$data['id'])->setField('level_date_dao',$user);
        $tutors=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',$video['level_date_level'])->find();
        if($member['level']>=$video['level_date_level']){
            if(!$tutors){
                $tutor=array(
                    'level_tutor_manga'=>$video['level_date_mange'],
                    'level_tutor_level'=>$video['level_date_level'],
                    'level_tutor_name'=>$member['name'],
                    'level_tutor_user'=>$member['id'],
                    'level_tutor_time'=>time(),
                    'level_tutor_text'=>$data['text']
                );
                $form=Db::table('level_tutor')->insert($tutor);
            }else{
                $tutor=array(
                    'level_tutor_name'=>$member['name'],
                    'level_tutor_user'=>$member['id'],
                    'level_tutor_time'=>time(),
                    'level_tutor_text'=>$data['text']
                );
                $form=Db::table('level_tutor')
                    ->where('level_tutor_manga',$video['level_date_mange'])
                    ->where('level_tutor_level',$video['level_date_level'])
                    ->update($tutor);
            }
            $mange=Db::table('mange_theme')->where('mange_theme_id',$data['id'])->find();
            $where=array(
                'user_new_system_name'=>$mange['mange_theme_user'],
                'user_new_system_vip'=>10,
                'user_new_system_text'=>"你的投稿".$mange['mange_theme_title']."拥有了导师".$member['name'].",可以前去查看<a href='/grow/".$mange['mange_theme_id']."'>前往</a>",
                'user_new_system_time'=>time(),
                'user_new_system_static'=>1
            );
            Db::table('user_new_system')->insert($where);
            if(Db::table('user_new_system_static')->where('user_new_system_name',$mange['mange_theme_id'])->find()){
                Db::table('user_new_system_static')->where('user_new_system_name',$mange['mange_theme_id'])->setField('user_new_system_static',1);
            }else{
                Db::table('user_new_system_static')->insert(array('user_new_system_name'=>$mange['mange_theme_id'],'user_new_system_static'=>1));
            }
            if(Db::table('user_new_static')->where('user_new_static_name',$mange['mange_theme_id'])->find()){
                Db::table('user_new_static')->where('user_new_static_name',$mange['mange_theme_id'])->setField('user_new_static_number',1);
            }else{
                Db::table('user_new_static')->insert(array('user_new_static_name'=>$mange['mange_theme_id'],'user_new_static_number'=>1));
            }
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }else{
            return json(0);
        }
    }

    //处理1级 修仙导师违规给予弹赦
    public function level_dan(){
        $data=input('post.');
        $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->find();
        Db::table('level_theme_date')->where('level_date_mange',$data['id'])->setField('level_date_dao',0);
        if($level['level_date_level']>=$data['level']){
            $tutor=Db::table('level_tutor')
                ->where('level_tutor_manga',$data['id'])
                ->where('level_tutor_level',$data['level'])
                ->where('level_tutor_name',$data['member'])
                ->find();
            if($tutor){
                $tutor['level_tutor_tet']=$data['text'];
                $tutor['level_tutor_time']=time();
                $form=Db::table('level_tutor_del')->insert($tutor);
                Db::table('level_tutor')
                   ->where('level_tutor_manga',$data['id'])
                   ->where('level_tutor_level',$data['level'])
                    ->where('level_tutor_name',$data['member'])
                    ->delete();
                if($form){
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

    //处理1级 权威导师名称ajax显示
    public function level_zhi(){
        $data=input('post.');
        $form=Db::table('member')->where('id',$data['text'])->find();
        if($form){
            $data['msg']=1;
            $data['text']=$form['name'];
            return json($data);
        }else{
            $data['msg']=0;
            return json($data);
        }
    }

    //处理1级 权威导师指定入住
    public function level_zhiding(){
        $data=input('post.');
        $tao=array(
            'level_tutor_manga'=>$data['id'],
            'level_tutor_level'=>$data['level'],
            'level_tutor_yu'=>$data['text']
        );
        $text="有一个作品等待您成为导师，<a href='/tao/".$data['id']."'>查看</a>";
        $new=array(
            'uid'=>0,
            'new_classify'=>5,
            'user_id'=>$data['id'],
            'new_text'=>$text,
            'time'=>time()
        );
        Db::table('news_new')->insert($new);
        $pan=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',$data['level'])->find();
        if(!$pan){
            if(!empty($data['text'])){
                $form=Db::table('level_tutor')->insert($tao);
                if($form){
                    return json(1);
                }else{

                    return json(0);
                }
            }else{
                return json(0);
            }
        }else{
            if(!empty($data['text'])){
                $form=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->update($tao);
                if($form){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
    }

    //处理1级 修仙升级处理和分配
    public function level_upd(){
        $data=input('post.');
        $reuqire=Db::table('level_require')->select();
        $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->find();
        if(empty($data)){
           return json(0);
        }
        if($level['level_date_level']==1){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',1)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']){
                    $up=array(
                        'level_date_click_up'=>$reuqire[2]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[2]['level_require_shou'],
                        'level_date_level'=>2,
                        'level_date_dao'=>0

                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>1,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',1)->update($up);
                    if($level){
                        return json(1);
                    }else{
                        return json(0);
                    }
                }
            }else{
                return json(0);
            }
        }
        elseif($level['level_date_level']==2){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',2)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']){
                    $up=array(
                        'level_date_click_up'=>$reuqire[3]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[3]['level_require_shou'],
                        'level_date_assess_up'=>$reuqire[3]['level_require_assess'],
                        'level_date_level'=>3,
                        'level_date_dao'=>0
                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>2,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',2)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==3){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',3)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']){
                    $up=array(
                        'level_date_click_up'=>$reuqire[4]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[4]['level_require_shou'],
                        'level_date_assess_up'=>$reuqire[4]['level_require_assess'],
                        'level_date_comment_up'=>$reuqire[4]['level_require_comment'],
                        'level_date_level'=>4,
                        'level_date_dao'=>0
                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>3,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',3)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==4){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',4)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']){
                        $up=array(
                            'level_date_click_up'=>$reuqire[5]['level_require_click'],
                            'level_date_shou_up'=>$reuqire[5]['level_require_shou'],
                            'level_date_assess_up'=>$reuqire[5]['level_require_assess'],
                            'level_date_comment_up'=>$reuqire[5]['level_require_comment'],
                            'level_date_support_up'=>$reuqire[5]['level_require_support'],
                            'level_date_level'=>5,
                            'level_date_dao'=>0
                        );
                        $levels=array(
                            'level_manga'=>$data['id'],
                            'level_level'=>4,
                            'level_time'=>time()
                        );
                        Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                        $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',4)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==5){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',5)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']){
                        $up=array(
                            'level_date_click_up'=>$reuqire[6]['level_require_click'],
                            'level_date_shou_up'=>$reuqire[6]['level_require_shou'],
                            'level_date_assess_up'=>$reuqire[6]['level_require_assess'],
                            'level_date_comment_up'=>$reuqire[6]['level_require_comment'],
                            'level_date_support_up'=>$reuqire[6]['level_require_support'],
                            'level_date_just_up' => $reuqire[6]['level_require_just'],
                            'level_date_level'=>6,
                            'level_date_dao'=>0
                        );
                        $levels=array(
                            'level_manga'=>$data['id'],
                            'level_level'=>5,
                            'level_time'=>time()
                        );
                        Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                        $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',5)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==6){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',5)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']) {
                    $up = array(
                        'level_date_click_up' =>$reuqire[7]['level_require_click'],
                        'level_date_shou_up' => $reuqire[7]['level_require_shou'],
                        'level_date_assess_up' => $reuqire[7]['level_require_assess'],
                        'level_date_comment_up' => $reuqire[7]['level_require_comment'],
                        'level_date_support_up' => $reuqire[7]['level_require_support'],
                        'level_date_just_up' => $reuqire[7]['level_require_just'],
                        'level_date_honor_up'=>$reuqire[7]['level_require_honor'],
                        'level_date_level' => 7,
                        'level_date_dao'=>0
                    );
                    $levels = array(
                        'level_manga' => $data['id'],
                        'level_level' => 6,
                        'level_time' => time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level = Db::table('level_theme_date')->where('level_date_mange', $data['id'])->where('level_date_level', 6)->update($up);
                    if ($level) {
                        return json(1);
                    } else {
                        return json(0);
                    }
                }else{
                   return json(0);
                }
            }else{
                return json(0);
            }
        }
        elseif($level['level_date_level']==7){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',5)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']){
                    $up=array(
                        'level_date_click_up'=>$reuqire[8]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[8]['level_require_shou'],
                        'level_date_assess_up'=>$reuqire[8]['level_require_assess'],
                        'level_date_comment_up'=>$reuqire[8]['level_require_comment'],
                        'level_date_support_up'=>$reuqire[8]['level_require_support'],
                        'level_date_just_up'=>$reuqire[8]['level_require_just'],
                        'level_date_honor_up'=>$reuqire[8]['level_require_honor'],
                        'level_date_offer_up'=>$reuqire[8]['level_require_offer'],
                        'level_date_level'=>8,
                        'level_date_dao'=>0
                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>7,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',7)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==8){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',5)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']
                    &&$level['level_date_offer']>=$level['level_date_offer_up']){
                    $up=array(
                        'level_date_click_up'=>$reuqire[9]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[9]['level_require_shou'],
                        'level_date_assess_up'=>$reuqire[9]['level_require_assess'],
                        'level_date_comment_up'=>$reuqire[9]['level_require_comment'],
                        'level_date_support_up'=>$reuqire[9]['level_require_support'],
                        'level_date_just_up'=>$reuqire[9]['level_require_just'],
                        'level_date_honor_up'=>$reuqire[9]['level_require_honor'],
                        'level_date_offer_up'=>$reuqire[9]['level_require_offer'],
                        'level_date_backer_up'=>$reuqire[9]['level_require_backer'],
                        'level_date_level'=>9,
                        'level_date_dao'=>0
                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>8,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',8)->update($up);
                    if($level){
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
        elseif($level['level_date_level']==9){
            $tutor=Db::table('level_tutor')->where('level_tutor_manga',$data['id'])->where('level_tutor_level',5)->find();
            if(!$tutor['level_tutor_user']==null){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']
                    &&$level['level_date_offer']>=$level['level_date_offer_up']
                    &&$level['level_date_backer']>=$level['level_date_backer_up']){
                    $up=array(
                         'level_date_click_up'=>$reuqire[10]['level_require_click'],
                        'level_date_shou_up'=>$reuqire[10]['level_require_shou'],
                        'level_date_assess_up'=>$reuqire[10]['level_require_assess'],
                        'level_date_comment_up'=>$reuqire[10]['level_require_comment'],
                        'level_date_support_up'=>$reuqire[10]['level_require_support'],
                        'level_date_just_up'=>$reuqire[10]['level_require_just'],
                        'level_date_honor_up'=>$reuqire[10]['level_require_honor'],
                        'level_date_offer_up'=>$reuqire[10]['level_require_offer'],
                        'level_date_backer_up'=>$reuqire[10]['level_require_backer'],
                        'level_date_selfless_up'=>$reuqire[10]['level_require_selflesss'],
                        'level_date_level'=>10,
                        'level_date_dao'=>0
                    );
                    $levels=array(
                        'level_manga'=>$data['id'],
                        'level_level'=>9,
                        'level_time'=>time()
                    );
                    Db::table('level')->insert($levels);
                    $this->level_allot($data['id'],$tutor['level_tutor_user']);
                    $level=Db::table('level_theme_date')->where('level_date_mange',$data['id'])->where('level_date_level',9)->update($up);
                    if($level){
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
        else{
            return json(0);
        }
    }
}