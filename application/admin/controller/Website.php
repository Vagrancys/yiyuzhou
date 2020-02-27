<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;

Class Website extends Commons
{
    /*全局公有函数*/
    public function public_list($table="",$array=""){
        $public=Db::table($table)->where('id',1)->find();
        if(!$public){
            for($i=0;$i<count($array);$i++){
                $public[$array[$i]]='请设置';
            }
        }
        $this->assign('public',$public);
        $this->token();
    }

    public function public_form($data,$table,$array,$url,$text){
        if(empty($data)){
            $this->Sensitive();
        }else{
            if(!empty($data['token'])){
                $this->verify_token($data['token'],$url);
            }else{
                return $this->error('您的操作有误！',$url,1);
            }
            for($i=0;$i<count($array);$i++){
                $form[$array[$i]]=$this->infusion($array[$i],$data);
            }
        }
        if(Db::table($table)->where('id',$form['id'])->find()){
            $site=Db::table($table)->where('id',$form['id'])->update($form);
        }else{
            $site=Db::table($table)->insert($form);
        }

        if($site){
            $tet="修改".$text."成功！";
            return $this->success($tet,$url);
        }else{
            $tet="修改".$text."失败";
            return $this->error($tet,$url);
        }
    }


    /*全局中的各个子块*/
    public function site_list(){
        $array=array(
            'id','bb','site','url','email','icp','statcode','boardlicensed'
        );
        $this->public_list("web_site",$array);
        return $this->fetch();
    }

    public function site_form(){
        $data=input('post.');
        $array=array(
            'id','bb','site','url','email','icp','statcode'
        );
        $url="site_list";
        $text="站点信息";
        $this->public_form($data,"web_site",$array,$url,$text);
    }

    public function register_list(){
        $array=array(
            'id','static'
        );
        $this->public_list("overall_register",$array);
        return $this->fetch();
    }

    public function register_form(){
        $data=input('post.');
        $array=array(
            'id','static'
        );
        $url="register_list";
        $text="注册信息";
        $this->public_form($data,"overall_register",$array,$url,$text);
    }

    public function optimize_list(){
        $array=array(
            'id','lazyload','sessionclose'
        );
        $this->public_list("overall_optimize",$array);
        return $this->fetch();
    }

    public function optimize_form(){
        $data=input('post.');
        $array=array(
            'id','lazyload','sessionclose'
        );
        $url="optimize_list";
        $text="优化管理";
        $this->public_form($data,"overall_optimize",$array,$url,$text);
    }

    public function seo_list(){
        $array=array(
            'id','portal','title','keyword'
        );
        $this->public_list("overall_seo",$array);
        return $this->fetch();
    }

    public function seo_form(){
        $data=input('post.');
        $array=array(
            'id','portal','title','keyword'
        );
        $url="seo_list";
        $text="seo信息";
        $this->public_form($data,"overall_seo",$array,$url,$text);
    }

    public function domain_list(){
        $array=array(
            'id','second'
        );
        $this->public_list("overall_domain",$array);
        return $this->fetch();
    }

    public function domain_form(){
        $data=input('post.');
        $array=array(
            'id','second'
        );
        $url="domain_list";
        $text="域名信息";
        $this->public_form($data,"overall_domain",$array,$url,$text);
    }

    public function users_list(){
        $array=array(
            'id','prompt'
        );
        $this->public_list("overall_user",$array);
        return $this->fetch();
    }

    public function users_form(){
        $data=input('post.');
        $array=array(
            'id','prompt'
        );
        $url="user_list";
        $text="用户权限";
        $this->public_form($data,"overall_user",$array,$url,$text);
    }

    public function search_s_list(){
        $array=array(
            'id','search'
        );
        $this->public_list("overall_search",$array);
        return $this->fetch();
    }

    public function search_s_form(){
        $data=input('post.');
        $array=array(
            'id','search'
        );
        $url="search_s_list";
        $text="搜索设置";
        $this->public_form($data,"overall_search",$array,$url,$text);
    }

    public function upload_list(){
        $array=array(
            'id','image','video','file'
        );
        $this->public_list("overall_upload",$array);
        return $this->fetch();
    }

    public function upload_form(){
        $data=input('post.');
        $file="./static/index/lib/ueditor/php/config.json";
        if(is_file($file)){
            $files=file_get_contents($file);
            $files=json_decode($files,true);
            $arr=array( (int)$data['image'], (int)$data['video'], (int)$data['file']);
            $files['imageMaxSize']=$arr[0];
            $files['videoMaxSize']=$arr[1];
            $files['fileMaxSize']=$arr[2];
            file_put_contents($file,json_encode($files));
        }
        $array=array(
            'id','image','video','file'
        );
        $url="upload_list";
        $text="上传设置";
        $this->public_form($data,"overall_upload",$array,$url,$text);
    }

    public function email_list(){
        $array=array(
            'id','formname','form','user','password','address','title'
        );
        $this->public_list("overall_email",$array);
        return $this->fetch();
    }

    public function email_form(){
        $data=input('post.');
        $array=array(
            'id','formname','form','user','password','address','title'
        );
        $url="email_list";
        $text="邮箱设置";
        $this->public_form($data,"overall_email",$array,$url,$text);
    }
}
