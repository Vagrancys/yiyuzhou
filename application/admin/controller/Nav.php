<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Nav extends Commons{
    public function nav(){
        $data=Db::table('video_nav')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function nav_add(){
        $data=Db::table('video_nav')->where('page',0)->where('level',1)->select();
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function nav_single(){
        $data=input('post.');
        if(!empty($data)){
            if($data['total']==1){
                $where=array(
                    'page'=>$data['id'],
                    'level'=>2
                );
                $form=Db::table('video_nav')->where($where)->select();
                $text="<option value='0'>请选择</option>";
                for($i=0;$i<count($form);$i++){
                    $text .="<option value=".$form[$i]['id'].">".$form[$i]['name']."</option>";
                }
            }elseif($data['total']==3){
                $text="<option value='0'>请选择</option>";
            }else{
                $where=array(
                    'level'=>2,
                    'vo'=>1
                );
                $form=Db::table('video_nav')->where($where)->select();
                $text="<option value='0'>请选择</option>";
                for($i=0;$i<count($form);$i++){
                    $text .="<option value=".$form[$i]['id'].">".$form[$i]['name']."</option>";
                }
            }
            return json($text);
        }
    }

    public function nav_total(){
        $data=input('post.');
        if(!empty($data)){
            if(!empty($data['total'])){
                if($data['total']== 3&&$data['vo']==1){
                    $where=array(
                        'page'=>$data['id'],
                        'level'=>2
                    );
                }elseif($data['total']==3&&$data['vo']==2){
                    $where=array(
                        'level'=>2,
                        'vo'=>1
                    );
                }elseif($data['total']==4){
                    $where=array(
                        'level'=>3,
                        'page'=>$data['v5']
                    );
                }else{
                    $where=array(
                        'level'=>2,
                        'vo'=>1
                    );
                }
            }
            $form=Db::table('video_nav')->where($where)->select();
            $text="<option value='0'>请选择</option>";
            for($i=0;$i<count($form);$i++){
                $text .="<option value=".$form[$i]['id'].">".$form[$i]['name']."</option>";
            }
            return json($text);
        }
    }


    public function nav_nav(){
        $data=input('post.');
        if(!empty($data)){
            if(!empty($data['total'])){
                if($data['total']== 4&&$data['vo']==1){
                    $where=array(
                        'page'=>$data['id'],
                        'level'=>3
                    );
                }
            }
            $form=Db::table('video_nav')->where($where)->select();
            $text="<option value='0'>请选择</option>";
            for($i=0;$i<count($form);$i++){
                $text .="<option value=".$form[$i]['id'].">".$form[$i]['name']."</option>";
            }
            return json($text);
        }
    }

    public function nav_form(){
        $data=input('post.');
        if(!empty($data)){
            if($data['vo']== 1){
                if($data['level']==1){
                    $data['page']=$data['single'];
                }elseif($data['level']==2){
                }elseif($data['level']==3&&$data['nav']==1){
                    $data['single']=$data['pages'];
                }elseif($data['level']==3&&$data['nav']==2){
                    $data['page']=$data['total'];
                    $data['total']=$data['single'];
                }elseif($data['level']==4){
                    $data['page']=$data['single'];
                    $data['single']="0";
                }
            }elseif($data['vo']=1&&(!$data['total']==0)){
                $data['single']=$data['total'];
                $data['total']="0";
            }
            $array=array(
                'level','page','name','single','total'
            );
            for($i=0;$i<count($array);$i++){
                $da[$array[$i]]=$this->infusion($array[$i],$data);
            }
            $form=Db::table('video_nav')->insert($da);
           /* if($form){
                return $this->success('添加导航成功！','nav');
            }else{
                return $this->error('添加导航失败！','nav');
            }*/
        }else{
            return $this->error('添加导航失败！','nav');
        }
    }

    public function nav_edit(){
        $data=input('get.');
        $data=Db::table('video_nav')->where('id',$data['id'])->find();
        $da=Db::table('video_nav')->where('page',0)->where('level',1)->select();
        $this->assign('da',$da);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function nav_edit_form(){
        $data=input('post.');
        if(!empty($data)){
            if($data['vo']== 2){
                if(!$data['level']==4){
                    $data['page']=$data['single'];
                    $data['single']=$data['total'];
                }elseif($data['level']==2){
                    $data['page']=$data['single'];
                    $data['single']=$data['total'];
                }elseif($data['level']==3){
                    $data['page']=$data['total'];
                    $data['total']=$data['single'];
                }elseif($data['level']==4){
                    $data['page']="0";
                    $data['single']="0";
                }
            }elseif($data['vo']=1&&(!$data['total']==0)){
                $data['single']=$data['total'];
                $data['total']="0";
            }
            $array=array(
                'level','page','name','single','total'
            );
            for($i=0;$i<count($array);$i++){
                $da[$array[$i]]=$this->infusion($array[$i],$data);
            }
            if($data['level']==2&&$data['vo']==2){
                $da['vo']="1";
            }
            $form=Db::table('video_nav')->where('id',$data['id'])->update($da);
            if($form){
                return $this->success('修改导航成功！','nav');
            }else{
                return $this->error('修改导航失败！','nav');
            }
        }else{
            return $this->error('修改导航失败！','nav');
        }
    }

    public function nav_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('video_nav')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","nav");
            }else{
                return $this->error("批量删除失败","nav");
            }
        }
        $form=Db::table('video_nav')->where('id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }
}