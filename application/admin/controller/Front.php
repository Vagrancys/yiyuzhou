<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Front extends Commons
{
    public function front_list()
    {
        $data = Db::table('system_front')->paginate(10);
        $page = $data->render();
        $number = Db::table('system_front')->count();
        $this->assign('data', $data);
        $this->assign('page', $page);
        $this->assign('number', $number);
        return $this->fetch();
    }

    public function system_front_add(){
        $li=Db::table('system_front')->where('cid',0)->select();
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function system_front_from(){
        $data=input('post.');
        $form=Db::table('system_front')->insert($data);
        if($form){
            return $this->success('前端模块添加成功！','front_list');
        }else{
            return $this->error('前端模块添加失败！','front_list');
        }
    }

    public function system_front_update(){
        $data=input('get.');
        $form=Db::table('system_front')->where('id',$data['id'])->find();
        $li=Db::table('system_front')->select();
        $this->assign('form',$form);
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function front_update(){
        $data=input('post.');
        $form=Db::table('system_front')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success('前端模块编辑成功！','front_list');
        }else{
            return $this->error('前端模块编辑失败！','front_list');
        }
    }

    public function front_del(){
        $data=input('post.');
        $da=input('get.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('system_front')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","front_list");
            }else{
                return $this->error("批量删除失败","front_list");
            }
        }
        $form=Db::table('system_front')->where('id',$da['id'])->delete();
        if(!$form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function front_power(){
        $page=Db::table('admin_front')->paginate(10);
        $page->toArray();
        foreach($page as $k=>$v){
            $v['title']="";
            $name=Db::table('video_nav')->where('id',$v['admin_page'])->find();
            $v['name']=$name['name'];
            $user=Db::table('front_auth_group_access')->where('uid',$v['id'])->find();
            $use=Db::table('front_auth_group')->where('id',$user['group_id'])->find();
            $v['title'] =$use['title'];
            $page->offsetSet($k,$v);
        }
        $number=Db::table('admin_front')->count();
        $this->assign('number',$number);
        $data=$page->render();
        $this->assign('data',$page);
        $this->assign('page',$data);
        return $this->fetch();
    }

    public function front_power_add(){
        $data=Db::table('front_auth_group')->select();
        $name=Db::table('video_nav')->where('page',1)->where('level',2)->select();
        $this->assign('name',$name);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function front_power_static(){
        $data=input('post.');
        $video['static']=$data['wid'];
        $video=Db::table('admin_front')->where('id',$data['id'])->update($video);
        if($video){
            return json('1');
        }else{
            return json('2');
        }
    }

    public function front_power_edit(){
        $data=input('get.');
        $form=Db::table('admin_front')->where('id',$data['id'])->find();
        $menu=Db::table('front_auth_group')->select();
        $this->assign('form',$form);
        $this->assign('menu',$menu);
        return $this->fetch();
    }

    public function front_power_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++){
                $form=Db::table('admin_front')->where('id',$item['item'][$j])->delete();
                $fo=Db::table('front_auth_group_access')->where('uid',$item['item'][$j])->delete();

            }
            if($form){
                if($fo){
                    return $this->success("批量删除成功","front_power");
                }else{
                    return $this->error("批量删除失败","front_power");
                }
            }else{
                return $this->error("批量删除失败","front_power");
            }
        }
        $form=Db::table('admin_front')->where('id',$data['id'])->delete();
        if($form){
            $fo=Db::table('front_auth_group_access')->where('uid',$data['id'])->delete();
            if($fo){
                $data='删除失败！';
                return json($data);
            }else{
                $data='删除成功！';
                return json($data);
            }
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function front_power_update(){
        $data=input('post.');
        $dat['group_id']=$data['group_id'];
        unset($data['group_id']);
        Db::table('admin_front')->where('id',$data['id'])->update($data);
        $form=Db::table('admin_user')->getLastInsID();
        if(!$form){
            $dat['uid']=$data['id'];
            $fo=Db::table('front_auth_group_access')->where('uid',$dat['uid'])->find();
                if($fo['group_id'] == $dat['group_id']){
                    return "<script language='javascript'>alert('编辑管理员成功！');history.go(-2);</script>";
                }else{
                    $dat=Db::table('front_auth_group_access')->where('uid',$data['admin_id'])->update($dat);
                    if($dat){
                        return "<script language='javascript'>alert('编辑管理员成功！');history.go(-2);</script>";
                    }else{
                        return "<script language='javascript'>alert('编辑管理员失败！');history.go(-2);</script>";
                    }
                }
        }
    }

    public function front_member(){
        $data=input('post.');
        $text=Db::table('member')->where('id',$data['id'])->find();
        if($text){
            return json($text);
        }else{
            return json(0);
        }
    }

    public function front_insert(){
        $data=input('post.');
        $dat['group_id']=$data['group_id'];
        unset($data['group_id']);
        $data['time']=time();
        Db::table('admin_front')->insert($data);
        $form=Db::table('admin_front')->getLastInsID();
        if($form){
            $dat['uid']=$form;
            $dat=Db::table('front_auth_group_access')->insert($dat);
            if($dat){
                return "<script language='javascript'>alert('添加管理员成功！');history.go(-2);</script>";
            }else{
                return "<script language='javascript'>alert('添加管理员失败！');history.go(-2);</script>";
            }
        }
    }

    public function front_auth(){
        $data=Db::table('front_auth_power')->paginate(10);
        $page=$data->render();
        $number=Db::table('front_auth_power')->count();
        $this->assign('number',$number);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function front_auth_one(){
        return $this->fetch();
    }

    public function front_auth_two(){
        $data=Db::table('front_auth_power')->where('level','一级')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function front_auth_there(){
        $data=Db::table('front_auth_power')->where('level','二级')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function front_auth_insert(){
        $data=input('post.');
        $form=Db::table('front_auth_power')->insert($data);
        if($form){
            return $this->success("权利类型添加成功","front_auth");
        }else{
            return $this->error("权利类型添加失败","front_auth");
        }
    }

    public function front_auth_upload(){
        $data=input();
        if($data['level'] == '二级'){
            $da['name']=Db::table('front_auth_power')->where('id',$data['parent_id'])->find();
            $da=Db::table('front_auth_power')->where('id',$data['id'])->find();
            $data=Db::table('front_auth_power')->where('level','二级')->select();
            $this->assign('data',$data);
            $this->assign('da',$da);
            return $this->fetch('front_upload_one');
        }elseif($data['level'] == '一级'){
            $da=Db::table('front_auth_power')->where('id',$data['id'])->find();
            $this->assign('da',$da);
            return $this->fetch('front_upload_two');
        }else{
            $da=Db::table('front_auth_power')->where('id',$data['id'])->find();
            $data=Db::table('front_auth_power')->where('level','二级')->select();
            $this->assign('data',$data);
            $this->assign('da',$da);
            return $this->fetch('front_upload_there');
        }
    }

    public function auth_front_upload(){
        $data=input('post.');
        $form=Db::table('front_auth_power')->where('id',$data['id'])->update($data);
        if($form){
            return "<script language='javascript'>alert('编辑网站权限成功！');history.go(-2);</script>";
        }else{
            return "<script language='javascript'>alert('编辑网站权限失败！');history.go(-2);</script>";
        }
    }

    public function front_auth_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('front_auth_power')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('权限删除成功','front_auth');
            }else{
                return $this->error('权限删除失败','front_auth');
            }
        }
        $form=Db::table('front_auth_power')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function front_role(){
        $data=Db::table('front_auth_group')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $number=Db::table('front_auth_group')->count();
        $this->assign('data',$data);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function front_role_add(){
        $this->front_auth_tree();
        return $this->fetch();
    }

    public function front_role_insert(){
        $data=input('post.');
        $da1="";
        for($j=0;$j<count($data['item']);$j++){
            if(($j+1) == (count($data['item']))){
                $da1 .=$data['item'][$j];
            }else{
                $da1 .=$data['item'][$j].',';
            }
        }
        $data['item']=$da1;
        $form=Db::table('front_auth_group')->insert($data);
        if($form){
            return "<script language='javascript'>alert('添加网站角色成功！');history.go(-2);</script>";
        }else{
            return "<script language='javascript'>alert('添加网站角色失败！');history.go(-2);</script>";
        }

    }

    public function front_role_update(){
        $data=input('get.');
        $form=Db::table('front_auth_group')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        $this->front_auth_tree();
        return $this->fetch();
    }

    public function front_role_update_add(){
        $data=input('post.');
        $da="";
        for($i=0;$i<count($data['rules']);$i++){
            if(($i+1) == (count($data['rules']))){
                $da .=$data['rules'][$i];
            }else{
                $da .=$data['rules'][$i].',';
            }
        }
        $data['rules']=$da;
        $da1="";
        for($j=0;$j<count($data['item']);$j++){
            if(($j+1) == (count($data['item']))){
                $da1 .=$data['item'][$j];
            }else{
                $da1 .=$data['item'][$j].',';
            }
        }
        $data['item']=$da1;
        $form=Db::table('front_auth_group')->where('id',$data['id'])->update($data);
        if($form){
            return "<script language='javascript'>alert('编辑网站角色成功！');history.go(-2);</script>";
        }else{
            return "<script language='javascript'>alert('编辑网站角色失败！');history.go(-2);</script>";
        }
    }

    public function front_role_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('front_auth_group')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('角色删除成功','front_role');
            }else{
                return $this->error('角色删除失败','front_role');
            }
        }
        $form=Db::table('front_auth_group')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }
}