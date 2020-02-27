<?php
namespace app\admin\controller;

use think\Controller;
use think\Db;
use think\Request;

class Sql extends Commons{
    public function mysql(){
        $sql=Db::query("SHOW TABLE STATUS");
        $this->assign('sql',$sql);
        return $this->fetch();
    }
}