<?php
namespace app\admin\controller;
use think\Collection;
use think\Controller;
use think\Db;
use think\Request;
use extend\Auth;
use think\Paginator;
class Users extends Commons
{
    public function admin_add(){
        $data=Db::table('auth_group')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function admin_insert(){
        $data=input('post.');
        if($data['admin_password'] ==$data['password2']){
            unset($data['password2']);
            $dat['group_id']=$data['group_id'];
            unset($data['group_id']);
            $data['admin_password']=md5($data['admin_password']);
            $data['time']=time();
            Db::table('admin_user')->insert($data);
            $form=Db::table('admin_user')->getLastInsID();
            if($form){
                $dat['uid']=$form;
                $dat=Db::table('auth_group_access')->insert($dat);
                if($dat){
                    return "<script language='javascript'>alert('添加管理员成功！');history.go(-2);</script>";
                }else{
                    return "<script language='javascript'>alert('添加管理员失败！');history.go(-2);</script>";
                }
            }
        }else{
            return "<script language='javascript'>alert('管理员添加失败！');history.go(-1);</script>";
        }
    }

    public function admin_list(){
        $page=Db::table('admin_user')->paginate(10);
        $page->toArray();
        foreach($page as $k=>$v){
            $dat="";
            $v['title']="";
            $user=Db::table('auth_group_access')->where('uid',$v['id'])->select();
            for($i=0;$i<count($user);$i++){
                $use=Db::table('auth_group')->where('id',$user[$i]['group_id'])->field('title')->select();
                if(!count($use)==0){
                    if(($i+1) == (count($user))){
                        $dat .=$use[0]['title'];
                    }else{
                        $dat .=$use[0]['title'].',';
                    }
                }
                $v['title'] =$dat;
            }
            $page->offsetSet($k,$v);
        }
        $number=Db::table('admin_user')->count();
        $this->assign('number',$number);
        $data=$page->render();
        $this->assign('data',$page);
        $this->assign('page',$data);
        return $this->fetch();
    }

    public function admin_static(){
        $data=input('post.');
        $video['static']=$data['wid'];
        $video=Db::table('admin_user')->where('id',$data['id'])->update($video);
        if($video){
            return json('1');
        }else{
            return json('2');
        }
    }

    public function admin_edit(){
        $data=input('get.');
        $form=Db::table('admin_user')->where('id',$data['id'])->find();
        $menu=Db::table('auth_group')->select();
        $this->assign('form',$form);
        $this->assign('menu',$menu);
        return $this->fetch();
    }

    public function admin_update(){
        $data=input('post.');
        if($data['admin_password'] ==$data['password2']){
            unset($data['password2']);
            $dat['group_id']=$data['group_id'];
            unset($data['group_id']);
            Db::table('admin_user')->where('id',$data['id'])->update($data);
            $form=Db::table('admin_user')->getLastInsID();
            if(!$form){
                $dat['uid']=$data['id'];
                $fo=Db::table('auth_group_access')->where('uid',$dat['uid'])->select();
                for($i=0;$i<count($fo);$i++){
                    if($fo[$i]['group_id'] == $dat['group_id']){
                        return "<script language='javascript'>alert('编辑管理员成功！');history.go(-2);</script>";
                    }else{
                        $dat=Db::table('auth_group_access')->insert($dat);
                        if($dat){
                            return "<script language='javascript'>alert('编辑管理员成功！');history.go(-2);</script>";
                        }else{
                            return "<script language='javascript'>alert('编辑管理员失败！');history.go(-2);</script>";
                        }
                    }
                }
            }
        }else{
            return "<script language='javascript'>alert('管理员编辑失败！');history.go(-1);</script>";
        }
    }

    public function admin_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++){
                $form=Db::table('admin_user')->where('id',$item['item'][$j])->delete();
                $fo=Db::table('auth_group_access')->where('uid',$item['item'][$j])->delete();

            }
            if($form){
                if($fo){
                    return $this->success("批量删除成功","admin_list");
                }else{
                    return $this->error("批量删除失败","admin_list");
                }
            }else{
                return $this->error("批量删除失败","admin_list");
            }
        }
        $form=Db::table('admin_user')->where('id',$data['id'])->delete();
        if($form){
            $fo=Db::table('auth_group_access')->where('uid',$data['id'])->delete();
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

    public function admin_auth(){
        $data=Db::table('auth_power')->paginate(10);
        $page=$data->render();
        $number=Db::table('auth_power')->count();
        $this->assign('number',$number);
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function admin_auth_upload(){
        $data=input();
        if($data['level'] == '二级'){
            $da['name']=Db::table('auth_power')->where('id',$data['parent_id'])->find();
            $da=Db::table('auth_power')->where('id',$data['id'])->find();
            $data=Db::table('auth_power')->where('level','二级')->select();
            $this->assign('data',$data);
            $this->assign('da',$da);
            return $this->fetch();
        }elseif($data['level'] == '一级'){
            $da=Db::table('auth_power')->where('id',$data['id'])->find();
            $this->assign('da',$da);
            return $this->fetch('auth_upload_two');
        }else{
            $da=Db::table('auth_power')->where('id',$data['id'])->find();
            $data=Db::table('auth_power')->where('level','二级')->select();
            $this->assign('data',$data);
            $this->assign('da',$da);
            return $this->fetch('auth_upload_there');
        }
    }

    public function auth_upload(){
        $data=input('post.');
        $form=Db::table('auth_power')->where('id',$data['id'])->update($data);
        if($form){
            return "<script language='javascript'>alert('编辑网站权限成功！');history.go(-2);</script>";
        }else{
            return "<script language='javascript'>alert('编辑网站权限失败！');history.go(-2);</script>";
        }
    }

    public function auth_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('auth_power')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('权限删除成功','admin_auth');
            }else{
                return $this->error('权限删除失败','admin_auth');
            }
        }
        $form=Db::table('auth_power')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function admin_auth_one(){
        return $this->fetch();
    }

    public function admin_auth_two(){
        $data=Db::table('auth_power')->where('level','一级')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function admin_auth_there(){
        $data=Db::table('auth_power')->where('level','二级')->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function auth_insert(){
        $data=input('post.');
        $form=Db::table('auth_power')->insert($data);
        if($form){
            return $this->success("权利类型添加成功","admin_auth");
        }else{
            return $this->error("权利类型添加失败","admin_auth");
        }
    }

    public function admin_role(){
        $data=Db::table('auth_group')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $number=Db::table('auth_group')->count();
        $this->assign('data',$data);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function admin_role_add(){
        $this->auth_tree();
        return $this->fetch();
    }

    public function role_insert(){
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
        $form=Db::table('auth_group')->insert($data);
        if($form){
            return "<script language='javascript'>alert('添加网站角色成功！');history.go(-2);</script>";
        }else{
            return "<script language='javascript'>alert('添加网站角色失败！');history.go(-2);</script>";
        }

    }

    public function role_update(){
        $data=input('get.');
        $form=Db::table('auth_group')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        $this->auth_tree();
        return $this->fetch();
    }

    public function role_update_add(){
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
        $form=Db::table('auth_group')->where('id',$data['id'])->update($data);
        if($form){
           return "<script language='javascript'>alert('编辑网站角色成功！');history.go(-2);</script>";
        }else{
          return "<script language='javascript'>alert('编辑网站角色失败！');history.go(-2);</script>";
        }
    }

    public function role_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('auth_group')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('角色删除成功','admin_role');
            }else{
                return $this->error('角色删除失败','admin_role');
            }
        }
        $form=Db::table('auth_group')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }
}