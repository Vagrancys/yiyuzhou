<?php

namespace app\admin\controller;

use think\Controller;

use think\Db;

use think\Session;

use think\Request;

class Commons extends Controller

{

    /*自运行函数*/

    public function _initialize(){

        $request=Request::instance();

        define('MODULE_NAME',$request->module());

        define('CONTROLLER_NAME',$request->controller());

        define('ACTION_NAME',$request->action());

        $uid =Session::has('user');

        if(!$uid){

            $this->error('您还没有登陆','index_login');

        }

        $id =Session::get('user');

        if(!($id==1)){

            $auth=$this->auth_check(CONTROLLER_NAME);

            if($auth){

                return $this->error("你没有权限操作！",'admin');

            }

        }



    }



    /*防止有人私自进入敏感页面*/

    public function Sensitive(){

        return $this->error("该页面您无权查看",'index_login');

    }



    /*防xss注入*/

    public function infusion($name,$data,$static='string'){

        if($static == 'number'){

            if(array_key_exists($name,$data)){

                if(is_numeric($data[$name])){

                    if(!$data[$name] == Session::get('users')){

                        return $this->error("你的操作有误！","welcome");

                        exit;

                    }else{

                        return $data[$name];

                    }

                }else{

                    return $this->error("你的操作有误！","welcome");

                    exit;

                }

            }else{

                return $this->error("你的操作有误！","welcome");

                exit;

            }

        }elseif($static == 'string'){

            if(array_key_exists($name,$data)){

                return $form[$name]=htmlspecialchars($data[$name]);

            }else{

                return $this->error("你的操作有误！","welcome");

                exit;

            }

        }

    }



    /*表单防多次提交*/

    public function token($key='cy'){

        $rand=rand(1,100);

        $Value=$key.$rand;

        $token=md5($Value);

        Session::set('token',$token);

        $this->assign('token',$token);

    }



    /*表单验证token*/

    public function verify_token($token='',$url=''){

        if($token == Session::get('token')) {

            Session::delete('token');

        }else{

            Session::delete('token');

            return $this->error("您的操作有误！",$url);

        }

    }



    /*auth的树状结构*/

    public function auth_tree(){

        $menu = Db::table('auth_power')->where('level', '一级')->order(['id' => 'asc'])->select();

        for ($i = 0; $i < count($menu); $i++) {

            $menu[$i]['two'] = Db::table('auth_power')->where(array('level' => '二级', 'parent_id' => $menu[$i]['id']))->select();

            for ($j = 0; $j < count($menu[$i]['two']); $j++) {

                $menu[$i]['two'][$j]['where'] = Db::table('auth_power')->where(array('level' => '三级', 'parent_id' => $menu[$i]['two'][$j]['id']))->select();

            }

        }

        $this->assign('menu', $menu);

    }



    /*auth的树状结构*/

    public function front_auth_tree(){

        $menu = Db::table('front_auth_power')->where('level', '一级')->order(['id' => 'asc'])->select();

        for ($i = 0; $i < count($menu); $i++) {

            $menu[$i]['two'] = Db::table('front_auth_power')->where(array('level' => '二级', 'parent_id' => $menu[$i]['id']))->select();

            for ($j = 0; $j < count($menu[$i]['two']); $j++) {

                $menu[$i]['two'][$j]['where'] = Db::table('front_auth_power')->where(array('level' => '三级', 'parent_id' => $menu[$i]['two'][$j]['id']))->select();

            }

        }

        $this->assign('menu', $menu);

    }



    public function auth_check($url=""){

        $id =Session::get('user');

        $group=Db::table('auth_group_access')->where('uid',$id)->find();

        $auth=Db::table('auth_group')->where('id',$group['group_id'])->find();

        $power=explode(",",$auth['rules']);

        for($z=0;$z<count($power);$z++) {

            $power[$z]= Db::table('auth_power')->where('id', $power[$z])->find();

        }

        for($j=0;$j<count($power);$j++){

            if($power[$j]['name']==$url){

                return true;

            }

        }



    }

}

