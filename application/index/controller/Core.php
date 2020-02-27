<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Core extends Comment{
    public function Core(){
        $data=input('get.');
        $name=Session::get("users");
        $this->nav_login();
        $this->header_nav();
        if(!$name){
            return $this->fetch("login/login");
        }else{
            $form=Db::table('member')->where('id',$name)->find();
        }
        if($form['level']==0){
            $form['level_class_name']="新手";
        }else{
            $level=Db::table('level_class')->where('level_class_id',$form['level'])->find();
            $form['level_class_name']=$level['level_class_name'];
        }
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $this->assign('form',$form);
        if(empty($data)){
            $level_text="";
            for($i=0;$i<=$form['level'];$i++){
                if($i==0){
                    $level_text .="<p class='level-p-radius level-click'>新手级</p>";
                }else{
                    if($i==$form['level']){
                        $name=Db::table('level_class')->where('level_class_id',$form['level'])->find();
                        $level_text .="<p class='level-click level-p-color'>".$name['level_class_name']."级</p>";
                    }else{
                        $name=Db::table('level_class')->where('level_class_id',$i)->find();
                        $level_text .="<p class='level-click'>".$name['level_class_name']."级</p>";
                    }
                }

            }
            $open=Db::table('member_level_date')->where('member_level_member',$form['id'])->find();
            if(!$open){
                if(empty($data)){
                    $data['id']=$name;
                }
                $where=array(
                    'member_level_member'=>$data['id']
                );
                Db::table('member_level_date')->insert($where);
            }
            $this->assign('level_text',$level_text);
            if($form['level']==0){
                $l_l['level']=0;
                $l_l['level_up']=1;
                $this->assign('l_l',$l_l);
            }else{
                $this->member_level_comment($form['id']);
            }
            return $this->fetch();
        }
        elseif($data['url']=="new"){
            return $this->fetch("new");
        }
        elseif($data['url']=="money"){
            if(!array_key_exists("coin",$data)){
                $data['coin']="";
            }
            if($data['coin']=="user"){
                $coin=Db::table('user_coin')
                    ->alias('c')
                    ->join('manga v','v.manga_id=c.video_id')
                    ->join('member m','m.id=c.user_id')
                    ->where('c.user_id',$form['id'])->paginate(10);
                $page=$coin->render();
                $num=1;
                $this->assign('num',$num);
                $this->assign('coin',$coin);
                $this->assign('page',$page);
                return $this->fetch("money_user");
            }else{
                $coin=Db::table('user_income')
                ->alias('c')
                ->join('manga v','v.manga_id=c.user_income_manga')
                ->join('member m','m.id=c.user_income_member')
                ->where('c.user_income_member',$form['id'])->paginate(10);
                $page=$coin->render();
                $num=1;
                $this->assign('num',$num);
                $this->assign('coin',$coin);
                $this->assign('page',$page);
                return $this->fetch("money");
            }

        }
        elseif($data['url']=="face"){
            return $this->fetch("face");
        }
        elseif($data['url']=="site"){
            return $this->fetch("site");
        }
    }
}