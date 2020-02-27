<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Love extends Commons{
    //新番月份展示
    public function love_month_list(){
        $form=Db::table('total_month')->paginate(20);
        $page=$form->render();
        $this->assign('page',$page);
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番月份添加
    public function love_month_add(){
        return $this->fetch();
    }

    //新番月份数据保存
    public function love_month_form(){
        $data=input('post.');
        $data['total_month_time']=time();
        $form =  Db::table('total_month')->insert($data);
        if($form) {
            return $this->success("添加成功", "love_month_list");
        } else {
            return $this->error("添加失败", "love_month_list");
        }
    }

    //新番月份修改提取数据
    public function love_month_update(){
        $data=input('get.');
        $form=Db::table('total_month')->where('total_month_id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番月份修改数据
    public function update_love_month(){
        $data=input('post.');
        $data['total_month_time']=time();
        $form =  Db::table('total_month')->where('total_month_id',$data['total_month_id'])->update($data);
        if($form) {
            return $this->success("修改成功", "love_month_list");
        } else {
            return $this->success("修改失败", "love_month_list");
        }
    }

    //新番月份删除数据
    public function love_month_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('total_month')->where('total_month_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","love_month_list");
            }else{
                return $this->error("批量删除失败","love_month_list");
            }
        }
        $form=Db::table('total_month')->where('total_month_id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }

    //新番星期几展示
    public function love_week_list(){
        $form=Db::table('total_week')->paginate(20);
        $page=$form->render();
        $this->assign('page',$page);
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番星期几添加
    public function love_week_add(){
        return $this->fetch();
    }

    //新番星期几数据保存
    public function love_week_form(){
        $data=input('post.');
        $data['total_week_time']=time();
        $form =  Db::table('total_week')->insert($data);
        if($form) {
            return $this->success("添加成功", "love_week_list");
        } else {
            return $this->error("添加失败", "love_week_list");
        }
    }

    //新番星期几修改提取数据
    public function love_week_update(){
        $data=input('get.');
        $form=Db::table('total_week')->where('total_week_id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番星期几修改数据
    public function update_love_week(){
        $data=input('post.');
        $data['total_week_time']=time();
        $form =  Db::table('total_week')->where('total_week_id',$data['total_week_id'])->update($data);
        if($form) {
            return $this->success("修改成功", "love_week_list");
        } else {
            return $this->success("修改失败", "love_week_list");
        }
    }

    //新番星期几删除
    public function love_week_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('total_week')->where('total_week_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","love_week_list");
            }else{
                return $this->error("批量删除失败","love_week_list");
            }
        }
        $form=Db::table('total_week')->where('total_week_id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }

    //新番作品更新展示
    public function love_video_list(){
        $form=Db::table('total_video')
            ->alias('v')
            ->join('total_month m','v.total_video_month=m.total_month_id')
            ->join('total_week w','v.total_video_week=w.total_week_id')
            ->paginate(20);
        $page=$form->render();
        $this->assign('page',$page);
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番作品添加
    public function love_video_add(){
        $month=Db::table('total_month')->select();
        $week=Db::table('total_week')->select();
        $this->assign('month',$month);
        $this->assign('week',$week);
        return $this->fetch();
    }

    //新番作品数据保存
    public function love_video_form(){
        $data=input('post.');
        $data['total_video_time']=time();
        $form =  Db::table('total_video')->insert($data);
        if($form) {
            return $this->success("添加成功", "love_video_list");
        } else {
            return $this->error("添加失败", "love_video_list");
        }
    }

    //新番作品修改提取数据
    public function love_video_update(){
        $data=input('get.');
        $form=Db::table('total_video')->where('total_video_id',$data['id'])->find();
        $month=Db::table('total_month')->select();
        $week=Db::table('total_week')->select();
        $this->assign('month',$month);
        $this->assign('week',$week);
        $this->assign('form',$form);
        return $this->fetch();
    }

    //新番作品修改数据
    public function update_love_video(){
        $data=input('post.');
        $data['total_video_time']=time();
        $form =  Db::table('total_video')->where('total_video_id',$data['total_video_id'])->update($data);
        if($form) {
            return $this->success("修改成功", "love_video_list");
        } else {
            return $this->success("修改失败", "love_video_list");
        }
    }

    //新番作品删除
    public function love_video_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('total_video')->where('total_video_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","love_video_list");
            }else{
                return $this->error("批量删除失败","love_video_list");
            }
        }
        $form=Db::table('total_video')->where('total_video_id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }
}