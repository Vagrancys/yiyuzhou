<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Auth;
use think\Request;
class Common extends Controller
{
    /*防止有人私自进入敏感页面*/
    public function Sensitive(){
        return $this->error("该页面您无权查看",'login');
    }

    /*防xss注入*/
    public function infusion($name,$data,$static='string'){
        if($static == 'number'){
            if(array_key_exists($name,$data)){
                if(is_numeric($data[$name])){
                    if(!$data[$name] == Session::get('users')){
                        var_dump('3');
                        return $this->error("你的操作有误！","index");
                        exit;
                    }else{
                        return $data[$name];
                    }
                }else{
                    return $this->error("你的操作有误！","index");
                    exit;
                }
            }else{
                return $this->error("你的操作有误！","index");
                exit;
            }
        }elseif($static == 'string'){
            if(array_key_exists($name,$data)){
                return $form[$name]=htmlspecialchars($data[$name]);
            }else{
                return $this->error("你的操作有误！","index");
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


}
