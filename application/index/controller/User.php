<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class User extends Comment
{
    public function users()
    {
        $this->header_nav();
        $this->nav_login();
        $data['id']=input('uid');
        $j=2;
        for($i=0;$i<$j;$i++){
            if($i==0){
                $u=5;
            }else{
                $u=7;
            }
            Db::table('manga_collect_list')->insert(array('manga_collect_list_uid'=>$u,'manga_collect_list_time'=>time()));
        }
        $data['user']=Session::get('users');
        if(!$this->user_error($data)){
            return $this->fetch('login/login');
        }
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        $da=array(
            'v.user_id'=>$data['id'],
            'v.manga_static'=>1
        );
        $video=Db::table('manga')->alias('v')
            ->join('level_date d','d.level_date_manga=v.manga_id')
            ->where($da)->order('d.level_date_click desc')->limit(3)->select();
        for($i=0;$i<count($video);$i++){
            if($video[$i]['level_date_click']>10000&&$video[$i]['level_date_click']<100000000){
                $video[$i]['level_date_click']=intval($video[$i]['level_date_click']/10000)."万";
            }elseif($video[$i]['level_date_click']>100000000){
                $video[$i]['level_date_click']=intval($video[$i]['level_date_click']/100000000)."亿";
            }
            $video[$i]['manga_time']=$this->user_time($video[$i]['manga_time']);
        }
        $this->assign('vi',$video);
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $this->assign('form',$form);
        $detail=Db::table('user_channel')->where('user_id',$data['id'])->select();
        for($i=0;$i<count($detail);$i++){
            $detail[$i]['details']=Db::table('user_detail')
                ->alias('d')
                ->join('member m','m.id=d.user_id')
                ->join('manga v','v.manga_id=d.data_id')
                ->where('d.channel_id',$detail[$i]['channel_id'])->limit(5)->select();
            for($z=0;$z<count($detail[$i]['details']);$z++){
                $detail[$i]['details'][$z]['Time']=$this->user_time($detail[$i]['details'][$z]['Time']);
            }
            $num=count($detail[$i]['details']);
            if(!$num==0){
                for($j=0;$j<$num;$j++){
                    $detail[$i]['details'][$j]['Text']=$this->cut($detail[$i]['details'][$j]['Text'],25);
                }

            }
            $detail[$i]['num']=$num;
        }
        $static="users";
        $this->assign('static',$static);
        $this->assign('detail',$detail);
        return $this->fetch();
    }

    public function browse()
    {
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $this->user_fans($user);
        $this->user_number($user);
        $data['user']=Session::get('users');
        if(empty($user)){
            return $this->fetch('login/login');
        }else{
            $data['id']=$user;
            $time=time()-30*24*60*60;
            Db::table('user_browse')->where('user_id',$user)->where('user_Time','>',$time)->delete();
        }
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $browse=Db::table('user_browse')
            ->alias('b') ->where('b.user_id',$data['id'])
            ->join('manga v','v.manga_id=b.data_id')
            ->where('v.manga_static',1)
            ->paginate(10);
        $browse->toArray();
        $page=$browse->render();
        $this->assign('page',$page);
        $this->assign('browse',$browse);
        $this->assign('form',$form);
        $static="browse";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function fans()
    {
        $this->header_nav();
        $this->nav_login();
        $data=input();
        $array=explode('-',$data['id']);
        $data['id']=$array[0];
        if(!empty($array[1])){
            $data['follow']=$array[1];
        }

        $data['user']=Session::get('users');
        if(!$this->user_error($data)){
            return $this->fetch('login/login');
        }
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $this->assign('form',$form);
        if(!empty($data)){
            if(array_key_exists('follow',$data)){
                $fans=Db::table('user_fans')
                    ->alias('b') ->where('b.fans_id',$data['id'])
                    ->join('member m','b.user_id=m.id')
                    ->paginate(10,false,[
                        'query'=>array('follow'=>$data['follow'])
                    ]);
                $fans->toArray();
                foreach ($fans as $k=>$v){
                    $fan=Db::table('user_fans')->where('user_id',$v['id'])->where('fans_id',$data['user'])->find();
                    if($fan){
                        $v['fans_static']=1;
                    }else{
                        $v['fans_static']=0;
                    }
                    $fans->offsetSet($k,$v);
                }
                $page=$fans->render();
                $this->assign('page',$page);
                $this->assign('fan',$fans);
                $static=$data['user'];
                $this->assign('static',$static);
                return $this->fetch();
            }else{
                $follow=Db::table('user_fans')
                    ->alias('b') ->where('b.user_id',$data['id'])
                    ->join('member m','b.fans_id=m.id')
                    ->paginate(10);
                $follow->toArray();
                foreach ($follow as $k=>$v){
                    $fan=Db::table('user_fans')->where('user_id',$v['id'])->where('fans_id',$data['user'])->find();
                    if($fan){
                        $v['fans_static']=1;
                    }else{
                        $v['fans_static']=0;
                    }
                    $follow->offsetSet($k,$v);
                }
                $page=$follow->render();
                $this->assign('page',$page);
                $this->assign('follow',$follow);
                $static=$data['user'];
                $this->assign('static',$static);
               return $this->fetch('Follow');
            }
        }
    }

    public function Videos()
    {
        $this->header_nav();
        $this->nav_login();
        $data['id']=input('uid');
        $data['user']=Session::get('users');
        if(!$this->user_error($data)){
            return $this->fetch('login/login');
        }
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $video=Db::table('mange_part')->alias('v')
            ->where('mange_part_static',1)
            ->where(array('mange_part_user'=>$data['id']))->paginate(10);
        $video->toArray();
        foreach($video as $k=>$v){
            $v['Time']=$this->user_time($v['mange_part_time']);
            $video->offsetSet($k,$v);
        }
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('vi',$video);
        $this->assign('form',$form);
        $static="Video";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function theme_list()
    {
        $this->header_nav();
        $this->nav_login();
        $data['id']=input('uid');
        $data['user']=Session::get('users');
        if(!$this->user_error($data)){
            return $this->fetch('login/login');
        }
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $video=Db::table('mange_theme')->alias('v')
            ->join('level_theme_date d','v.mange_theme_id=d.level_date_mange')
            ->where('v.mange_theme_static',1)
            ->where(array('v.mange_theme_user'=>$data['id']))->paginate(10);
        $video->toArray();
        foreach($video as $k=>$v){
            $v['Time']=$this->user_time($v['mange_theme_time']);
            if($v['level_date_click']>10000&&$v['level_date_click']<100000000){
                $v['level_date_click']=intval($v['level_date_click']/10000)."万";
            }elseif($v['level_date_click']>100000000){
                $v['level_date_click']=intval($v['level_date_click']/100000000)."亿";
            }
            $video->offsetSet($k,$v);
        }
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('vi',$video);
        $this->assign('form',$form);
        $static="theme";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function collect(){
        $data['id']=input('id');
        $this->header_nav();
        $this->nav_login();
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $this->assign('form',$form);
        $uid=Session::get('users');
        if($uid&&$data['id']){
            $where=array(
                'manga_collect_list_uid'=>$data['id']
            );
        }else{
            $where=array(
                'manga_collect_list_uid'=>$data['id'],
                'manga_collect_list_static'=>1
            );
        }
        $video=Db::table('manga_collect_list')->where($where)->paginate(20);
        $video->toArray();
        foreach($video as $k=>$v){
            $number=Db::table('manga_collect')->where('manga_collect_list',$v['manga_collect_list_id'])->select();
            if(count($number)==0){
                $v['img']=1;
                $v['number']=0;
            }else{
                $vi=Db::table('manga')->where('manga_id',$number[0]['manga_collect_manga'])->find();
                $v['img']=$vi['img'];
                $v['number']=count($number);
            }
            $video->offsetSet($k,$v);
        }
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('vi',$video);
        $static="collect";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function collect_list(){
        $data=input('get.');
        $this->header_nav();
        $this->nav_login();
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $this->assign('form',$form);
        $collect=Db::table('manga_collect_list')->where('manga_collect_list_id',$data['list'])->find();
        $video=Db::table('manga_collect')->where('manga_collect_list',$data['list'])->paginate(20);
        $video->toArray();
        foreach($video as $k=>$v){
            $list=Db::table('manga')->where('manga_id',$v['manga_collect_manga'])->find();
            $v['title']=$list['title'];
            $v['img']=$list['img'];
            $video->offsetSet($k,$v);
        }
        $this->assign('collect',$collect);
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('vi',$video);
        $static="collect";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function collect_static(){
        $id=Session::get('users');
        $this->nav_login();
        $this->header_nav();
        if(!$id){
            return $this->fetch('login/login');
        }
        $this->user_fans($id);
        $this->user_number($id);
        $form=Db::table('member')->where('id',$id)->find();
        if(!$form['nickname']==''){
            $form['name']=$form['nickname'];
        }
        $vi=Db::table('manga_collect_list')->where('manga_collect_list_uid',$id)->paginate(20);
        $page=$vi->render();
        $this->assign('page',$page);
        $this->assign('vi',$vi);
        $this->assign('form',$form);
        $static="static";
        $this->assign('static',$static);
        return $this->fetch();
    }

    public function collect_static_name(){
        $data=input('post.');
        $user=Session::get('users');
        $form=Db::table('manga_collect_list')->where('manga_collect_list_id',$data['id'])->where('manga_collect_list_uid',$user)->setField('manga_collect_list_name',$data['val']);
        if($form){
            $text['msg']=1;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function collect_static_power(){
        $data=input('post.');
        $user=Session::get('users');
        $form=Db::table('manga_collect_list')->where('manga_collect_list_id',$data['id'])->where('manga_collect_list_uid',$user)->setField('manga_collect_list_static',$data['val']);
        if($form){
            $text['msg']=1;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function collect_static_delete(){
        $data=input('post.');
        $user=Session::get('users');
        $num=Db::table('manga_collect')->where('manga_collect_list',$data['id'])->select();
        $collect=Db::table('manga_collect_list')->where('manga_collect_list_uid',$data['id'])->select();
        if(count($num)==0&&count($collect)>1){
            $form=Db::table('manga_collect_list')->where('manga_collect_list_id',$data['id'])->where('manga_collect_list_uid',$user)->delete();
            if($form){
                $text['msg']=1;
                return json($text);
            }else{
                $text['msg']=0;
                return json($text);
            }
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function collect_list_delete(){
        $data=input('post.');
        $user=Session::get('users');

            $form=Db::table('manga_collect')->where('manga_collect_id',$data['id'])->where('manga_collect_member',$user)->delete();
            if($form){
                $text['msg']=1;
                return json($text);
            }else {
                $text['msg'] = 0;
                return json($text);
            }
    }

    public function search_videos(){
        $this->header_nav();
        $this->nav_login();
        $data=array();
        $data=input('post.');
        if(empty($data)){
            $data=input('get.');
        }
        $form=Db::table('member')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        $static="Video";
        $this->assign('static',$static);
        $this->assign('text','没有找到视频！');
        $this->assign('id',$data['id']);
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        if(empty($data)){
            $data['keyword']='';
        }else{
            if(!array_key_exists('keyword',$data)){
                return $this->fetch('videos_404');
            }
        }
        if($data['keyword'] == ''){
            return $this->fetch('videos_404');
        }else{
            $keyword ='%'.$data['keyword'].'%';
            $video=Db::table('manga')
                ->alias('v')
                ->where('v.user_id',$data['id'])
                ->where('v.title|v.manga_text','like',$keyword)->where('v.manga_static',1)->order('v.manga_time desc')
                ->paginate(10,false,[
                    'query'=>array('keyword'=>$data['keyword'],'id'=>$data['id'])
                ]);
            $number=count($video);
            $video->toArray();
            foreach ($video as$k => $v){
                $n=Db::table('level_date')->where('level_date_manga',$v['manga_id'])->find();
                if($n['level_date_click']>10000&&$n['level_date_click']<100000000){
                    $n['level_date_click']=intval($n['level_date_click']/10000)."万";
                }elseif($n['level_date_click']>100000000){
                    $n['level_date_click']=intval($n['level_date_click']/100000000)."亿";
                }
                $v['level_date_click']=$n['level_date_click'];
                $v['Time']=$this->user_time($v['manga_time']);
                $text1="<em class='keyword'>".$data['keyword']."</em>";
                $v['title']=preg_replace("/".$data['keyword']."/",$text1,$v['title']);
                $video->offsetSet($k,$v);
            }
            $page=$video->render();
            $this->assign('page',$page);
            $this->assign('text',$data['keyword']);
            $this->assign('number',$number);
            if($video){
                $this->assign('vi',$video);
                return $this->fetch('videos');
            }else{
                return $this->fetch('videos_404');
            }
        }
    }

    public function upload_logo(){
        $data=input('post.');
        $images=Db::table('member')->where('id',$data['id'])->find();
        $url="./images/head-images/";
        if(!$images['image']==''){
            $img=$url.$images['image'];
            if(file_exists($img)){
               unlink($img);
            }
        }
        $img=str_replace('data:image/jpeg;base64,',"",$data['img']);
        $img = str_replace(' ', '+', $img);
        $image = base64_decode($img);
        $user=uniqid();
        $users=$user.'.jpeg';
        $id=array(
            'image'=>$users
        );
        Db::table('member')->where('id',$data['id'])->update($id);
        $file =$url.$user . '.jpeg';
        $success = file_put_contents($file, $image);
        if($success){
            return json($users);
        }
    }

    public function user_form(){
        $user=input('post.');
        if(!empty($user)){
            $array = array(
                'nickname' => $user['nickname'],
                'autograph' =>$user['autograph'],
            );
            if($user['id'] == Session::get('users')){
                $data=Db::table('member')->where('id',Session::get('users'))->update($array);
                if($data){
                    return $this->success("修改成功！","core");
                }else{
                    return $this->error("修改失败！",'core');
                }
            }else{
                return $this->error("您的行为很危险！","index");
            }
        }else{
            var_dump('确定要这样');
        }
    }

    public function fans_insert(){
        $id=input('post.');
        $user=Session::get('users');
        if($user==""){
            return json(0);
        }
        $where=array(
            'user_id'=>$id['id'],
            'fans_id'=>$user
        );
        $fans=Db::table('user_fans')->where('user_id',$id['id'])->where('fans_id',$user)->find();
        if(!$fans){
            if(Db::table('member_level_date')->where('member_level_member',$id['id'])->find()){
                Db::table('member_level_date')->where('member_level_member',$id['id'])->setInc('member_level_fans',1);
            }
            $form=Db::table('user_fans')->insert($where);
            if($form){
                return json(1);
            }else{
                return json(0);
            }
        }
    }

    public function fans_del(){
        $id=input('post.');
        $form=Db::table('user_fans')->where(array('user_id'=>$id['id'],'fans_id'=>Session::get('users')))->delete();
        if($form){
            if(Db::table('member_level_date')->where('member_level_member',$id['id'])->find()){
                Db::table('member_level_date')->where('member_level_member',$id['id'])->setDec('member_level_fans',1);
            }
            return json(1);
        }else{
            return json(0);
        }
    }

    public function user_email(){
        $user=Session::get("users");
        $data=Db::table('member')->where('id',$user)->find();
        $Key='cy';
        $data['code']=substr(md5((substr($data['email'],0,4)).$Key),0,8);
        $text=$data['email']."
这封信是由 异宇宙 www.yiyuzhou.com 发送的。

您收到这封邮件，是由于在 异宇宙 www.yiyuzhou.com 进行了密码修改。如果您并没有访问过 次元，或没有进行上述操作，请忽 略这封邮件。您不需要退订或进行其他进一步的操作。
您只需点击下面的链接即可修改密码：";
        $url="<a href='http://www.yiyuzhou.com/password_activate.html?id=".$user."&code=".$data['code']."' target='_blank'>http://www.yiyuzhou.com/email_activate.html?id=".$user."&code=".$data['code']."</a>";
        $email= $this->sendEmail($text,$data['email'],$url);
        if($email==1){
            $where=array(
                'member_id'=>$user,
                'member_time'=>time()+24*60*60*60,
                'member_static'=>1,
                'member_code'=>$data['code']
            );
            if(Db::table('member_password')->where('member_id',$user)->find()){
                Db::table('member_password')->where('member_id',$user)->update($where);
            }else{
                Db::table('member_password')->insert($where);
            }
            $dad=1;
            return json($dad);
        }else{
            $dad=0;
            return json($dad);
        };
    }

    public function password_activate(){
        $data=input('get.');
        $this->nav_login();
        $this->header_nav();
        $user=Db::table('member_password')->where('member_id',$data['id'])->where('member_code',$data['code'])->where('member_static',1)->find();
        if($user['member_time']>time()){
            $this->assign('id',$data['id']);
            return $this->fetch();
        }else{
            abort(404);
        }
    }

    public function password_form(){
        $data=input('post.');
        if($data['password']==$data['passwords']){
            $array=array(
                'password'=>md5($data['password'])
            );
            $form=Db::table('member')->where('id',$data['id'])->update($array);
            if($form){
                Db::table('member_password')->where('member_id',$data['id'])->setField('member_static',0);
                return $this->skip('修改密码成功！','login','首页','success');
            }else{
                return $this->skip('修改密码失败！','login','首页','error');
            }
        }else{
            abort(404);
        }
    }

    public function activation_email(){
        $user=Session::get('users');
        $da=Db::table('member')->where('id',$user)->find();
        $da['time'] = time();
        $Key='cy';
        $da['code']=substr(md5((substr($da['email'],0,4)).$Key),0,8);
        Db::table('member')->where('id',$user)->setField('code',$da['code']);
        if($da['email']==""){
            $dad=0;
            return json($dad);
        }
        $text=$da['email']."，
这封信是由 次元 www.ciyuan.com 发送的。

您收到这封邮件，是由于在 次元 www.ciyuan.com 进行了新用户注册，或用户修改 Email 使用 了这个邮箱地址。如果您并没有访问过 次元，或没有进行上述操作，请忽 略这封邮件。您不需要退订或进行其他进一步的操作。


----------------------------------------------------------------------
帐号激活说明
----------------------------------------------------------------------

如果您是 次元 的新用户，或在修改您的注册 Email 时使用了本地址，我们需 要对您的地址有效性进行验证以避免垃圾邮件或地址被滥用。

您只需点击下面的链接即可激活您的帐号：";
        $url="<a href='http://www.ciyuan.com/email_activate.html?id=".$user."&code=".$da['code']."' target='_blank'>http://www.ciyuan.com/email_activate.html?id=".$user."&code=".$da['code']."</a>";
        $email= $this->sendEmail($text,$da['email'],$url);
        if($email==1){
            $dad=1;
            return json($dad);
        }else{
            $dad=0;
            return json($dad);
        };
    }

    public function email_send(){
        $data=input('post.');
        $form=Db::table('member')->where('email',$data['email'])->find();
        if($form){
            $Key='cy';
            $data['code']=substr(md5((substr($data['email'],0,4)).$Key),0,8);
            $text=$form['email']."
这封信是由 异宇宙 www.yiyuzhou.com 发送的。

您收到这封邮件，是由于在 异宇宙 www.yiyuzhou.com 进行了密码修改。如果您并没有访问过 次元，或没有进行上述操作，请忽 略这封邮件。您不需要退订或进行其他进一步的操作。
您只需点击下面的链接即可修改密码：";
            $url="<a href='http://www.yiyuzhou.com/password_activate.html?id=".$form['id']."&code=".$form['code']."' target='_blank'>http://www.yiyuzhou.com/email_activate.html?id=".$form['id']."&code=".$form['code']."</a>";
            $email= $this->sendEmail($text,$form['email'],$url);
            if($email==1){
                $where=array(
                    'member_id'=>$form['id'],
                    'member_time'=>time()+24*60*60*60,
                    'member_static'=>1,
                    'member_code'=>$data['code']
                );
                if(Db::table('member_password')->where('member_id',$form['id'])->find()){
                    Db::table('member_password')->where('member_id',$form['id'])->update($where);
                }else{
                    Db::table('member_password')->insert($where);
                }
                return $this->skip('请去邮箱验证！','login','首页','success');
            }else{
                return $this->skip('您输入的邮箱无效！','login','首页','error');
            }
        }else{
            return $this->skip('您输入的邮箱无效！','login','首页','error');
        }
    }

    public function level_list(){
        $this->header_nav();
        $this->nav_login();
        $data['id']=input('level_list');
        $data['user']=Session::get('users');
        if(!$this->user_error($data)){
            return $this->fetch('login/login');
        }
        if($data['user']==$data['id']){
            $member['static']=1;
        }else{
            $member['static']=0;
        }
        $this->user_fans($data['id']);
        $this->user_number($data['id']);
        $form=Db::table('member')->where('id',$data['id'])->find();
        $this->assign('form',$form);
        $static="level_list";
        $this->assign('static',$static);
        $level_text="";
        for($i=0;$i<=$form['level'];$i++){
            if($i==0){
                $level_text .="<p class='level-p-radius level-click' data-aid='0'>新手级</p>";
            }else{
                if($i==$form['level']){
                    $name=Db::table('level_class')->where('level_class_id',$form['level'])->find();
                    $level_text .="<p class='level-click level-p-color' data-aid='".$form['level']."'>".$name['level_class_name']."级</p>";
                }else{
                    $name=Db::table('level_class')->where('level_class_id',$i)->find();
                    $level_text .="<p class='level-click' data-aid='".$i."'>".$name['level_class_name']."级</p>";
                }
            }

        }
        $open=Db::table('member_level_date')->where('member_level_member',$data['id'])->find();
        if(!$open){
            $where=array(
                'member_level_member'=>$data['id']
            );
            Db::table('member_level_date')->insert($where);
        }
        $this->assign('level_text',$level_text);
        $level_text="";
        $require=Db::table('member_level_require')->select();
        for($a=0;$a<$form['level'];$a++){
            if($a==0){
                $level_text .="<div class='level-text' id='level".$a."'>
                    <div class='level-text-p level-text-p2'><div class='level-text-button'><button class='level-button'>已开启异宇宙动漫成长之路</button></div></div></div>";
            }
            elseif($a==1){
                $level_text .="<div class='level-text' id='level".$a."'>
                    <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                        <div class='level-text-div'>消费量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                        </div></div>
                    <div class='level-text-button'>
                        <button class='level-button'>已进入新的境界</button>
                    </div>
                </div>";
            }
            elseif($a==2){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                    <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==3){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                    <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                 <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==4){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                    <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==5){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                   <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==6){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                     <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==7){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                     <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                 <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==8){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                     <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                 <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
            elseif($a==9){
                $num=$this->dao_static($form['id'],$a);
                $level_text .="   <div class='level-text' id='level".$a."'>
                     <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$require[$a]['member_l_r_fans']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$require[$a]['member_l_r_money']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$require[$a]['member_l_r_comment']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$require[$a]['member_l_r_article']."'></div>
                            </div></div><div class='level-text-button'><button class='level-button'>已进入新的境界</button></div></div>";
            }
        }
        if($form['level']==10){
            $num=$this->dao_static($form['id'],$a);
            $level_text .="<div class='level-text' id='level10'>
                    <div class='level-text-p level-text-p2'>
                        <div class='level-text-div'>粉丝量：</div>
                        <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                            <div class='layui-progress-bar' lay-percent='".$open['member_level_fans']."/".$open['member_level_fans_up']."'></div>
                        </div></div><div class='level-text-p'>
                            <div class='level-text-div'>消费量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_money']."/".$open['member_level_money_up']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>导师等级：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                               <div class='layui-progress-bar' lay-percent='".$num['n']."/1'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>次级导师数：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                 <div class='layui-progress-bar' lay-percent='".$num['number']."/".$require[$a]['member_l_r_dao_number']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>评论量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_comment']."/".$open['member_level_comment_up']."'></div>
                            </div></div><div class='level-text-p'>
                            <div class='level-text-div'>文章量：</div>
                            <div class='level-text-p1 layui-progress layui-progress-big' lay-showpercent='yes'>
                                <div class='layui-progress-bar' lay-percent='".$open['member_level_article']."/".$open['member_level_article_up']."'></div>
                            </div></div>
                            <div class='level-text-p'> <div class='level-text-button'>
                        <button class='level-button'>请筹备不可思议之宴！</button>
                    </div></div></div>";
        }
        $this->assign('l_text',$level_text);
        $this->assign('member',$member);
        $this->member_level_comment($data['id']);
        if($form['level']>=1&&$form['level']<10){
            return $this->fetch('level_list1');
        }elseif($form['level']==10){
            return $this->fetch('level_list10');
        }else{
            return $this->fetch();
        }
    }

    public function member_level_upd(){
        $user=Session::get('users');
        $level=Db::table('member')->where('id',$user)->find();
        if(!$level){
            return json(0);
        }else{
            $member=Db::table('member')->where('id',$user)->setInc('level',1);
            if($member){
                return json(1);
            }else{
                return json(0);
            }
        }
    }

    public function member_level_up(){
        $user=Session::get('users');
        $re=Db::table('member_level_require')->select();
        $form=Db::table('member')->where('id',$user)->find();
        $level=Db::table('member_level_date')->where('member_level_member',$user)->find();
        if($form['level']==1){
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $up=array(
                        'member_level_fans_up'=>$re[2]['member_l_r_fans'],
                        'member_level_money_up'=>$re[2]['member_l_r_money'],
                        'member_level_level'=>2
                    );
                    $levels=array(
                        'member_level_user'=>$user,
                        'member_level_level'=>1,
                        'member_level_time'=>time()
                    );
                    Db::table('member_levels')->insert($levels);
                    Db::table('member')->where('id',$user)->setInc('level',1);
                    $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',1)->update($up);
                    if($level){
                        return json(1);
                    }else{
                        return json(0);
                    }
                }else{
                    return json(0);
                }
        }
        elseif($form['level']==2){
            $num=$this->dao_static($user,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$level['member_level_money']>=$level['member_level_money_up']
                &&$num['n']==1
                &&$num['number']>=10){
                $up=array(
                    'member_level_fans_up'=>$re[3]['member_l_r_fans'],
                    'member_level_level'=>3
                );
                $levels=array(
                    'member_level_user'=>$user,
                    'member_level_level'=>2,
                    'member_level_time'=>time()
                );
                Db::table('member_levels')->insert($levels);
                Db::table('member')->where('id',$user)->setInc('level',1);
                $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',2)->update($up);
                if($level){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
        elseif($form['level']==3){
            $num=$this->dao_static($user,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']){
                    $up=array(
                        'member_level_fans_up'=>$re[4]['member_l_r_fans'],
                        'member_level_comment_up'=>$re[4]['member_l_r_comment'],
                        'member_level_level'=>4
                    );
                    $levels=array(
                        'member_level_user'=>$user,
                        'member_level_level'=>3,
                        'member_level_time'=>time()
                    );
                    Db::table('member_levels')->insert($levels);
                    Db::table('member')->where('id',$user)->setInc('level',1);
                    $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',3)->update($up);
                    if($level){
                        return json(1);
                    }else{
                        return json(0);
                    }
                }else{
                    return json(0);
                }
        }
        elseif($form['level']==4){
            $num=$this->dao_static($user,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']){
                    $up=array(
                        'member_level_fans_up'=>$re[5]['member_l_r_fans'],
                        'member_level_comment_up'=>$re[5]['member_l_r_comment'],
                        'member_level_article_up'=>$re[5]['member_l_r_article'],
                        'member_level_level'=>5
                    );
                    $levels=array(
                        'member_level_user'=>$user,
                        'member_level_level'=>4,
                        'member_level_time'=>time()
                    );
                    Db::table('member_levels')->insert($levels);
                    Db::table('member')->where('id',$user)->setInc('level',1);
                    $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',4)->update($up);
                    if($level){
                        return json(1);
                    }else{
                        return json(0);
                    }
                }else{
                    return json(0);
                }
        }
        elseif($form['level']==5){
            $num=$this->dao_static($user,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']=1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $up=array(
                        'member_level_fans_up'=>$re[6]['member_l_r_fans'],
                        'member_level_comment_up'=>$re[6]['member_l_r_comment'],
                        'member_level_article_up'=>$re[6]['member_l_r_article'],
                        'member_level_money_up'=>$re[6]['member_l_r_money'],
                        'member_level_level'=>6
                    );
                    $levels=array(
                        'member_level_user'=>$user,
                        'member_level_level'=>5,
                        'member_level_time'=>time()
                    );
                    Db::table('member_levels')->insert($levels);
                    Db::table('member')->where('id',$user)->setInc('level',1);
                    $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',5)->update($up);
                    if($level){
                        return json(1);
                    }else{
                        return json(0);
                    }
                }else{
                    return json(0);
                }
        }
        elseif($form['level']==6){
            $num=$this->dao_static($user,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$num['n']=1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                $up=array(
                    'member_level_fans_up'=>$re[7]['member_l_r_fans'],
                    'member_level_comment_up'=>$re[7]['member_l_r_comment'],
                    'member_level_article_up'=>$re[7]['member_l_r_article'],
                    'member_level_money_up'=>$re[7]['member_l_r_money'],
                    'member_level_level'=>7
                );
                $levels=array(
                    'member_level_user'=>$user,
                    'member_level_level'=>6,
                    'member_level_time'=>time()
                );
                Db::table('member_levels')->insert($levels);
                Db::table('member')->where('id',$user)->setInc('level',1);
                $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',6)->update($up);
                if($level){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
        elseif($form['level']==7){
            $num=$this->dao_static($user,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$num['n']=1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                $up=array(
                    'member_level_fans_up'=>$re[8]['member_l_r_fans'],
                    'member_level_comment_up'=>$re[8]['member_l_r_comment'],
                    'member_level_article_up'=>$re[8]['member_l_r_article'],
                    'member_level_money_up'=>$re[8]['member_l_r_money'],
                    'member_level_level'=>8
                );
                $levels=array(
                    'member_level_user'=>$user,
                    'member_level_level'=>7,
                    'member_level_time'=>time()
                );
                Db::table('member_levels')->insert($levels);
                Db::table('member')->where('id',$user)->setInc('level',1);
                $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',7)->update($up);
                if($level){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
        elseif($form['level']==8){
            $num=$this->dao_static($user,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$num['n']=1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                $up=array(
                    'member_level_fans_up'=>$re[9]['member_l_r_fans'],
                    'member_level_comment_up'=>$re[9]['member_l_r_comment'],
                    'member_level_article_up'=>$re[9]['member_l_r_article'],
                    'member_level_money_up'=>$re[9]['member_l_r_money'],
                    'member_level_level'=>9
                );
                $levels=array(
                    'member_level_user'=>$user,
                    'member_level_level'=>8,
                    'member_level_time'=>time()
                );
                Db::table('member_levels')->insert($levels);
                Db::table('member')->where('id',$user)->setInc('level',1);
                $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',8)->update($up);
                if($level){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
        elseif($form['level']==9){
            $num=$this->dao_static($user,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$num['n']=1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                $up=array(
                    'member_level_fans_up'=>$re[10]['member_l_r_fans'],
                    'member_level_comment_up'=>$re[10]['member_l_r_comment'],
                    'member_level_article_up'=>$re[10]['member_l_r_article'],
                    'member_level_money_up'=>$re[10]['member_l_r_money'],
                    'member_level_level'=>10
                );
                $levels=array(
                    'member_level_user'=>$user,
                    'member_level_level'=>9,
                    'member_level_time'=>time()
                );
                Db::table('member_levels')->insert($levels);
                Db::table('member')->where('id',$user)->setInc('level',1);
                $level=Db::table('member_level_date')->where('member_level_member',$user)->where('member_level_level',9)->update($up);
                if($level){
                    return json(1);
                }else{
                    return json(0);
                }
            }else{
                return json(0);
            }
        }
        else{
            return json(0);
        }
    }

    public function member_level_rise(){
        $user=Session::get('users');
        $member=Db::table('member')->where('id',$user)->find();
        $where=array(
            'member_level_rise_name'=>$user,
            'member_level_rise_level'=>$member['level'],

        );
        $rises=Db::table('rise_level_require')->where('rise_level_id',$member['level'])->find();
        $rise=array(
            'rise_level_date_name'=>$user,
            'rise_level_date_level'=>$member['level'],
            'rise_level_date_fans_up'=>$rises['rise_level_fans'],
            'rise_level_date_money_up'=>$rises['rise_level_money'],
            'rise_level_date_time'=>time(),
            'rise_level_date_static'=>0
        );
        Db::table('rise_level_date')->insert($rise);
        $form=Db::table('member_level_rise')->insert($where);
        if($form){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function user_kidney(){
        $data=input('post.');
        $id=Session::get('users');
        $form=Db::table('member')->where('id',$id)->setField('autograph',$data['text']);
        if($form){
            $text['msg']=1;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }

    public function user_notice(){
        $data=input('post.');
        $id=Session::get('users');
        $form=Db::table('member')->where('id',$id)->setField('notice',$data['text']);
        if($form){
            $text['msg']=1;
            return json($text);
        }else{
            $text['msg']=0;
            return json($text);
        }
    }
}