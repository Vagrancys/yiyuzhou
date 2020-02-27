<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Mange extends Commons
{
    public function admin_theme_copy_list(){
        $data=Db::table('mange_theme_copy')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function mange_theme_copy_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $x= Db::table('mange_theme_copy')->where('mange_theme_id', $item['item'][$j])->delete();
            }
            if($x){
                return $this->success("批量删除成功","admin_theme_copy_list");
            }else{
                return $this->error("批量删除失败","admin_theme_copy_list");
            }
        }

        $x=Db::table('mange_theme_copy')->where('mange_theme_id',$data['id'])->delete();
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function mange_theme_copy_static(){
        $data=input('post.');
        if(!empty($data)){
            $video=Db::table('mange_theme_copy')->where('mange_theme_id',$data['id'])->field('mange_theme_title,mange_theme_page,mange_theme_user,mange_theme_time,mange_theme_img,mange_theme_text,mange_theme_class,mange_theme_line,mange_theme_pass')->find();
            Db::table('mange_theme')->insert($video);
            $id=Db::table('mange_theme')->getLastInsID();
            $level=array(
                'level_date_mange'=>$id,
                'level_date_level'=>1
            );
            $le=array(
                'level_manga'=>$id,
                'level_level'=>1,
                'level_time'=>time()
            );
            Db::table('mange_support_number')->insert(array('mange_support_num_mange'=>$id,'mange_support_num_time'=>time()));
            Db::table('level_theme')->insert($le);
            Db::table('level_theme_date')->insert($level);
            Db::table('mange_theme_copy')->where('mange_theme_id',$data['id'])->delete();
            if($id){
                return json('n');
            }else{
                return json('j');
            };
        }

    }

    public function admin_theme_list(){
        $data=Db::table('mange_theme')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function mange_theme_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $x= Db::table('mange_theme')->where('manga_id', $item['item'][$j])->delete();
            }
            if($x){
                return $this->success("批量删除成功","admin_theme_list");
            }else{
                return $this->error("批量删除失败","admin_theme_list");
            }
        }

        $x=Db::table('mange_theme')->where('manga_id',$data['id'])->delete();
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function mange_theme_static(){
        $item=input('post.');
        if(!empty($item)){
            $x=Db::table('mange_theme')->where('manga_id', $item['id'])->setField('manga_static',$item['static']);
            if($x){
                return json('n');
            }else{
                return json('j');
            }
        }
    }

    public function admin_part_copy_list(){
        $data=Db::table('mange_part_copy')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function mange_part_copy_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $x= Db::table('mange_part_copy')->where('mange_part_id', $item['item'][$j])->delete();
            }
            if($x){
                return $this->success("批量删除成功","admin_part_copy_list");
            }else{
                return $this->error("批量删除失败","admin_part_copy_list");
            }
        }

        $x=Db::table('mange_part_copy')->where('mange_part_id',$data['id'])->delete();
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function mange_part_copy_static(){
        $data=input('post.');
        if(!empty($data)){
            $video=Db::table('mange_part_copy')->where('mange_part_id',$data['id'])->field('mange_part_user,mange_part_name,mange_part_text,mange_part_level,mange_part_img,mange_part_time,mange_part_line')->find();
            Db::table('mange_part')->insert($video);
            $id=Db::table('mange_part')->getLastInsID();
            Db::table('mange_part_copy')->where('mange_part_id',$data['id'])->delete();
            if($id){
                return json('n');
            }else{
                return json('j');
            };
        }

    }

    public function admin_part_list(){
        $data=Db::table('mange_part')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function mange_part_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $x= Db::table('mange_part')->where('mange_part_id', $item['item'][$j])->delete();
            }
            if($x){
                return $this->success("批量删除成功","admin_part_list");
            }else{
                return $this->error("批量删除失败","admin_part_list");
            }
        }

        $x=Db::table('mange_part')->where('mange_part_id',$data['id'])->delete();
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function mange_part_static(){
        $item=input('post.');
        if(!empty($item)){
            $x=Db::table('mange_part')->where('mange_part_id', $item['id'])->setField('mange_part_static',$item['static']);
            if($x){
                return json('n');
            }else{
                return json('j');
            }
        }
    }
}
