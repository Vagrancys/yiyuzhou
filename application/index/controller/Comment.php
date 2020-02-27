<?php
namespace app\index\controller;
use think\Config;
use think\Controller;
use think\Request;
use think\Db;
use think\Session;
use think\Cookie;
use PHPMailer\phpmailer;
class Comment extends Controller{

    public function _initialize()
    {
        $request=Request::instance();
        define('MODULE_NAME',$request->module());
        define('CONTROLLER_NAME',$request->controller());
        define('ACTION_NAME',$request->action());
        if(MODULE_NAME=="index"&&CONTROLLER_NAME=="Video"&&ACTION_NAME=="lists"){
            config('pathinfo_depr','-');
        }
    }

    function Mobile(){
        // 如果有HTTP_X_WAP_PROFILE则一定是移动设备
        if (isset ($_SERVER['HTTP_X_WAP_PROFILE'])){
            return TRUE;
        }
        // 如果via信息含有wap则一定是移动设备,部分服务商会屏蔽该信息
        if (isset ($_SERVER['HTTP_VIA'])){
            return stristr($_SERVER['HTTP_VIA'], "wap") ? TRUE : FALSE;// 找不到为flase,否则为TRUE
        }
        // 判断手机发送的客户端标志,兼容性有待提高
        if (isset ($_SERVER['HTTP_USER_AGENT'])) {
            $clientkeywords = array (
                'mobile',
                'nokia',
                'sony',
                'ericsson',
                'mot',
                'samsung',
                'htc',
                'sgh',
                'lg',
                'sharp',
                'sie-',
                'philips',
                'panasonic',
                'alcatel',
                'lenovo',
                'iphone',
                'ipod',
                'blackberry',
                'meizu',
                'android',
                'netfront',
                'symbian',
                'ucweb',
                'windowsce',
                'palm',
                'operamini',
                'operamobi',
                'openwave',
                'nexusone',
                'cldc',
                'midp',
                'wap'
            );
            // 从HTTP_USER_AGENT中查找手机浏览器的关键字
            if (preg_match("/(" . implode('|', $clientkeywords) . ")/i", strtolower($_SERVER['HTTP_USER_AGENT']))){
                return TRUE;
            }
        }
        if (isset ($_SERVER['HTTP_ACCEPT'])){ // 协议法，因为有可能不准确，放到最后判断
            // 如果只支持wml并且不支持html那一定是移动设备
            // 如果支持wml和html但是wml在html之前则是移动设备
            if ((strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') !== FALSE) && (strpos($_SERVER['HTTP_ACCEPT'], 'text/html') === FALSE || (strpos($_SERVER['HTTP_ACCEPT'], 'vnd.wap.wml') < strpos($_SERVER['HTTP_ACCEPT'], 'text/html')))){
                return TRUE;
            }
        }
        return FALSE;
    }


    /*处理1级 登入整合*/
    public function nav_login(){
        $Key="cy";
        $name['name']=Session::get('index_name');
        $name['id']=Session::get('users');
        if(empty($name['id'])){
            $Value=Cookie::get('Login');
            if(get_magic_quotes_gpc()){
                $Value =stripslashes($Value);
            }
            $Str=substr($Value,0,32);
            $Value =substr($Value,32);
            if(md5($Value.$Key) == $Str){
                $users=unserialize($Value);
                Session::set('users',$users);
                $name['id']=Session::get('users');

            }
        }
        $users=Db::table('member')->where('id',$name['id'])->find();
        $droit=Db::table('droit_list')->where('droit_list_name',$name['id'])->find();
        if($droit){
            if($droit['droit_list_sign']==1){
                $this->sign_in($name["id"]);
            }
        }
        if(empty($users['name'])) {
            $name['name'] = $users['email'];
        }else{
            $name['name'] = $users['name'];
        }
        if(!$users['nickname']==''){
            $name['name']=$users['nickname'];
        }
        if($users['jurisdiction']>110){
            $name['number']=Db::table('manga')
                ->where('manga_static',0)->count();
            $name['static']=1;
            if($users['jurisdiction']==150){
                $name['index_static']=1;
            }else{
                $name['index_static']=0;
            }
        }else{
            $name['index_static']=0;
            $name['number']=Db::table('news_new')->where('user_id',$users['id'])->where('static',0)->count();
            $name['static']=0;
        }
        $num=Db::table('user_new_static')->where('user_new_static_name',$name['id'])->find();
        if($num['user_new_static_number']>0){
            if(Db::table('user_new_system_static')->where('user_new_system_name',$name['id'])->where('user_new_system_static',1)->find()){
                $new['system']=1;
            }else{
                $new['system']=0;
            }
            $new['static']=1;
        }else{
            $new['static']=0;
            $new['system']=0;
        }
        $this->assign('new',$new);
        $name['img']=$users['image'];
        $start_time = strtotime(date('Y-m-d'));
        $sign =Db::table('member_sign')->where(array('sign_uid' => $name['id'], 'sign_time' => array('EGT', $start_time)))->count();
        $web=Db::table('web_site')->where('id',1)->find();
        $seo=Db::table('overall_seo')->where('id',1)->find();
        $this->assign('seo',$seo);
        $this->assign('web',$web);
        $this->assign('sign',$sign);
        $this->assign('name',$name);
        $this->assign('users',$users);
    }

    /*处理1级 判断是否是数字*/
    public function isMobile($mobile) {
        if (!is_numeric($mobile)) {
            return false;
        }
        return preg_match('#^13[\d]{9}$|^14[5,7]{1}\d{8}$|^15[^4]{1}\d{8}$|^17[0,6,7,8]{1}\d{8}$|^18[\d]{9}$#', $mobile) ? true : false;
    }

    /*处理1级 防xss注入*/
    public function infusion($name,$data,$static='string'){
        $this->nav_login();
        $this->header_nav();
        $text="您输入的是敏感信息！";
        $this->assign('text',$text);
        if($static == 'number'){
            if(array_key_exists($name,$data)){
                if(is_numeric($data[$name])){
                    if(!$data[$name] == Session::get('users')){
                        return false;
                        exit;
                    }else{
                        return $data[$name];
                    }
                }else{
                    return false;
                    exit;
                }
            }else{
                return false;
                exit;
            }
        }elseif($static == 'string'){
            if(array_key_exists($name,$data)){
                return $form[$name]=htmlspecialchars($data[$name]);
            }else{
                return false;
                exit;
            }
        }
    }

    /*处理1级 email注册*/
    function sendEmail($desc_content, $email,  $desc_url){
        $emails=Db::table('overall_email')->where('id',1)->find();
        vendor('PHPMailer.phpmailer');
        vendor('PHPMailer.Exception');
        vendor('PHPMailer.SMTP');
        $mail =new phpmailer(true);
        $mail->isSMTP();
        $mail->CharSet = "utf8";
        $mail->Host = 'smtp.163.com';
        $mail->SMTPAuth = true;
        $mail->From    = $emails['form'];
        $mail->Username = $emails['user'];
        $mail->Password = $emails['password'];
        $mail->FromName = $emails['formname'];
        $mail->Port = 465;
        $mail->addAddress($email,$emails['address']);
        $mail->isHTML(true);
        $mail->Subject = $emails['title'];
        $mail->Body =$desc_content."点击下面的链接即可：".$desc_url;
        if(!$mail->send()){// 发送邮件
            return $mail->ErrorInfo;
        }else{
            return 1;
        }
    }

    /*处理1级 nav头部导航*/
    public function header_nav(){
        $array=array(
            'page'=>1,
            'level'=>2
        );
        $nav=Db::table('video_nav')->where($array)->select();
        for($i=0;$i<count($nav);$i++){
            $nav[$i]['one']=Db::table('video_nav')->where('page',1)->where('single',$nav[$i]['id'])->limit(5)->select();
        }
        $this->assign("nav",$nav);
    }

    /*处理1级 videos全站导航*/
    public function videos_nav($page=1,$id=2,$level=1,$single='',$area='',$time="",$nature="",$age=""){
        $nav=Db::table('video_nav')->where('page',$page)->where('level',2)->select();
        $text="";
        $text1="";
        for($i=0;$i<count($nav);$i++){
            if($nav[$i]['id']==$id){
                $text .=" <li><a class='selected' href='/lists/1-".$nav[$i]['id']."-".$nav[$i]['level']."-----"."'>".$nav[$i]['name']."</a></li>";
            }else{
                $text .=" <li><a href='/lists/1-".$nav[$i]['id']."-".$nav[$i]['level']."-----"."'>".$nav[$i]['name']."</a></li>";
            }
        }
        for($i=0;$i<count($nav);$i++){
            if($nav[$i]['id']==$id){
                $sin=Db::table('video_nav')->where('single',$id)->where('level',3)->select();
                if($single==''){
                    $text1 .= "<li><a class='selected' href='/lists/1-".$nav[$i]['id']."-".$nav[$i]['level']."-----"."'>全部</a></li>";
                }else{
                    $text1 .= "<li><a  href='/lists/1-".$nav[$i]['id']."-".$nav[$i]['level']."-----"."'>全部</a></li>";
                }
                for($j=0;$j<count($sin);$j++){
                    if($sin[$j]['id']==$single){
                        $text1 .=" <li><a class='selected' href='/lists/1-".$id."-".$level."-".$sin[$j]['id']."----'>".$sin[$j]['name']."</a></li>";
                    }else{
                        $text1 .=" <li><a href='/lists/1-".$id."-".$level."-".$sin[$j]['id']."----'>".$sin[$j]['name']."</a></li>";
                    }
                }
            }
        }
        $s=Db::table('video_nav')->where('id',$id)->where('level',2)->select();
        if($s){
            $d=Db::table('video_nav')->where('page',$s[0]['id'])->where('level',3)->select();
            for($j=0;$j<count($d);$j++){
                $d[$j]['text']="";
                if($j==3){
                    $c=Db::table('video_nav')->where('total',$d[$j]['id'])->where('level',4)->order('name desc')->select();
                }else{
                    $c=Db::table('video_nav')->where('total',$d[$j]['id'])->where('level',4)->select();
                }
                if($j==0){
                    if($area==''){
                        $d[$j]['text'] .= "<li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-"."---"."'>全部</a></li>";
                    }else{
                        $d[$j]['text'] .= "<li><a  href='/lists/1-".$id."-".$level."-".$single."-"."---"."'>全部</a></li>";
                    }
                    for($i=0;$i<count($c);$i++){
                        if($c[$i]['id']==$area){
                            $d[$j]['text'] .=" <li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$c[$i]['id']."---"."'>".$c[$i]['name']."</a></li>";
                        }else{
                            $d[$j]['text'] .=" <li><a href='/lists/1-".$id."-".$level."-".$single."-".$c[$i]['id']."---"."'>".$c[$i]['name']."</a></li>";
                        }
                    }
                }elseif($j==1){
                    if($time==''){
                        $d[$j]['text'] .= "<li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."---'>全部</a></li>";
                    }else{
                        $d[$j]['text'] .= "<li><a  href='/lists/1-".$id."-".$level."-".$single."-".$area ."---'>全部</a></li>";
                    }
                    for($z=0;$z<count($c);$z++){
                        if($c[$z]['id']==$time){
                            $d[$j]['text'] .=" <li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$c[$z]['id']."--'>".$c[$z]['name']."</a></li>";
                        }else{
                            $d[$j]['text'] .=" <li><a href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$c[$z]['id']."--'>".$c[$z]['name']."</a></li>";
                        }
                    }
                }elseif($j==2){
                    if($nature==''){
                        $d[$j]['text'] .= "<li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."--'>全部</a></li>";
                    }else{
                        $d[$j]['text'] .= "<li><a  href='/lists/1-".$id."-".$level."-".$single."-".$area ."-".$time."--'>全部</a></li>";
                    }
                    for($z=0;$z<count($c);$z++){
                        if($c[$z]['id']==$nature){
                            $d[$j]['text'] .=" <li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."-".$c[$z]['id']."-'>".$c[$z]['name']."</a></li>";
                        }else{
                            $d[$j]['text'] .=" <li><a href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."-".$c[$z]['id']."-'>".$c[$z]['name']."</a></li>";
                        }
                    }
                }elseif($j==3){
                    if($age==''){
                        $d[$j]['text'] .= "<li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."-".$nature."-'>全部</a></li>";
                    }else{
                        $d[$j]['text'] .= "<li><a  href='/lists/1-".$id."-".$level."-".$single."-".$area ."-".$time."-".$nature."-'>全部</a></li>";
                    }
                    for($z=0;$z<count($c);$z++){
                        if($c[$z]['id']==$age){
                            $d[$j]['text'] .=" <li><a class='selected' href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."-".$nature."-".$c[$z]['id']."'>".$c[$z]['name']."</a></li>";
                        }else{
                            $d[$j]['text'] .=" <li><a href='/lists/1-".$id."-".$level."-".$single."-".$area."-".$time."-".$nature."-".$c[$z]['id']."'>".$c[$z]['name']."</a></li>";
                        }
                    }
                }
            }
        }
        $this->assign("type",$text1);
        $this->assign("one",$d);
        $this->assign("header",$text);
    }

    /*处理1级 ranking排行榜导航*/
    public function ranking_nav($page=3,$id=1){
        $nav=Db::table('video_nav')->where('page',$page)->where('level',2)->select();
        $rank=Db::table('video_nav')->where('page',$id)->where('level',2)->select();
        $this->assign('rank',$rank);
        $this->assign('ranks',$nav);
    }

    /*处理1级 videos页面特别筛选*/
    public function video_screen($data,$url){
        if(empty($data[8])){
            $data[8]="";
        }
        $video=Db::table('manga')
            ->where('page',$data[1])
            ->where( 'single','like','%'.$data[3].'%')
            ->where( 'region','like','%'.$data[4].'%')
            ->where( 'play','like','%'.$data[5].'%')
            ->where( 'nature','like','%'.$data[6].'%')
            ->where( 'age','like','%'.$data[7].'%')
            ->where('manga_static',1)
            ->order('manga_time desc')
            ->paginate(30,false,[
                'p'=>$data[8],
                'url'=>$url,
                'pan'=>1,
            ]);
        $page=$video->render();
        $this->assign('page',$page);
        $this->assign('zx',$video);
    }

    /*text文本省略*/
    //处理1级 截取字符串长度
    function cut($Str, $Length,$more=true) {
        //$Str为截取字符串，$Length为需要截取的长度

        global $s;
        $i = 0;
        $l = 0;
        $ll = strlen($Str);
        $s = $Str;
        $f = true;
        while ($i <= $ll) {
            if($ll==$i){
                $s=$Str;
                return $s;
            }
            if (ord($Str{$i}) < 0x80) {
                $l++; $i++;
            } else if (ord($Str{$i}) < 0xe0) {
                $l++; $i += 2;
            } else if (ord($Str{$i}) < 0xf0) {
                $l += 2; $i += 3;
            } else if (ord($Str{$i}) < 0xf8) {
                $l += 1; $i += 4;
            } else if (ord($Str{$i}) < 0xfc) {
                $l += 1; $i += 5;
            } else if (ord($Str{$i}) < 0xfe) {
                $l += 1; $i += 6;
            }

            if (($l >= $Length - 1&& $f)) {
                $s = substr($Str, 0, $i);
                $f=false;
            }

            if (($l > $Length) && ($i < $ll) && $more) {
                $s = $s . '...';
                break;
                //如果进行了截取，字符串末尾加省略符号“...”
            }
        }
        return $s;
    }

    /*处理1级 用户签到公共处理函数*/
    function sign_in($id='1'){
        $start_time = strtotime(date('Y-m-d'));
        $year = date("Y"); //今天-年
        $month = date("m"); //今天-月
        $day = date("d"); //今天-日
        $yesterday = strtotime('-1 day');
        $yesterday_year = date('Y',$yesterday); //昨天-年
        $yesterday_month = date('m',$yesterday); //昨天-月
        $yesterday_day = date('d',$yesterday); //昨天-日
        $uid=Db::table('member_sign')->where('sign_uid',$id)->find();
        if($uid) {
            $sign = Db::table('member_sign')->where(array('sign_uid' => $id, 'sign_time' => array('EGT', $start_time)))->count();
            if ($sign) {
                return 2;
            } else {
                if ($uid['month'] == $yesterday_month) {
                    $uid['sign_month'] = $uid['sign_month'] + 1;
                }else{
                    $uid['sign_month'] =10;
                }
                $data = array(
                    'sign_uid' => $id,
                    'year' => $yesterday_year,
                    'month' => $yesterday_month,
                    'day' => $yesterday_day
                );
                $signs = Db::table('member_sign')->where($data)->select();
                if ($signs) {
                    $data = array(
                        'sign_time' => time(),
                        'sign_number' => $uid['sign_number'] + 1,
                        'sign_con' => $uid['sign_con'] + 1,
                        'sign_month' => $uid['sign_month'],
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                    );
                    if($uid['sign_con']+1>=10){
                        $money=20;
                    }else{
                        $money=$uid['sign_con']+1;
                    }
                } else {
                    $data = array(
                        'sign_time' => time(),
                        'sign_number' => $uid['sign_number'] + 1,
                        'sign_con' => 1,
                        'sign_month' => $uid['sign_month'],
                        'year' => $year,
                        'month' => $month,
                        'day' => $day,
                    );
                    $money=10;
                }
                if (Db::table('member_sign')->where('sign_uid',$id)->update($data)) {
                    $ids=Db::table('system_money')->where('money_id',1)->find();
                    if($ids['money_number']>0) {
                        Db::table('system_money')->where('money_id',1)->setDec('money_number',$money);
                        Db::table('member')->where('id', $id)->setInc('money',$money);
                        $income=Db::table('user_income')->where('user_income_member',$id)->where('user_income_class',0)->find();
                        if($income){
                            Db::table('user_income')->where('user_income_member', $id)->where('user_income_class',0)->setInc('user_income_money', 10);
                        }else{
                            $where=array(
                                'user_income_member'=>$id,
                                'user_income_class'=>0,
                                'user_income_money'=>1,
                                'user_income_time'=>time()
                            );
                            Db::table('user_income')->insert($where);
                        }
                    }
                    return 1;
                }else{
                    return 3;
                }
            }
        }else{
            $data = array(
                'sign_uid' => $id,
                'sign_time' => time(),
                'year' => $year,
                'month' => $month,
                'day' => $day,
            );
            if(Db::table('member_sign')->insert($data)) {
                $ids=Db::table('system_money')->where('money_id',1)->find();
                if($ids['money_number']>0) {
                    Db::table('system_money')->where('money_id',1)->setDec('money_number',10);
                    Db::table('member')->where('id', $id)->setInc('money', 10);
                    $income=Db::table('user_income')->where('user_income_member',$id)->where('user_income_class',0)->find();
                    if($income){
                        Db::table('user_income')->where('user_income_member', $id)->where('user_income_class',0)->setInc('user_income_money', 10);
                    }else{
                        $where=array(
                            'user_income_member'=>$id,
                            'user_income_class'=>0,
                            'user_income_money'=>10,
                            'user_income_time'=>time()
                        );
                        Db::table('user_income')->insert($where);
                    }
                }
                return 1;
            }else{
                return 2;
            }
        }
    }

    /*处理1级 个人数据*/
    function user_number($id="1"){
        $array=array(
            'video',
            'theme',
            'fans'
        );
        for($i=0;$i<count($array);$i++){
            if($array[$i]=="video"){
                $number=Db::table('mange_part')
                    ->where('mange_part_static',1)
                    ->where('mange_part_user',$id)->count();
                $num['video']=$number;
            }elseif($array[$i]=="fans"){
                $number1=Db::table('user_fans')->where('fans_id',$id)->select();
                $num['follow']=count($number1);
                $number2=Db::table('user_fans')->where('user_id',$id)->select();
                $number2=count($number2);
                if($number2>10000){
                    $number2=$number2/10000;
                    $num['fans']=$number2."万";
                }else{
                    $num['fans']=$number2;
                }
            }elseif($array[$i]=='theme'){
                $number=Db::table('mange_theme')->where('mange_theme_user',$id)->count();
                $num['theme']=$number;
            }
        }
        $this->assign('num',$num);
    }

    /*处理1级 粉丝判断*/
    function user_fans($id="1"){
        $user=Session::get('users');
        if($id==$user){
            $fans['static']="";
        }else{
            $fans['static']=1;
            $static=Db::table('user_fans')->where(array('user_id'=>$id,'fans_id'=>$user))->find();
            if($static){
                $fans['fans']=1;
            }else{
                $fans['fans']=0;
            }
            $fans['fans_user']=$user;
        }
        $this->assign("fans",$fans);
    }

    /*处理1级 评论整合*/
    function comment($id="1",$part="",$class=""){
        $comment=Db::table('manga_comment')
            ->alias('c')
            ->join('member m','m.id=c.manga_comment_member')
            ->where(array('c.manga_comment_class'=>$class,'c.manga_comment_manga'=>$id,'c.manga_comment_part'=>$part,'c.manga_comment_reply'=>0,'c.manga_comment_static'=>1))
            ->order('c.manga_comment_floor desc')
            ->paginate(10,true,[
            'var_page' => 'page',
        ]);
        $comment->toArray();
        foreach($comment as $k =>$v){
            $v['comment']=Db::table('manga_comment')
                ->alias('c')
                ->join('member m','m.id=c.manga_comment_member')
                ->where(array('c.manga_comment_class'=>$class,'c.manga_comment_manga'=>$id,'c.manga_comment_part'=>$part,'c.manga_comment_num'=>1,'c.manga_comment_static'=>1,'c.manga_comment_floor'=>$v['manga_comment_floor']))
                ->order('c.manga_comment_time asc')
                ->paginate(10,true,[
                    'var_page' => 'pages',
                ]);
            $v['pages']=$v['comment']->render();
            $comment->offsetSet($k,$v);
        }
        $page=$comment->render();
        $this->assign("page",$page);
        $comm=1;
        $this->assign("comm",$comm);
        $this->assign("comment",$comment);
    }

    /*处理1级 主体评论整合*/
    function comment_theme($id="1",$part="",$class=""){
        $comment=Db::table('mange_comment')
            ->alias('c')
            ->join('member m','m.id=c.mange_comment_member')
            ->where(array('c.mange_comment_class'=>$class,'c.mange_comment_mange'=>$id,'c.mange_comment_part'=>$part,'c.mange_comment_reply'=>0,'c.mange_comment_static'=>1))
            ->order('c.mange_comment_floor desc')
            ->paginate(10,true,[
                'var_page' => 'page',
            ]);
        $comment->toArray();
        foreach($comment as $k =>$v){
            $v['comment']=Db::table('mange_comment')
                ->alias('c')
                ->join('member m','m.id=c.mange_comment_member')
                ->where(array('c.mange_comment_class'=>$class,'c.mange_comment_mange'=>$id,'c.mange_comment_part'=>$part,'c.mange_comment_num'=>1,'c.mange_comment_static'=>1,'c.mange_comment_floor'=>$v['mange_comment_floor']))
                ->order('c.mange_comment_time asc')
                ->paginate(10,true,[
                    'var_page' => 'pages',
                ]);
            $v['pages']=$v['comment']->render();
            $comment->offsetSet($k,$v);
        }
        $page=$comment->render();
        $this->assign("page",$page);
        $comm=1;
        $this->assign("comm",$comm);
        $this->assign("comment",$comment);
    }


    //处理1级 评论ajax整合
    function comments($id){
        $comment=Db::table('mange_comment')
            ->alias('c')
            ->join('member m','m.id=c.mange_comment_member')
            ->where(array('c.mange_comment_class'=>$id['num'],'c.mange_comment_mange'=>$id['id'],'c.mange_comment_part'=>$id['part'],'c.mange_comment_reply'=>0,'c.mange_comment_static'=>1))
            ->order('c.mange_comment_floor desc')
            ->paginate(10,true,[
                'var_page' => 'page',
                'page'=>$id['page'],
            ]);
        $comment->toArray();
        foreach($comment as $k =>$v){
            if($v['image']==""){
                $v['image']="default.jpg";
            }
            $v['comment']=Db::table('mange_comment')
                ->alias('c')
                ->join('member m','m.id=c.mange_comment_member')
                ->where(array('c.mange_comment_class'=>$id['num'],'c.mange_comment_mange'=>$id['id'],'c.mange_comment_part'=>$id['part'],'c.mange_comment_num'=>1,'c.mange_comment_static'=>1,'c.mange_comment_floor'=>$v['mange_comment_floor']))
                ->paginate(10,true,[
                    'var_page' => 'pages',
                    'pages'=>$id['pages'],
                ]);
            $v['comment']->toArray();
            foreach($v['comment'] as $a =>$s){
                if($s['image']==""){
                    $s['image']="default.jpg";
                }
                $v['comment']->offsetSet($a,$s);
            }
            $v['pages']=$v['comment']->render();
            $comment->offsetSet($k,$v);
        }
        return $comment;
    }

    //处理1级 用户id判断处理
    public function user_error($data){
        if(empty($data)){
            return false;
        }else{
            if(empty($data['id'])){
                return false;
            }else{
                if(is_numeric($data['id'])){
                    $pan=Db::table('member')->where('id',$data['id'])->select();
                    if(!$pan){
                        return false;
                    }else{
                        return true;
                    }
                }else{
                    return false;
                }
            }
        }

    }

    //处理1级 手动报错
    public function skip($text,$url,$h1,$static,$time=3){
        $this->header_nav();
        $this->nav_login();
        $this->assign('text',$text);
        $this->assign('url',$url);
        $this->assign('h1',$h1);
        $this->assign('time',$time);
        if($static == "success"){
            return $this->fetch('comment/success');
        }elseif($static=="error"){
            return $this->fetch('comment/error');
        }

    }

    //处理1级 创作中心报错
    public function create_skip($text,$url,$h1,$static,$time=3){
        $this->header_nav();
        $this->nav_login();
        $this->assign('text',$text);
        $this->assign('url',$url);
        $this->assign('h1',$h1);
        $this->assign('time',$time);
        if($static == "success"){
            return $this->fetch('create/success');
        }elseif($static=="error"){
            return $this->fetch('create/error');
        }

    }

    //处理1级 人性化时间
    function user_time($time){
        if(empty($time)){
            return false;
        }
        $minute =60;
        $hour = $minute * 60;
        $day = $hour * 24;
        $month = $day * 30;
        $age =$month*12;
        $times=time();
        $Time=$times-$time;
        $ageC =intval($Time/$age);
        $monthC =intval($Time/$month);
        $dayC=intval($Time/$day);
        $hourC=intval($Time/$hour);
        $minC=intval($Time/$minute);
        if($age<=$Time){
            return $time=$ageC."年前";
        }elseif($month<=$Time&&$age>$Time){
            return $time=$monthC."月前";
        }elseif($day<=$Time&&$month>$Time){
            return $time=$dayC."天前";
        }elseif($hour<=$Time&&$day>$Time){
            return $time=$hourC."小时前";
        }elseif($minute<=$Time&&$hour>$Time){
            return $time=$minC."分钟前";
        }
    }

    function getMonthDay($year,$month){
        if($month>12){
            $month=1;
            $year+=1;
        }else if($month<1){
            $month=12;
            $year-=1;
        }
        $arr=array(31,28,31,30,31,30,31,31,30,31,30,31);
        $days=0;
        if(($year%4==0&&$year%100!=0)||$year%400==0){
            $arr[1]=29;
        }
        $days=$arr[$month-1];
        return $days;
    }

    //处理1级 修仙公共处理成为导师或者指定函数
    function level_user_up($level="",$manga=""){
        $user=Session::get('users');
        $up=Db::table('member')->where('id',$user)->find();
        if($up['level']>=$level){
            $level_level['up']=1;
            $zhi=Db::table('level_tutor')->where('level_tutor_yu',null)->where('level_tutor_manga',$manga)->where('level_tutor_level',$level)->find();
            if($zhi){
                if($user==$zhi['level_tutor_yu']){
                    $level_level['zhi']=1;
                }else{
                    $level_level['zhi']=0;
                }
                $level_level['zhiding']=1;
            }else{
                $level_level['zhiding']=0;
                $level_level['zhi']=0;
            }
        }else{
            $level_level['zhi']=0;
            $level_level['up']=0;
            $level_level['zhiding']=0;
        }
        return  $level_level;
    }

    //处理1级 修仙公共处理
    function level_comment($id=''){
        $level=Db::table('level_theme_date')->where('level_date_mange',$id)->find();
        $level_level['number']=$level['level_date_level'];
        $user=$this->level_user_up($level['level_date_level'],$id);
        $level_level['zhi']=$user['zhi'];
        $level_level['zhiding']=$user['zhiding'];
        $level_level['up']=$user['up'];
        $level_level['level']=$level['level_date_level'];
        $tutor=Db::table('level_tutor')->where('level_tutor_manga',$id)->where('level_tutor_level',$level['level_date_level'])->find();
        if(!$tutor['level_tutor_yu']==null){
            $level_level['level_yu']=1;
        }else{
            $level_level['level_yu']=0;
        }
        if($tutor&&(!$tutor['level_tutor_user']==null)){
            $level_level['tutor_static']=1;
            $name=Db::table('member')->where('id',$tutor['level_tutor_user'])->find();
            if(!$name['nickname']==''){
                $name['name']=$name['nickname'];
            }
            $level_level['tutor_id']=$tutor['level_tutor_user'];
            $level_level['tutor_name']=$name['name'];
        }else{
            $level_level['level_up']=0;
            $level_level['tutor_static']=0;
        }
        if($level['level_date_level']==1){
            if($level['level_date_click']>=$level['level_date_click_up']){
                $level_level['level_up']=1;
            }else{
                $level_level['level_up']=0;
            }

        }
        elseif($level['level_date_level']==2){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($level['level_date_level']==3){


                    if($level['level_date_click']>=$level['level_date_click_up']
                        &&$level['level_date_shou']>=$level['level_date_shou_up']
                        &&$level['level_date_assess']>=$level['level_date_assess_up']){
                        $level_level['level_up']=1;
                    }else{
                        $level_level['level_up']=0;
                    }
            }
        elseif($level['level_date_level']==4){


                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($level['level_date_level']==5){


                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']){
                        $level_level['level_up']=1;
                    }else{
                        $level_level['level_up']=0;
                    }
        }
        elseif($level['level_date_level']==6){


                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($level['level_date_level']==7){


                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($level['level_date_level']==8){
                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']
                    &&$level['level_date_offer']>=$level['level_date_offer_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($level['level_date_level']==9){

                if($level['level_date_click']>=$level['level_date_click_up']
                    &&$level['level_date_shou']>=$level['level_date_shou_up']
                    &&$level['level_date_assess']>=$level['level_date_assess_up']
                    &&$level['level_date_comment']>=$level['level_date_comment_up']
                    &&$level['level_date_support']>=$level['level_date_support_up']
                    &&$level['level_date_just']>=$level['level_date_just_up']
                    &&$level['level_date_honor']>=$level['level_date_honor_up']
                    &&$level['level_date_offer']>=$level['level_date_offer_up']
                    &&$level['level_date_backer']>=$level['level_date_backer_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }

        }
        $level_level['progress_num']=$this->level_level_number($level['level_date_click'],$level['level_date_click_up']);
        $level_level['progress']="<span>".$level['level_date_click']."/".$level['level_date_click_up']."</span>";
        $level_level['shou_num']=$this->level_level_number($level['level_date_shou'],$level['level_date_shou_up']);
        $level_level['shou']="<span>".$level['level_date_shou']."/".$level['level_date_shou_up']."</span>";
        $level_level['assess_num']=$this->level_level_number($level['level_date_assess'],$level['level_date_assess_up']);
        $level_level['assess']="<span>".$level['level_date_assess']."/".$level['level_date_assess_up']."</span>";
        $level_level['comment_num']=$this->level_level_number($level['level_date_comment'],$level['level_date_comment_up']);
        $level_level['comment']="<span>".$level['level_date_comment']."/".$level['level_date_comment_up']."</span>";
        $level_level['support_num']=$this->level_level_number($level['level_date_support'],$level['level_date_support_up']);
        $level_level['support']="<span>".$level['level_date_support']."/".$level['level_date_support_up']."</span>";
        $level_level['just_num']=$this->level_level_number($level['level_date_just'],$level['level_date_just_up']);
        $level_level['just']="<span>".$level['level_date_just']."/".$level['level_date_just_up']."</span>";
        $level_level['honor_num']=$this->level_level_number($level['level_date_honor'],$level['level_date_honor_up']);
        $level_level['honor']="<span>".$level['level_date_honor']."/".$level['level_date_honor_up']."</span>";
        $level_level['offer_num']=$this->level_level_number($level['level_date_offer'],$level['level_date_offer_up']);
        $level_level['offer']="<span>".$level['level_date_offer']."/".$level['level_date_offer_up']."</span>";
        $level_level['backer_num']=$this->level_level_number($level['level_date_backer'],$level['level_date_backer_up']);
        $level_level['backer']="<span>".$level['level_date_backer']."/".$level['level_date_backer_up']."</span>";
        $this->assign('l_l',$level_level);
    }

    //处理1级 人物修仙公共处理
    function member_level_comment($id=''){
        $level=Db::table('member_level_date')->where('member_level_member',$id)->find();
        $form=Db::table('member')->where('id',$id)->find();
        $level_level['level']=$form['level'];
        $power=Db::table('member_level_power')->where('member_level_power_name',$id)->where('member_level_power_static',1)->where('member_level_power_level',$form['level'])->find();
        if($power){
            $level_level['level_power']=1;
        }else{
            $level_level['level_power']=0;
        }
        $rise=Db::table('member_level_rise')->where('member_level_rise_name',$id)->where('member_level_rise_level',$form['level'])->find();
        if($rise){
            $level_level['level_rise']=1;
        }else{
            $level_level['level_rise']=0;
        }
        if($form['level']==1){
            if($level['member_level_fans']>=$level['member_level_fans_up']){
                $level_level['level_up']=1;
            }else{
                $level_level['level_up']=0;
            }
        }
        elseif($form['level']==2){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==3){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==4){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==5){
            $num=$this->dao_static($id,$form['level']);
            if($level['member_level_fans']>=$level['member_level_fans_up']
                &&$num['n']==1
                &&$num['number']>=10
                &&$level['member_level_comment']>=$level['member_level_comment_up']
                &&$level['member_level_article']>=$level['member_level_article_up']
                &&$level['member_level_money']>=$level['member_level_money_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==6){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==7){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==8){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }
        elseif($form['level']==9){
            $num=$this->dao_static($id,$form['level']);
                if($level['member_level_fans']>=$level['member_level_fans_up']
                    &&$num['n']==1
                    &&$num['number']>=10
                    &&$level['member_level_comment']>=$level['member_level_comment_up']
                    &&$level['member_level_article']>=$level['member_level_article_up']
                    &&$level['member_level_money']>=$level['member_level_money_up']){
                    $level_level['level_up']=1;
                }else{
                    $level_level['level_up']=0;
                }
        }elseif($form['level']==10){
            $level_level['level_up']=0;
        }
        $num=$this->dao_static($id,$form['level']);
        $level_level['progress_num']=$this->level_level_number($level['member_level_fans'],$level['member_level_fans_up']);
        $level_level['progress']="<span>".$level['member_level_fans']."/".$level['member_level_fans_up']."</span>";
        $level_level['shou_num']=$this->level_level_number($num['n'],1);
        $level_level['shou']="<span>".$num['n']."/1"."</span>";
        $level_level['assess_num']=$this->level_level_number($num['number'],$level['member_level_dao_number_up']);
        $level_level['assess']="<span>".$num['number']."/".$level['member_level_dao_number_up']."</span>";
        $level_level['comment_num']=$this->level_level_number($level['member_level_comment'],$level['member_level_comment_up']);
        $level_level['comment']="<span>".$level['member_level_comment']."/".$level['member_level_comment_up']."</span>";
        $level_level['support_num']=$this->level_level_number($level['member_level_article'],$level['member_level_article_up']);
        $level_level['support']="<span>".$level['member_level_article']."/".$level['member_level_article_up']."</span>";
        $level_level['just_num']=$this->level_level_number($level['member_level_money'],$level['member_level_money_up']);
        $level_level['just']="<span>".$level['member_level_money']."/".$level['member_level_money_up']."</span>";
        $this->assign('l_l',$level_level);
    }

    function level_level_number($number,$up){
        $num=intval($number/$up*100);
        if($num>=100){
            $num=100;
        }
        $num="style=width:".$num."%;";
        return $num;
    }

    //处理1级 数据人性化万亿
    function number_say($number=""){
        if($number>10000&&$number<100000000){
            $number=intval($number/10000)."万";
        }elseif($number>100000000){
            $number=intval($number/100000000)."亿";
        }
        return $number;
    }

    //处理1级 导师升级作品要求函数
    public function dao_static($id="",$level=""){
        $dao=Db::table('level_tutor')->where('level_tutor_user',$id)->where('level_tutor_level',$level)->find();
        if($dao){
            $num['n']=1;
        }else{
            $num['n']=0;
        }
        $num['number']=count(Db::table('level_tutor')->where('level_tutor_user',$id)->where('level_tutor_level',$level-1)->select());
        return $num;
    }

    //处理1级 作品升级处理分配
    public function level_allot($manga="",$dao=""){
        $form=Db::table('level_date')->where('level_date_manga',$manga)->find();
        $dao_number=intval($form['level_date_click_up']*0.1);
        $member=intval($form['level_date_click_up']-$dao_number-$form['level_date_click_up']*0.1);
        $this->level_allot_xi(Session::get('users'),$member,$manga);
        $this->level_allot_xi($dao,$dao_number,$manga);
    }

    //处理1级 作品分配处理细节
    public function level_allot_xi($id="",$money="",$manga=""){
        $income=Db::table('user_income')->where('user_income_member',$id)->where('user_income_manga',$manga)->where('user_income_class',2)->find();
        Db::table('member')->where('id',$id)->setInc('money',$money);
        if($income){
            Db::table('user_income')->where('user_income_member',$id)->where('user_income_manga',$manga)->where('user_income_class',2)->setInc('user_income_money',$money);
        }else{
            $where=array(
                'user_income_member'=>$id,
                'user_income_manga'=>$manga,
                'user_income_class'=>2,
                'user_income_money'=>$money,
                'user_income_time'=>time()
            );
            Db::table('user_income')->insert($where);
        }
    }

    //处理1级 打赏收入处理细节
    public function user_income($user="",$use="",$manga="",$class="",$money=""){
        $income=Db::table('user_income')->where('user_income_member',$user)->where('user_income_members',$use)->where('user_income_manga',$manga)->where('user_income_class',$class)->find();
        if($income){
            Db::table('user_income')->where('user_income_member',$user)->where('user_income_manga',$manga)->where('user_income_members', $use)->where('user_income_class',$class)->setInc('user_income_money', $money);
        }else{
            $where=array(
                'user_income_member'=>$user,
                'user_income_class'=>$class,
                'user_income_money'=>$money,
                'user_income_members'=>$use,
                'user_income_manga'=>$manga,
                'user_income_time'=>time()
            );
            Db::table('user_income')->insert($where);
        }
    }

    //处理1级 消费处理细节
    public function user_coins($use="",$manga="",$money=""){
        $coin=Db::table('user_coin')->where('user_id',$use)->where('video_id',$manga)->find();
        if($coin){
            Db::table('user_coin')->where('user_id',$use)->where('video_id',$manga)->setInc('manga_money',$money);
            Db::table('member_level_date')->where('member_level_member',$use)->setInc('member_level_money',$money);
        }else{
            $where=array(
                'user_id'=>$use,
                'video_id'=>$manga,
                'coin_time'=>time(),
                'manga_money'=>$money
            );
            Db::table('user_coin')->insert($where);
        }
    }
}