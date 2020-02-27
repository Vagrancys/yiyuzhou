<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;

class Comment extends Commons{

    public function comment_list(){
        $data=Db::table('news_comment')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function comment_status(){
        $data=input('post.');
        $form=Db::table('news_comment')->where('id',$data['id'])->update(array('news_static'=>$data['status']));
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function comment_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $d=Db::table('news_comment')->where('id',$item['item'][$i])->find();
                $fo=Db::table('news_comment_recovery')->insert($d);
                Db::table('news_comment')->where('id',$item['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","comment_list");
            }else{
                return $this->error("批量删除失败","comment_lsit");
            }
        }
        $user=Db::table('news_comment')->where('id',$data['id'])->find();
        Db::table('news_comment_recovery')->insert($user);
        $form=Db::table('news_comment')->where('id',$data['id'])->delete();
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function comment_recovery(){
        $data=Db::table('news_comment_recovery')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function comment_recovery_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $fo=Db::table('news_comment_recovery')->where('id',$item['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","comment_recovery");
            }else{
                return $this->error("批量删除失败","comment_recovery");
            }
        }
        $form=Db::table('news_comment_recovery')->where('id',$data['id'])->delete();
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function comment_recovery_insert(){
        $data=input('post.');
        $form=Db::table('news_comment_recovery')->where('id',$data['id'])->find();
        Db::table('news_comment')->insert($form);
        $form=Db::table('news_comment_recovery')->where('id',$data['id'])->delete();
        if($form){
            return json('n');
        }else{
            return json('j');
        }
    }
}