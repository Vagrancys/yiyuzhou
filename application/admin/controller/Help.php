<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Help extends Commons
{
    public function help_classify(){
        $data=Db::table('help_classify')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function help_content(){
        $data=Db::table('help_content')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function help_classify_add(){
        $li=Db::table('help_classify')->where('helpUid',0)->select();
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function help_content_add(){
        $li=Db::table('help_classify')->select();
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function help_classify_edit(){
        $data=input('get.');
        $data=Db::table('help_classify')->where('id',$data['id'])->find();
        $li=Db::table('help_classify')->where('helpUid',0)->select();
        $this->assign('li',$li);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function help_content_edit(){
        $data=input('get.');
        $data=Db::table('help_content')->where('id',$data['id'])->find();
        $data['text']=html_entity_decode($data['text']);
        $li=Db::table('help_classify')->select();
        $this->assign('li',$li);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function help_classify_form(){
        $data=input('post.');
        if(!empty($data)){
            if(array_key_exists('id',$data)){
                var_dump($data);
                $array=array(
                    'id','helpUid','name'
                );
                for($i=0;$i<count($array);$i++){
                    $da[$array[$i]]=$this->infusion($array[$i],$data);
                }
                $form=Db::table('help_classify')->where('id',$data['id'])->update($da);
                if($form){
                    return $this->success('修改帮助分类成功！','help_classify');
                }else{
                    return $this->error('修改帮助分类失败！','help_classify');
                }
            }else{
                $array=array(
                    'helpUid','name'
                );
                for($i=0;$i<count($array);$i++){
                    $da[$array[$i]]=$this->infusion($array[$i],$data);
                }
                $form=Db::table('help_classify')->insert($da);
                if($form){
                    return $this->success('添加帮助分类成功！','help_classify');
                }else{
                    return $this->error('添加帮助分类失败！','help_classify');
                }
            }

        }else{
            return $this->error('添加帮助分类失败！','help_classify');
        }

    }

    public function help_content_form(){
        $data=input('post.');
        if(!empty($data)){
            if(array_key_exists('id',$data)){
                var_dump($data);
                $array=array(
                    'id','classifyId','title','text'
                );
                for($i=0;$i<count($array);$i++){
                    $da[$array[$i]]=$this->infusion($array[$i],$data);
                }
                $form=Db::table('help_content')->where('id',$data['id'])->update($da);
                if($form){
                    return $this->success('修改帮助内容成功！','help_content');
                }else{
                    return $this->error('修改帮助内容失败！','help_content');
                }
            }else{
                $array=array(
                    'classifyId','title','text'
                );
                for($i=0;$i<count($array);$i++){
                    $da[$array[$i]]=$this->infusion($array[$i],$data);
                }
                $form=Db::table('help_content')->insert($da);
                if($form){
                    return $this->success('添加帮助内容成功！','help_content');
                }else{
                    return $this->error('添加帮助内容失败！','help_content');
                }
            }
        }else{
            return $this->error('添加帮助内容失败！','help_contemt');
        }

    }

    public function help_classify_del(){
        $data=input('post.');
        $help=Db::table('help_classify')->where('id',$data['id'])->delete();
        if($help){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function help_content_del(){
        $data=input('post.');
        $help=Db::table('help_content')->where('id',$data['id'])->delete();
        if($help){
            return json(1);
        }else{
            return json(0);
        }
    }
}