<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
Class Content extends Commons
{
     public function  terms_list(){
         $text="./static/index/filter.tet";
         if(is_file($text)){
             $terms=file_get_contents($text);
         }
         $this->assign('terms',$terms);
         return $this->fetch();
     }

    public function terms_form(){
        $data=input('post.');
        $text="./static/index/filter.tet";
        if(is_file($text)){
            $terms=file_put_contents($text,$data['terms']);
        }
        if($terms){
            return $this->success('敏感词添加成功','terms_list');
        }else{
            return $this->error('敏感词添加失败','terms_list');
        }
    }

    public function report_list(){
        $report=Db::table('web_report')->paginate(10);
        $page=$report->render();
        $this->assign('page',$page);
        $this->assign('data',$report);
        $number=Db::table('web_report')->count();
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function report_static(){
        $data=input('post.');
        $report=Db::table('web_report')->where('id',$data['id'])->find();
        if($report['report_video_collect']==""){
            $form=Db::table('news_comment')->where('comment_id',$report['report_video_id'])->setField('news_static',0);
        }else{
            $form=Db::table('video_collect')->where(array('Uid'=>$report['report_video'],'collectionId'=>$report['report_video_collect']))->setField('Static',0);
        }
        if($form){
            Db::table('web_report')->where('id',$data['id'])->delete();
            $report['report_tet']="举报处理完";
            $report['report_static']=1;
            Db::table('web_report_del')->insert($report);
            return json(1);
        }else{
            return json(0);
        }
    }

    public function report_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('web_report')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('举报删除成功','report_list');
            }else{
                return $this->error('举报删除失败','report_list');
            }
        }
        $form=Db::table('web_report')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function handle_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $form=Db::table('web_report_del')->where('id',$item['item'][$i])->delete();
            }
            if($form){
                return $this->success('举报删除成功','report_list');
            }else{
                return $this->error('举报删除失败','report_list');
            }
        }
        $form=Db::table('web_report_del')->where('id',$data['id'])->delete();
        if($form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function report_handle(){
        $report=Db::table('web_report_del')->paginate(10);
        $page=$report->render();
        $this->assign('page',$page);
        $this->assign('data',$report);
        $number=Db::table('web_report_del')->count();
        $this->assign('number',$number);
        return $this->fetch();
    }

}