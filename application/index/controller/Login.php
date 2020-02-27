<?php
namespace app\index\controller;
use think\Cache;
use think\Controller;
use think\Cookie;
use think\Db;
use think\Loader;
use think\Request;
use think\Session;
use think\Validate;
class Login extends Comment
{
    public function login()
    {
        $this->nav_login();
        $this->header_nav();
        return $this->fetch();
    }

    public function register()
    {
        $this->nav_login();
        $this->header_nav();
        return $this->fetch();
    }

    //处理1级 注册处理
    public function login_up()
    {
        $data =input('post.');
        if(!empty($data)) {
            if ($data['password'] == $data['regPassword']) {
                $data['password'] = md5($data['password']);
                if (array_key_exists('name', $data)) {
                    $da = array(
                        'name' => $data['name'],
                        'password' => $data['password'],
                        'email' => $data['email']
                    );
                    $validate = Loader::validate('User');
                    if (!$validate->scene('name')->check($da)) {
                        $dad['msg'] = $validate->getError();
                        return json($dad);
                    }
                    if (!Db::table('member')->where('name', $da['name'])->find() && !Db::table('member')->where('email', $da['email'])->find())
                    {
                        $da['time'] = time();
                        $Key = 'cy';
                        $da['code'] = substr(md5((substr($da['email'], 0, 4)) . $Key), 0, 8);
                        $form = Db::table('member')->insert($da);
                        $id=Db::table('member')->getLastInsID();
                        Db::table('droit_list')->insert(array('droit_list_name'=>$id));
                        Db::table('manga_collect_list')->insert(array('manga_collect_list_uid'=>$id,'manga_collect_list_time'=>time()));
                        if ($form) {
                            $da['msg'] = "";
                            return json($da);
                        } else {
                            $da['msg'] = "注册失败！";
                            return json($da);
                        }
                    }
                    else {
                        $da['msg'] = '不要尝试错误！';
                        return json($da);
                    }
                }
            }else{
                $dad['msg'] = '密码不一致';
                return json($dad);
            }
        }
    }

    //处理1级 邮箱激活
    public function email_activate(){
        $data=input('get.');
        $array=array(
            'id','code'
        );
        for($i=0;$i<count($array);$i++){
            if($array[$i] == 'id'){
                $da[$array[$i]]=$this->infusion($array[$i],$data);
            }else{
                $da[$array[$i]]=$this->infusion($array[$i],$data);
            }
        }
        $user=Db::table('member')->where('id',$da['id'])->find();
        if($user['code']==$da['code']&&$user['activation']==0){
            $user['activation']=1;
            Db::table('member')->where('id',$user['id'])->update($user);
            return $this->success("邮箱激活成功！",'login');
        }else{
            return $this->error("邮箱激活失败！",'login');
        }
    }

    public function login_upd()
    {
        $data= input('post.');
        if(!empty($data)){
            if (filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
                $da = array(
                    'email' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('email')->check($da)){
                  $dad['msg']=$validate->getError();
                    return json($dad);
                }
            } elseif ($this->isMobile($data['username'])){
                $da = array(
                    'phone' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('phone')->check($da)){
                    $dad['msg']=$validate->getError();
                    return json($dad);
                }
            } else{
                $da = array(
                    'name' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('name')->check($da)){
                    $dad['msg']=$validate->getError();
                    return json($dad);
                }
            }
        }else{
           $dad['msg']='您输入了空值';
            return json($dad);
        }
        $da['password'] = md5($da['password']);
        $request=Request::instance();
        $ip=$request->ip();
        $locking=Db::table('admin_locking')->where('ip',$ip)->find();
        if($locking['errorlogin_time']>time()&&$locking['error_static']==0){
            $dad['msg'] = '账号已被锁定，请半小时后再试！';
            return json($dad);
        }
        $form = Db::table('member')->where($da)->find();
        if ($form&&($form['status']==1)) {
            $where=array(
                'ip'=>$ip,
                'errorlogin_time'=>time(),
                'error_count'=>0,
                'error_static'=>1
            );
            if($locking){
                Db::table('admin_locking')->where('ip',$ip)->update($where);
            }else{
                Db::table('admin_locking')->where('ip',$ip)->insert($where);
            }
            Session::set('users', $form['id']);
            if(empty($form['name'])){
                Session::get('index_name',$form['email']);
            }else{
                Session::set('index_name', $form['name']);
            }
            if ((array_key_exists('check',$data))&&$data['check'] == 'true') {
                $key = "cy";
                $Value = serialize($form['id']);
                $Str = md5($Value . $key);
                Cookie::set('Login',$Str.$Value,60*60*24*7);
            }
            $dad['flag'] = '1';
            return json($dad);
        } else {
            if($locking=Db::table('admin_locking')->where('ip',$ip)->find()){
                if($locking['error_count']>4){
                    $locking['errorlogin_time']=time()+30*60*60;
                    $locking['error_count']=0;
                    $locking['error_static']=0;
                    Db::table('admin_locking')->where('ip',$ip)->update($locking);
                    $dad['msg'] = '登录失败5次！账号已锁定30分钟！';
                    return json($dad);
                }else{
                    $locking['error_count']=$locking['error_count']+1;
                    $locking['error_static']=0;
                    Db::table('admin_locking')->where('ip',$ip)->update($locking);
                    $dad['msg'] = '登录失败'.$locking['error_count'].'次！';
                    return json($dad);
                }
            }else{
                $where=array(
                    'ip'=>$ip,
                    'errorlogin_time'=>"",
                    'error_count'=>1,
                    'error_static'=>0
                );
                Db::table('admin_locking')->where('ip',$ip)->insert($where);
                $dad['msg'] = '登录失败'.$where['error_count'].'次！';
                return json($dad);
            }

        }

    }

    //处理1级 用户注销
    public function login_quit()
{
    Session::delete('index_name', null);
    Session::delete('users', null);
    if(Cookie::has('Login')){
        Cookie::delete('Login');
    }
    $data = 1;
    return json($data);
}

    //处理1级 判断用户名是否重复
    public function register_user()
    {
        $data = input('post.');
        if(empty($data)){
            $da = "非法输入！";
            return json($da);
        }
        $form = Db::table('member')->where('name', $data['mobile'])->find();
        if ($form) {
            $da = "用户名已经使用！";
            return json($da);
        }
    }

    //处理1级 判断邮箱是否重复
    public function register_email()
    {
        $data = input('post.');
        if(empty($data)){
            $da = "非法输入！";
            return json($da);
        }
        $form = Db::table('member')->where('email', $data['email'])->find();
        if ($form) {
            $da['msg'] = "邮箱已经使用！";
            $da['flag'] = false;
            return json($da);
        }
    }

    //处理1级 快捷登录
    public function login_pass(){
        $data= input('post.');
        if(!empty($data)){
            if (filter_var($data['username'], FILTER_VALIDATE_EMAIL)) {
                $da = array(
                    'email' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('email')->check($da)){
                    $dad['msg']=$validate->getError();
                    return json($dad);
                }
                $name['email']=$da['email'];
            } else{
                $da = array(
                    'Name' => $data['username'],
                    'password' => $data['password'],
                );
                $validate=Loader::validate('User');
                if(!$validate->scene('Name')->check($da)){
                    $dad['msg']=$validate->getError();
                    return json($dad);
                }
                $name['name']=$da['Name'];
            }
        }else{

            $dad['msg']='您输入了空值';
            return json($dad);
        }
        $user=Db::table('member')->where($name)->find();
        if(!$user){
            $dad['flag'] = 0;
            $dad['msg'] = '该帐号未使用';
            return json($dad);
        }
        $name['password'] = md5($da['password']);
        $request=Request::instance();
        $ip=$request->ip();
        $locking=Db::table('admin_locking')->where('ip',$ip)->find();
        if($locking['errorlogin_time']>time()&&$locking['error_static']==0){
            $dad['flag'] = 0;
            $dad['msg'] = '账号已被锁定，请半小时后再试！';
            return json($dad);
        }
        $form = Db::table('member')->where($name)->find();
        if ($form&&($form['status']==1)) {
            $where=array(
                'ip'=>$ip,
                'errorlogin_time'=>time(),
                'error_count'=>0,
                'error_static'=>1
            );
            if($locking){
                Db::table('admin_locking')->where('ip',$ip)->update($where);
            }else{
                Db::table('admin_locking')->where('ip',$ip)->insert($where);
            }
            Session::set('users', $form['id']);
            if(empty($form['name'])){
                Session::get('index_name',$form['email']);
            }else{
                Session::set('index_name', $form['name']);
            }
            if ((array_key_exists('check',$data))&&$data['check'] == 'true') {
                $key = "cy";
                $Value = serialize($form['id']);
                $Str = md5($Value . $key);
                Cookie::set('Login',$Str.$Value,60*60*24*7);
            }
            $dad['flag'] = 1;
            return json($dad);
        } else {
            if($locking=Db::table('admin_locking')->where('ip',$ip)->find()){
                if($locking['error_count']>4){
                    $locking['errorlogin_time']=time()+30*60*60;
                    $locking['error_count']=0;
                    $locking['error_static']=0;
                    Db::table('admin_locking')->where('ip',$ip)->update($locking);
                    $dad['msg'] = '登录失败5次！账号已锁定30分钟！';
                    return json($dad);
                }else{
                    $locking['error_count']=$locking['error_count']+1;
                    $locking['error_static']=0;
                    Db::table('admin_locking')->where('ip',$ip)->update($locking);
                    $dad['flag'] = 0;
                    $dad['msg'] = '密码错误！';
                    return json($dad);
                }
            }else{
                $where=array(
                    'ip'=>$ip,
                    'errorlogin_time'=>"",
                    'error_count'=>1,
                    'error_static'=>0
                );
                Db::table('admin_locking')->where('ip',$ip)->insert($where);
                $dad['flag'] = 0;
                $dad['msg'] = '密码错误！';
                return json($dad);
            }

        }
    }

    public function retrieve(){
        $this->header_nav();
        $this->nav_login();
        return $this->fetch();
    }

    public function retrieve_email(){
        $data=input('post.');
        $email=Db::table('member')->where('email',$data['text'])->find();
        if($email){
            $Key='cy';
            $data['code']=substr(md5((substr($email['email'],0,4)).$Key),0,8);
            $text="密码找回:".$email['email']."
这封信是由 异宇宙动漫 www.yiyuzhou.com 发送的。
您收到这封邮件，是由于在 异宇宙动漫 www.yiyuzhou.com 进行了密码修改。如果您并没有访问过 次元，或没有进行上述操作，请忽 略这封邮件。您不需要退订或进行其他进一步的操作。
您只需点击下面的链接即可修改密码：";
            $url="<a href='http://www.yiyuzhou.com/password_activate.html?id=".$email['id']."&code=".$data['code']."' target='_blank'>http://www.yiyuzhou.com/password_activate.html?id=".$email['id']."&code=".$data['code']."</a>";
            $emails= $this->sendEmail($text,$email['email'],$url);
            if($emails==1){
                $where=array(
                    'member_id'=>$email['id'],
                    'member_time'=>time()+24*60*60*60,
                    'member_static'=>1,
                    'member_code'=>$data['code']
                );
                if(Db::table('member_password')->where('member_id',$email['id'])->find()){
                    Db::table('member_password')->where('member_id',$email['id'])->update($where);
                }else{
                    Db::table('member_password')->insert($where);
                }
                $text['msg']=1;
                $text['text']="邮箱发送成功！";
                return json($text);
            }else{
                $text['msg']=0;
                $text['text']="系统错误！";
                return json($text);
            }
        }else{
            $text['msg']=0;
            $text['text']="该邮箱未使用！";
            return json($text);
        }
    }
}