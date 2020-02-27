<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Manage extends Commons{
    //各主副页人工推荐
    public function push_list(){
        $push=Db::table('manage_push')->alias('m')->join('video v','v.id=m.push_video')->paginate(10);
        $page=$push->render();
        $push->toArray();
        foreach($push as $k=>$v){
            $v['push_times']=$v['push_time']+6*24*60*60-time();
            $push->offsetSet($k,$v);
        }
        $this->assign('page',$page);
        $this->assign('push',$push);
        return $this->fetch();
    }

    public function push_add(){
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function push_edit(){
        $data=input('get.');
        $push=Db::table('manage_push')->alias('m')->join('video v','v.id=m.push_video')->where('push_id',$data['id'])->find();
        $this->assign('push',$push);
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function push_form(){
        $data=input('post.');
        $data['push_time']=time();
        $form=Db::table('manage_push')->insert($data);
        Db::table('manage_push_push')->where('pushs_video',$data['push_video'])->delete();
        if($form){
            return $this->success('推荐添加成功','push_list');
        }else{
            return $this->error('推荐添加失败','push_list');
        }
    }

    public function push_forms(){
        $data=input('post.');
        $data['push_time']=time();
        $form=Db::table('manage_push')->where('push_id',$data['push_id'])->update($data);
        Db::table('manage_push_push')->where('pushs_video',$data['push_video'])->delete();
        if($form){
            return $this->success('推荐添加成功','push_list');
        }else{
            return $this->error('推荐添加失败','push_list');
        }
    }

    public function push_ajax(){
        $data=input('post.');
        $ajax=Db::table('manage_push_push')->alias('m')->join('video v','v.id=m.pushs_video')->where('pushs_index',$data['index'])->select();
        $text="";
        for($i=0;$i<count($ajax);$i++){
            $text .="<option value='".$ajax[$i]['pushs_video']."'>".$ajax[$i]['title']."</option>";
        }
        return json($text);
    }

    public function push_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('manage_push')->where('push_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","push_list");
            }else{
                return $this->error("批量删除失败","push_list");
            }
        }
        $da=Db::table('manage_push')->where('push_id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    //作品自己推荐
    public function pushs_list(){
        $push=Db::table('manage_push_push')->alias('m')->join('video v','v.id=m.pushs_video')->paginate(10);
        $page=$push->render();
        $this->assign('page',$page);
        $this->assign('push',$push);
        return $this->fetch();
    }

    public function pushs_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('manage_push_push')->where('push_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","pushs_list");
            }else{
                return $this->error("批量删除失败","pushs_list");
            }
        }
        $da=Db::table('manage_push_push')->where('push_id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function pushs_ajax(){
        $data=input('post.');
        $ajax=Db::table('video')->where('id',$data['id'])->find();
        return json($title=$ajax['title']);
    }

    public function pushs_add(){
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function pushs_form(){
        $data=input('post.');
        $data['pushs_time']=time();
        $form=Db::table('manage_push_push')->insert($data);
        if($form){
            return $this->success('候补添加成功','pushs_list');
        }else{
            return $this->error('候补添加失败','pushs_list');
        }
    }

    //导流推荐
    public function guide_list(){
        $push=Db::table('manage_guide')->alias('m')->join('video v','v.id=m.guide_video')->paginate(10);
        $page=$push->render();
        $push->toArray();
        foreach($push as $k=>$v){
            $v['guide_times']=$v['guide_time']+6*24*60*60-time();
            $push->offsetSet($k,$v);
        }
        $this->assign('page',$page);
        $this->assign('guide',$push);
        return $this->fetch();
    }

    public function guide_add(){
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function guide_edit(){
        $data=input('get.');
        $push=Db::table('manage_guide')->alias('m')->join('video v','v.id=m.guide_video')->where('guide_id',$data['id'])->find();
        $this->assign('push',$push);
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function guide_form(){
        $data=input('post.');
        $data['guide_time']=time();
        $form=Db::table('manage_guide')->insert($data);
        Db::table('manage_guides')->where('guides_video',$data['guide_video'])->delete();
        if($form){
            return $this->success('导流添加成功','guide_list');
        }else{
            return $this->error('导流添加失败','guide_list');
        }
    }

    public function guide_forms(){
        $data=input('post.');
        $data['guide_time']=time();
        $form=Db::table('manage_guide')->where('guide_id',$data['guide_id'])->update($data);
        Db::table('manage_guides')->where('guides_video',$data['guide_video'])->delete();
        if($form){
            return $this->success('导流添加成功','guide_list');
        }else{
            return $this->error('导流添加失败','guide_list');
        }
    }

    public function guide_ajax(){
        $data=input('post.');
        $ajax=Db::table('manage_guides')->alias('m')->join('video v','v.id=m.guides_video')->where('guides_index',$data['index'])->select();
        $text="";
        for($i=0;$i<count($ajax);$i++){
            $text .="<option value='".$ajax[$i]['guides_video']."'>".$ajax[$i]['title']."</option>";
        }
        return json($text);
    }

    public function guide_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('manage_guide')->where('guide_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","guide_list");
            }else{
                return $this->error("批量删除失败","guide_list");
            }
        }
        $da=Db::table('manage_guide')->where('guide_id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    //导流候补
    public function guides_list(){
        $push=Db::table('manage_guides')->alias('m')->join('video v','v.id=m.guides_video')->paginate(10);
        $page=$push->render();
        $this->assign('page',$page);
        $this->assign('guides',$push);
        return $this->fetch();
    }

    public function guides_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('manage_guides')->where('guides_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","guides_list");
            }else{
                return $this->error("批量删除失败","guides_list");
            }
        }
        $da=Db::table('manage_guides')->where('guides_id',$data['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function guides_ajax(){
        $data=input('post.');
        $ajax=Db::table('video')->where('id',$data['id'])->find();
        return json($title=$ajax['title']);
    }

    public function guides_add(){
        $nav=Db::table('video_nav')->where(array('level'=>2,'page'=>1))->select();
        $this->assign('nav',$nav);
        return $this->fetch();
    }

    public function guides_form(){
        $data=input('post.');
        $data['guides_time']=time();
        $form=Db::table('manage_guides')->insert($data);
        if($form){
            return $this->success('导流添加成功','guides_list');
        }else{
            return $this->error('导流添加失败','guides_list');
        }
    }

}