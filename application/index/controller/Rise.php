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

class Rise extends Comment
{
    public function rise(){
        $data=input('get.');
        $this->header_nav();
        $this->nav_login();
        if(empty($data)){
            $rise=Db::table('rise_level_date')
                ->alias('r')->join('member m','r.rise_level_date_name=m.id')
                ->paginate(10);
            $page=$rise->render();
            $this->assign("rise",$rise);
            $this->assign("page",$page);
            return $this->fetch();
        }else{
            $rises=Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->find();
            $member=Db::table('member')->where('id',$rises['rise_level_date_name'])->find();
            if($data['static']=='level'){
                $rise=Db::table('level_tutor')->where('level_tutor_user',$member['id'])->where('level_tutor_level',$member['level'])->select();
                $this->assign('rise',$rise);
                return $this->fetch('rise_list_number');
            }elseif($data['static']=='tor'){
                $rise=Db::table('level_tutor')->where('level_tutor_user',$member['id'])->where('level_tutor_level',$member['level']-1)->select();
                $this->assign('rise',$rise);
                return $this->fetch('rise_list_number1');
            }elseif($data['static']=='comment'){
                $rise=Db::table('manga_comment')
                    ->alias('r')
                    ->join('member m','m.id=r.manga_comment_member')
                    ->where('manga_comment_member',$member['id'])
                    ->select();
                $this->assign('rise',$rise);
                return $this->fetch('rise_list_number2');
            }elseif($data['static']=='article'){
                $rise=Db::table('manga_article')->where('manga_article_member',$member['id'])->select();
                $this->assign('rise',$rise);
                return $this->fetch('rise_list_number3');
            }
        }
    }

    public function rises(){
        $data['rise']=input('rise');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $members=Db::table('member')->where('id',$user)->find();
        $rise=Db::table('rise_level_date')
            ->alias('r')->join('member m','r.rise_level_date_name=m.id')
            ->where('rise_level_date_id',$data['rise'])
            ->find();
        if($rise['rise_level_date_fans']>=$rise['rise_level_date_fans_up']
            &&$rise['rise_level_date_support']>=$rise['rise_level_date_support_up']) {
            $rise['level_power']=1;
        }else{
            $rise['level_power']=0;
        }
        $rise_power=Db::table('member_level_power')->where('member_level_power_name',$rise['rise_level_date_name'])->where('member_level_power_level',$rise['rise_level_date_level'])->find();
        if($rise_power){
            $rise['rise_power']=1;
        }else{
            $rise['rise_power']=0;
        }
        $member=Db::table('member')->where('id',$rise['rise_level_date_name'])->find();
        $rise['fans_width']="style=width:".(($rise['rise_level_date_fans']/$rise['rise_level_date_fans_up'])*100)."%";
        $rise['support_width']="style=width:".(($rise['rise_level_date_support']/$rise['rise_level_date_support_up'])*100)."%";
        if($user==$rise['rise_level_date_name']){
            $rise['login_static']=1;
        }else{
            $rise['login_static']=0;
        }
        $fans=Db::table('rise_level_fans')->where(array('rise_level_fans_name'=>$user,'rise_level_fans_level'=>$rise['rise_level_date_level']))->find();
        if($fans){
            $rise['fans_static']=1;
        }else{
            $rise['fans_static']=0;
        }
        $support=Db::table('rise_level_support')->where(array('rise_level_support_name'=>$user,'rise_level_support_level'=>$rise['rise_level_date_level']))->find();
        if($support){
            $rise['support_static']=1;
        }else{
            $rise['support_static']=0;
        }
        if($members['level']>$rise['rise_level_date_level']){
            $rise['support_level']=1;
        }else{
            $rise['support_level']=0;
        }
        $this->assign('member',$member);

        $comment=Db::table('rise_level_comment')
            ->alias('c')
            ->join('member m','m.id=c.rise_comment_member')
            ->where(array('c.rise_comment_name'=>$data['rise'],'c.rise_comment_reply'=>0,'c.rise_comment_static'=>1))
            ->order('c.rise_comment_floor desc')
            ->paginate(10,true,[
                'var_page' => 'page',
            ]);
        $comment->toArray();
        foreach($comment as $k =>$v){
            $v['comment']=Db::table('rise_level_comment')
                ->alias('c')
                ->join('member m','m.id=c.rise_comment_member')
                ->where(array('c.rise_comment_name'=>$data['rise'],'c.rise_comment_num'=>1,'c.rise_comment_static'=>1,'c.rise_comment_floor'=>$v['rise_comment_floor']))
                ->order('c.rise_comment_time asc')
                ->paginate(10,true,[
                    'var_page' => 'pages',
                ]);
            $v['pages']=$v['comment']->render();
            $comment->offsetSet($k,$v);
        }
        $page=$comment->render();
        $this->assign('page',$page);
        $this->assign('comment',$comment);
        $this->assign('rise',$rise);
        return $this->fetch();
    }

    public function rise_list(){
        $data=input('get.');
        $this->header_nav();
        $this->nav_login();
        $name=Session::get('users');
        if(!$name){
            return $this->fetch("login/login");
        }else{
            $form=Db::table('member')->where('id',$name)->find();
        }
        if(empty($data)){
            $rise=Db::table('member_level_rise')
                ->alias('r')->join('member m','r.member_level_rise_name=m.id')
                ->where('m.id',$name)
                ->select();
            $this->assign("rise",$rise);
            $this->assign("form",$form);
            return $this->fetch();
        }else{
            $rise=Db::table('rise_level_date')->where(array('rise_level_date_name'=>$name,'rise_level_date_level'=>$data['rise']))->find();
            $rise_power=Db::table('member_level_power')->where('member_level_power_name',$name)->where('member_level_power_level',$data['rise'])->find();
            if($rise_power){
                $rise['rise_power']=1;
            }else{
                $rise['rise_power']=0;
            }
            $this->assign('rise',$rise);
            return $this->fetch("rise_lists");
        }

    }

    public function rise_money(){
        $data=input('post.');
        $user=Session::get('users');
        $id=Db::table('member')->where('id',$user)->find();
        if($id['money']>=$data['money']){
            Db::table('member')->where('id',$user)->setDec('money',$data['money']);
            $form=Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->where('rise_level_date_name',$user)->setInc('rise_level_date_money',$data['money']);
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }else{
            return json(0);
        }

    }

    public function rise_text(){
        $data=input('post.');
        $name=Session::get('users');
        $form=Db::table('rise_level_date')->where(array('rise_level_date_name'=>$name,'rise_level_date_id'=>$data['id']))->setField('rise_level_date_text',$data['text']);
        if($form){
            $text['msg']=1;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function rise_fans_become(){
        $data=input('post.');
        $user=Session::get('users');
        $rise=Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->find();
        $money=$rise['rise_level_date_money']*0.1/$rise['rise_level_date_fans_up'];
        Db::table('member')->where('id',$user)->setInc('money',$money);
        Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->setInc('rise_level_date_fans',1);
        Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->setDec('rise_level_date_money',$money);
        $form=Db::table('rise_level_fans')->insert(array('rise_level_fans_name'=>$user,'rise_level_fans_uid'=>$rise['rise_level_date_id'],'rise_level_fans_level'=>$rise['rise_level_date_level'],'rise_level_fans_time'=>time()));
        $width=(($rise['rise_level_date_fans']+1)/$rise['rise_level_date_fans_up']*100)."%";
        $span=($rise['rise_level_date_fans']+1)."/".$rise['rise_level_date_fans_up'];
        if($form){
            $text['msg']=1;
            $text['money']=$money;
            $text['width']=$width;
            $text['span']=$span;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function rise_support_become(){
        $data=input('post.');
        $user=Session::get('users');
        $rise=Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->find();
        $money=$rise['rise_level_date_money']*0.1/$rise['rise_level_date_support_up'];
        Db::table('member')->where('id',$user)->setInc('money',$money);
        Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->setInc('rise_level_date_support',1);
        Db::table('rise_level_date')->where('rise_level_date_id',$data['id'])->setDec('rise_level_date_money',$money);
        $form=Db::table('rise_level_support')->insert(array('rise_level_support_name'=>$user,'rise_level_support_uid'=>$rise['rise_level_date_id'],'rise_level_support_level'=>$rise['rise_level_date_level'],'rise_level_support_time'=>time()));
        $width=(($rise['rise_level_date_support']+1)/$rise['rise_level_date_support_up']*100)."%";
        $span=($rise['rise_level_date_support']+1)."/".$rise['rise_level_date_support_up'];
        if($form){
            $text['msg']=1;
            $text['money']=$money;
            $text['width']=$width;
            $text['span']=$span;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function rise_comment(){
        $data=input('post.');
        if(array_key_exists('user',$data)){
            $num=$data['number'];
        }else{
            $num=count(Db::table('rise_level_comment')->where('rise_comment_name',$data['id'])->select());
            $num=$num+1;
            $data['user']=0;
        }
        if(array_key_exists('nums',$data)){
            $nums=$data['nums'];
        }else{
            $nums=0;
        }
        $where=array(
            'rise_comment_name'=>$data['id'],
            'rise_comment_member'=>Session::get('users'),
            'rise_comment_text'=>htmlspecialchars($data['value']),
            'rise_comment_time'=>time(),
            'rise_comment_static'=>1,
            'rise_comment_floor'=>$num,
            'rise_comment_num'=>$nums,
            'rise_comment_reply'=>$data['user']
        );
        $form=Db::table('rise_level_comment')->insert($where);
        $member=Db::table('member')->where('id',Session::get('users'))->find();
        if($member['image']==''){
            $member['image']="default.jpg";
        }
        $where['images']=$member['image'];
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

    public function level_power(){
        $data=input('post.');
        $user=Session::get('users');
        $where=array(
            'member_level_power_name'=>$user,
            'member_level_power_level'=>$data['id'],
            'member_level_power_static'=>1,
            'member_level_power_time'=>time()
        );
        $form=Db::table('member_level_power')->insert($where);
        if($form){
            return json(1);
        }else{
            return json(0);
        }
    }
}