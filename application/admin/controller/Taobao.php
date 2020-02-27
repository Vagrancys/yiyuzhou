<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Taobao extends Commons
{
    //作品升级记录展示
    public function taobao_list(){
        $record=Db::table('taobao')
            ->alias('l')
            ->join('manga m','m.manga_id=l.taobao_manga')
            ->paginate(20);
        $page=$record->render();
        $this->assign('page',$page);
        $this->assign('record',$record);
        return $this->fetch();
    }

    //作品升级记录添加
    public function taobao_add(){
        return $this->fetch();
    }

    //作品升级记录保存
    public function taobao_form(){
        $data=input('post.');
        $data['taobao_time']=time();
        $form =Db::table('taobao')->insert($data);
        if($form) {
            return $this->success("添加成功", "taobao_list");
        } else {
            return $this->error("添加失败", "taobao_list");
        }
    }

    //作品升级修改数据
    public function update_taobao(){
        $data=input('post.');
        $data['taobao_time']=time();
        $form =  Db::table('taobao')->where('taobao_id',$data['taobao_id'])->update($data);
        if($form) {
            return $this->success("修改成功", "taobao_list");
        } else {
            return $this->success("修改失败", "taobao_list");
        }
    }

    //作品修改提取数据
    public function taobao_update(){
        $data=input('get.');
        $form=Db::table('taobao')->where('taobao_id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    //作品升级删除
    public function taobao_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('taobao')->where('taobao_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","taobao_list");
            }else{
                return $this->error("批量删除失败","taobao_list");
            }
        }
        $form=Db::table('taobao')->where('taobao_id',$data['id'])->delete();
        if($form){
            return json('1');
        }else{
            return json('0');
        }
    }
}