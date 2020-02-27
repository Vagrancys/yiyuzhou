<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Url extends Comment
{
    public function url(){
        $data=input();
        $url=Db::table('manga_url')->where('manga_url_id',$data[0])->find();
        $this->assign('url',$url);
        return $this->fetch();
    }

}