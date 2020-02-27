<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Member extends Commons{
    public function member_list(){
        $condition=input('post.');
        if(!empty($condition)){
             if($condition['name']==''){unset($condition['name']);};
            if($condition['id']==''){unset($condition['id']);};
            if($condition['level']==''){unset($condition['level']);};
        }
        $data=Db::table('member')->where($condition)->paginate(10);
        $page=$data->render();
        $number=count($data);
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function member_add(){
        return $this->fetch();
    }

    public function member_del(){
        $data=Db::table('member_del')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function member_del_restore(){
        $id=input('post.');
        $form=Db::table('member_del')->where('id',$id['id'])->find();
        array_splice($form, 0, 1);
        Db::table('member')->insert($form);
        $user=Db::table('member_del')->where('id',$id['id'])->delete();
        if($user){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function member_del_date(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('member_del')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","member_del");
            }else{
                return $this->error("批量删除失败","member_del");
            }
        }
        $da=Db::table('member_del')->where('id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function member_del_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $d=Db::table('member')->where('id',$item['item'][$i])->find();
                $d['time']=time();
                $fo=Db::table('member_del')->insert($d);
                Db::table('member')->where('id',$item['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","member_list");
            }else{
                return $this->error("批量删除失败","member_lsit");
            }
        }
        $d=Db::table('member')->where('id',$data['id'])->find();
        $d['time']=time();
        $form=Db::table('member_del')->insert($d);
        $da=Db::table('member')->where('id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function member_static(){
        $data=input('post.');
        $form=Db::table('member')->where('id',$data['id'])->update(array('status'=>$data['status']));
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function level_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('member_level')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","member_level");
            }else{
                return $this->error("批量删除失败","member_level");
            }
        }
        $d=Db::table('member_level')->where('id',$data['id'])->delete();
        if($d){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function integral_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('member_integral')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","member_integral");
            }else{
                return $this->error("批量删除失败","member_integral");
            }
        }
        $d=Db::table('member_integral')->where('id',$data['id'])->delete();
        if($d){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function member_up(){
        $data=input('post.');
        $data['time']=time();
        $data['password']=md5($data['password']);
        $form=Db::table('member')->insert($data);
        if($form){
            return $this->success('添加用户成功','member_list');
        }else{
            return $this->error('添加用户失败','member_list');
        }
    }

    public function member_updata(Request $request){
        $data=$request->param();
        $form=Db::table('member')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function member_upda(){
        $data=input('post.');
        $data['time']=time();
        $form=Db::table('member')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success('修改用户成功','member_list');
        }else{
            return $this->error('修改用户失败','member_list');
        }
    }

    public function member_show(){
        $data=input('get.');
        $user=Db::table('member')->where('id',$data['id'])->find();
        $this->assign('user',$user);
        return $this->fetch();
    }

    public function member_level(){
        $data=Db::table('member_level')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function member_integral(){
        $data=Db::table('member_integral')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function level_add(){
        return $this->fetch();
    }

    public function integral_add(){
        return $this->fetch();
    }
    public function level_updata(Request $request){
        $data=$request->param();
        $form=Db::table('member_level')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function integral_updata(Request $request){
        $data=$request->param();
        $form=Db::table('member_integral')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }
    public function level_up(){
        $data=input('post.');
        $data=Db::table('member_level')->insert($data);
        if($data){
            return $this->success('添加等级成功','member_level.html');
        }else{
            return $this->error('添加等级失败','member_level.html');
        }
    }

    public function integral_up(){
        $data=input('post.');
        $data=Db::table('member_integral')->insert($data);
        if($data){
            return $this->success('添加积分成功','member_integral.html');
        }else{
            return $this->error('添加积分失败','member_integral.html');
        }
    }

    public function integral_upda(){
        $data=input('post.');
        $form=Db::table('member_integral')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success('修改积分成功','member_integral.html');
        }else{
            return $this->error('修改积分失败','member_integral.html');
        }
    }

    public function level_upda(){
        $data=input('post.');
        $form=Db::table('member_level')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success('修改等级成功','member_level.html');
        }else{
            return $this->error('修改等级失败','member_level.html');
        }
    }

    public function member_sign(){
        $data=Db::table('member_sign')->alias('s')
            ->join('member m','s.sign_uid=m.id')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $number=count($data);
        $this->assign('number',$number);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function member_coin(){
        $data=Db::table('user_coin')->alias('c')
            ->join('video v','v.id=c.video_id')
            ->join('member m','m.id=c.user_id')
            ->join('member n','n.id=c.users_id')
            ->join('video_collect i','i.collectionId=c.coin_data')
            ->field('c.coin_id,m.name user,n.name users,v.title,c.coin_time,c.coin,i.Text,c.classify')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $number=count($data);
        $this->assign('number',$number);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function coin_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('user_coin')->where('coin_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","member_coin");
            }else{
                return $this->error("批量删除失败","member_coin");
            }
        }
        $d=Db::table('user_coin')->where('coin_id',$data['id'])->delete();
        if($d){
            return json('n');
        }else{
            return json('j');
        }
    }
}