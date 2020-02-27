<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
class Contribute extends Comment{

    public function contribute(){
        $data=Db::table('video_nav')->where('level',2)->where('page',1)->select();
        $this->assign('nav',$data);
        $this->header_nav();
        $this->nav_login();
        $id=Session::get('users');
        if($id==""){
            return $this->fetch('login/login');
        }
        return $this->fetch();
    }

    public function theme(){
        $data=Db::table('video_nav')->where('level',2)->where('page',1)->select();
        $this->assign('nav',$data);
        $this->header_nav();
        $this->nav_login();
        $id=Session::get('users');
        if($id==""){
            return $this->fetch('login/login');
        }
        return $this->fetch();
    }

    public function works(){

        $this->header_nav();
        $this->nav_login();
        $id=Session::get('users');
        if($id==""){
            return $this->fetch('login/login');
        }
        $da=Db::table('mange_theme')->where('mange_theme_user',$id)->select();
        $this->assign('na',$da);
        return $this->fetch();
    }

    public function articleCb(){
        $this->header_nav();
        $this->nav_login();
        $title=input('article');
        $video=Db::table('manga')->where('manga_id',$title)->find();
        $this->assign('vi',$video);
        $id=Session::get('users');
        $class=Db::table('manga_article_class')->select();
        $this->assign('class',$class);
        if($id==""){
            return $this->fetch('login/login');
        }
        return $this->fetch();
    }

    public function theme_post()
    {
        $data = input('post.');
        $this->header_nav();
        $this->nav_login();
        if (!empty($data)) {
            $array = array(
                'mange_theme_user',
                'mange_theme_title',
                'mange_theme_page',
                'mange_theme_img',
                'mange_theme_text',
                'mange_theme_class',
                'mange_theme_time',
                'mange_theme_line',
                'mange_theme_pass'
            );
                for ($i = 0; $i < count($array); $i++) {
                    if ($array[$i] == 'mange_theme_user') {
                        $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                    } elseif ($array[$i] == 'mange_theme_time') {
                        $form[$array[$i]] = time();
                    } elseif ($array[$i] == 'mange_theme_text') {
                        if (!empty($data[$array[$i]])) {
                            $form[$array[$i]] = $data[$array[$i]];
                        }else{
                            $form[$array[$i]]='未知描述';
                        }
                    }elseif($array[$i]=='mange_theme_img') {
                        $form[$array[$i]] =$data[$array[$i]];
                    }else{
                        $form[$array[$i]] = $this->infusion($array[$i], $data);
                    }
                }
                Db::table('mange_theme_copy')->insert($form);
                if ($form) {
                        $news=array(
                            'user_id'=>$data['mange_theme_user'],
                            'uid'=>1,
                            'new_classify'=>1,
                            'static'=>0,
                            'new_text'=>$data['mange_theme_title']."正在等待审核！",
                            'time'=>time()
                        );
                    Db::table('news_new')->insert($news);
                    return $this->skip("已上交投稿，请等待审核!", "index","首页","success");
                } else {
                    return $this->skip("投稿提交失败！请重新投稿", "contribute","投稿","error");
                }
        } else {
            abort(404);
        }
    }

    public function works_post()
    {
        $data = input('post.');
        $this->header_nav();
        $this->nav_login();
        if (!empty($data)) {
            $array = array(
                'mange_part_name',
                'mange_part_user',
                'mange_part_text',
                'mange_part_level',
                'mange_part_time',
                'mange_part_img',
                'mange_part_line'
            );
            for ($i = 0; $i < count($array); $i++) {
                if ($array[$i] == 'mange_part_user') {
                    $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                } elseif ($array[$i] == 'mange_part_time') {
                    $form[$array[$i]] = time();
                } elseif ($array[$i] == 'mange_part_text') {
                    if (!empty($data[$array[$i]])) {
                        $form[$array[$i]] = $data[$array[$i]];
                    }else{
                        $form[$array[$i]]='未知描述';
                    }
                }elseif($array[$i]=='mange_part_img') {
                    $form[$array[$i]] =$data[$array[$i]];
                }else{
                    $form[$array[$i]] = $this->infusion($array[$i], $data);
                }
            }
            Db::table('mange_part_copy')->insert($form);
            if ($form) {
                $news=array(
                    'user_id'=>$data['mange_part_user'],
                    'uid'=>1,
                    'new_classify'=>1,
                    'static'=>0,
                    'new_text'=>$data['mange_part_name']."正在等待审核！",
                    'time'=>time()
                );
                Db::table('news_new')->insert($news);
                return $this->skip("已上交投稿，请等待审核!", "index","首页","success");
            } else {
                return $this->skip("投稿提交失败！请重新投稿", "contribute","投稿","error");
            }
        } else {
            abort(404);
        }
    }

    public function mangaCb_post()
    {
        $data = input('post.');
        $this->header_nav();
        $this->nav_login();
        if (!empty($data)) {
            $array = array(
                'user_id',
                'title',
                'page',
                'single',
                'region',
                'play',
                'nature',
                'img',
                'downClass',
                'downPass',
                'downLine',
                'manga_text',
                'age',
                'manga_time'
            );
            if($data['page']==5){
                for ($i = 0; $i < count($array); $i++) {
                    if ($array[$i] == 'user_id') {
                        $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                    } elseif ($array[$i] == 'manga_time') {
                        $form[$array[$i]] = time();
                    } elseif ($array[$i] == 'manga_text') {
                        if (!empty($data[$array[$i]])) {
                            $form[$array[$i]] = $data[$array[$i]];
                        }else{
                            $form[$array[$i]]='未知描述';
                        }
                    }elseif($array[$i]=='single'){
                        if(!array_key_exists('single',$data)){
                            $text="类型未选择！";
                            $this->assign('text',$text);
                            return $this->fetch('comment/404');
                        }
                        $form[$array[$i]]=$data[$array[$i]];
                    }elseif($array[$i]=='img') {
                        $form[$array[$i]] =$data[$array[$i]];
                    }else{
                        $form[$array[$i]] = $this->infusion($array[$i], $data);
                    }
                }
                if (Db::table('manga_copy')->insert($form)) {
                    $Uid = Db::table('manga_copy')->getLastInsID();
                    $array1 = array(
                        'Collect',
                        'Line',
                    );
                    $num = count($data[$array1[1]]);
                    for ($j = 0; $j < $num; $j++) {
                        $video[$j]['Uid'] = $Uid;
                        $video[$j]['part_time'] = time();
                        for ($i = 0; $i < count($array1); $i++) {
                            $video[$j][$array1[$i]] = $data[$array1[$i]][$j];
                            $video[$j][$array1[$i]] = $this->infusion($array1[$i], $video[$j]);
                        }
                    }
                    for ($z = 0; $z < count($video); $z++) {
                        $form = Db::table('manga_part_copy')->insert($video[$z]);
                        Db::table('manga_part_copy')->getLastInsID();
                    }
                    if ($form) {
                        $news=array(
                            'user_id'=>$data['user_id'],
                            'uid'=>1,
                            'new_classify'=>1,
                            'static'=>0,
                            'new_text'=>$data['title']."正在等待审核！",
                            'time'=>time()
                        );
                        Db::table('news_new')->insert($news);
                        return $this->skip("已上交投稿，请等待审核!", "index","首页","success");
                    } else {
                        return $this->skip("投稿提交失败！请重新投稿", "contribute","投稿","error");
                    }
                };
            }elseif($data['page']==6){
                for ($i = 0; $i < count($array); $i++) {
                    if ($array[$i] == 'user_id') {
                        $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                    } elseif ($array[$i] == 'manga_time') {
                        $form[$array[$i]] = time();
                    } elseif ($array[$i] == 'manga_text') {
                        if (!empty($data[$array[$i]])) {
                            $form[$array[$i]] = $data[$array[$i]];
                        }else{
                            $form[$array[$i]]='未知描述';
                        }
                    }elseif($array[$i]=='single'){
                        if(!array_key_exists('single',$data)){
                            $text="类型未选择！";
                            $this->assign('text',$text);
                            return $this->fetch('comment/404');
                        }
                        $form[$array[$i]]=$data[$array[$i]];
                    } elseif($array[$i]=='nature'){
                        if(!array_key_exists('nature',$data)){
                            $text="属性未选择！";
                            $this->assign('text',$text);
                            return $this->fetch('comment/404');
                        }
                        for($j=0;$j<count($data[$array[$i]]);$j++){
                            if($j==0){
                                $form[$array[$i]]=$data[$array[$i]][$j];
                            }else{
                                $form[$array[$i]]=$form[$array[$i]].','.$data[$array[$i]][$j];
                            }
                        }
                    }elseif($array[$i]=='img') {
                        $form[$array[$i]] =$data[$array[$i]];
                    }else{
                        $form[$array[$i]] = $this->infusion($array[$i], $data);
                    }
                }
                if (Db::table('manga_copy')->insert($form)) {
                    $Uid = Db::table('manga_copy')->getLastInsID();
                    $array1 = array(
                        'Collect',
                        'Line',
                    );
                    $num = count($data[$array1[1]]);
                    for ($j = 0; $j < $num; $j++) {
                        $video[$j]['Uid'] = $Uid;
                        $video[$j]['part_time'] = time();
                        for ($i = 0; $i < count($array1); $i++) {
                            $video[$j][$array1[$i]] = $data[$array1[$i]][$j];
                            $video[$j][$array1[$i]] = $this->infusion($array1[$i], $video[$j]);
                        }
                    }
                    for ($z = 0; $z < count($video); $z++) {
                        $form = Db::table('manga_part_copy')->insert($video[$z]);
                        Db::table('manga_part_copy')->getLastInsID();
                    }
                    if ($form) {
                        $news=array(
                            'user_id'=>$data['user_id'],
                            'uid'=>1,
                            'new_classify'=>1,
                            'static'=>0,
                            'new_text'=>$data['title']."正在等待审核！",
                            'time'=>time()
                        );
                        Db::table('news_new')->insert($news);
                        return $this->skip("已上交投稿，请等待审核!", "index","首页","success");
                    } else {
                        return $this->skip("投稿提交失败！请重新投稿", "contribute","投稿","error");
                    }
                };
            }

        } else {
            abort(404);
        }
    }

    public function articleCb_post()
    {
        $data = input('post.');
        $this->header_nav();
        $this->nav_login();
        $users=Session::get('users');
        if (!empty($data)) {
            $data['manga_article_time']=time();
            $data['manga_article_member']=$users;
            $video['manga_article_text'] = $this->infusion('manga_article_text', $data);
            $form=Db::table('manga_article')->insert($data);
            if ($form) {
                $news = array(
                    'user_id' => $users,
                    'uid' => 1,
                    'new_classify' => 1,
                    'static' => 0,
                    'new_text' => $data['manga_article_title'] . "正在等待审核！",
                    'time' => time()
                );
                Db::table('news_new')->insert($news);
                return $this->skip("已上交投稿，请等待审核!", "main/".$data['manga_article_manga'], "首页", "success");
            } else {
                return $this->skip("投稿提交失败！请重新投稿", "articleCb", "投稿", "error");
            }
        }else {
            abort(404);
        }
    }

    public function contribute_total(){
        $data=input('post.');
        $single=Db::table('video_nav')->where('single',$data['id'])->where('level',3)->where('page',1)->select();
        if($single){
            $text['msg']=1;
        }else{
            $text['msg']=0;
        }
        $text['page']="";
        for($i=0;$i<count($single);$i++){
            $text['page'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='single' value=".$single[$i]['id']."><span>".$single[$i]['name']."</span></div>";
        }
        $zong=Db::table('video_nav')->where('level',3)->where('page',$data['id'])->select();
        if($data['id']==5){
            for($z=0;$z<count($zong);$z++){
            if($z==3){
                $w=Db::table('video_nav')->where('level',4)->where('total',$zong[$z]['id'])->order('name desc')->select();
            }else{
                $w=Db::table('video_nav')->where('level',4)->where('total',$zong[$z]['id'])->select();
            }
            if($z==0){
                $text['region']="";
                for($j=0;$j<count($w);$j++){
                    $text['region'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='region' value=".$w[$j]['id']."><span>".$w[$j]['name']."</span></div>";
                }
            }elseif($z==1){
                $text['age']="";
                for($a=0;$a<count($w);$a++){
                    $text['age'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='age' value=".$w[$a]['id']."><span>".$w[$a]['name']."</span></div>";
                }
            }
        }
        }elseif($data['id']==6){
            for($z=0;$z<count($zong);$z++){
                if($z==3){
                    $w=Db::table('video_nav')->where('level',4)->where('total',$zong[$z]['id'])->order('name desc')->select();
                }else{
                    $w=Db::table('video_nav')->where('level',4)->where('total',$zong[$z]['id'])->select();
                }
                if($z==0){
                    $text['region']="";
                    for($j=0;$j<count($w);$j++){
                        $text['region'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='region' value=".$w[$j]['id']."><span>".$w[$j]['name']."</span></div>";
                    }
                }elseif($z==1){
                    $text['time']="";
                    for($a=0;$a<count($w);$a++){
                        $text['time'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='play' value=".$w[$a]['id']."><span>".$w[$a]['name']."</span></div>";
                    }
                }elseif($z==2){
                    $text['play']="";
                    for($s=0;$s<count($w);$s++){
                        $text['play'] .="<div class='form-div-checkbox'><input type='checkbox' class='form-checkbox nature' name='nature[]' value=".$w[$s]['id']."><span>".$w[$s]['name']."</span></div>";
                    }
                }elseif($z==3){
                    $text['age']="";
                    for($d=0;$d<count($w);$d++){
                        $text['age'] .="<div class='form-div-checkbox'><input type='radio' class='form-checkbox' name='age' value=".$w[$d]['id']."><span>".$w[$d]['name']."</span></div>";
                    }
                }
            }
        }
        return json($text);
    }

    public function manga_conform(){
        $text=input('post.');
        if($text['text']==''){
            $data['msg']=0;
            return json($data);
        }
        $title ='%'.$text['text'].'%';
        $video=Db::table('manga')->where('title','like',$title)->select();
        $html="";
        for($i=0;$i<count($video);$i++){
            if($i==0){
                $html="该作品已有相关投稿：<a href='/manga/".$video[$i]['manga_id']."' target='_blank'>".$video[$i]['title']."</a>";
            }else{
                $html=$html."<a href='/manga/".$video[$i]['manga_id']."' target='_blank'>".$video[$i]['title']."</a>";
            }
        }
        if(count($video)>0){
            $data['text']=$html;
            $data['msg']=1;
            return json($data);
        }else{
            $data['msg']=0;
            return json($data);
        }
    }

    public function article_conform(){
        $text=input('post.');
        if($text['text']==''){
            $data['msg']=0;
            return json($data);
        }
        $title ='%'.$text['text'].'%';
        $video=Db::table('manga_article')->where('manga_article_title','like',$title)->select();
        $html="";
        for($i=0;$i<count($video);$i++){
            if($i==0){
                $html="该作品已有相关投稿：<a href='/articles_list/".$video[$i]['manga_article_id']."' target='_blank'>".$video[$i]['manga_article_title']."</a>";
            }else{
                $html=$html."<a href='/articles_list/".$video[$i]['manga_article_id']."' target='_blank'>".$video[$i]['manga_article_title']."</a>";
            }
        }
        if(count($video)>0){
            $data['text']=$html;
            $data['msg']=1;
            return json($data);
        }else{
            $data['msg']=0;
            return json($data);
        }
    }

    public function works_form(){
        $data=input('post.');
        if (!empty($data)) {
            $array = array(
                'mange_part_user',
                'mange_part_name',
                'mange_part_text',
                'mange_part_level',
                'mange_part_img',
                'mange_part_line',
                'mange_part_time'
            );
            for ($i = 0; $i < count($array); $i++) {
                if ($array[$i] == 'mange_part_user') {
                    $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                } elseif ($array[$i] == 'mange_part_time') {
                    $form[$array[$i]] = time();
                } elseif ($array[$i] == 'mange_part_text') {
                    if (!empty($data[$array[$i]])) {
                        $form[$array[$i]] = $data[$array[$i]];
                    }else{
                        $form[$array[$i]]='未知描述';
                    }
                }elseif($array[$i]=='mange_part_img') {
                    $form[$array[$i]] =$data[$array[$i]];
                }else{
                    $form[$array[$i]] = $this->infusion($array[$i], $data);
                }
            }
            $form['mange_part_static']=0;
            if($data['number']==1){
                $table="mange_part";
            }elseif($data['number']==3){
                $table="mange_part_copy";
            }
            $form=Db::table($table)->where('mange_part_id',$data['mange_part_id'])->update($form);
                if ($form) {
                    $news=array(
                        'user_id'=>$data['mange_part_user'],
                        'uid'=>1,
                        'new_classify'=>1,
                        'static'=>0,
                        'new_text'=>$data['mange_part_name']."正在等待审核！",
                        'time'=>time()
                    );
                    Db::table('news_new')->insert($news);
                    return $this->create_skip("已上交投稿，请等待审核!", "create.html?url=article","投稿管理","success");
                } else {
                    return $this->create_skip("投稿提交失败！请重新投稿", "create.html?url=article","投稿管理","error");
                }
        } else {
            abort(404);
        }
    }

    public function works_theme_form(){
        $data=input('post.');
        if (!empty($data)) {
            $array = array(
                'user_id',
                'title',
                'page',
                'img',
                'downClass',
                'downPass',
                'downLine',
                'manga_text',
                'manga_time'
            );
            for ($i = 0; $i < count($array); $i++) {
                if ($array[$i] == 'user_id') {
                    $form[$array[$i]] = $this->infusion($array[$i], $data, 'number');
                } elseif ($array[$i] == 'manga_time') {
                    $form[$array[$i]] = time();
                } elseif ($array[$i] == 'manga_text') {
                    if (!empty($data[$array[$i]])) {
                        $form[$array[$i]] = $data[$array[$i]];
                    }else{
                        $form[$array[$i]]='未知描述';
                    }
                }elseif($array[$i]=='img') {
                    $form[$array[$i]] =$data[$array[$i]];
                }else{
                    $form[$array[$i]] = $this->infusion($array[$i], $data);
                }
            }
            $form['manga_static']=0;
            if($data['number']==2){
                $table="mange_theme";
            }elseif($data['number']==4){
                $table="mange_theme_copy";
            }
            $form=Db::table($table)->where('manga_id',$data['manga_id'])->update($form);
            if ($form) {
                $news=array(
                    'user_id'=>$data['user_id'],
                    'uid'=>1,
                    'new_classify'=>1,
                    'static'=>0,
                    'new_text'=>$data['title']."正在等待审核！",
                    'time'=>time()
                );
                Db::table('news_new')->insert($news);
                return $this->create_skip("已上交投稿，请等待审核!", "create.html?url=article","投稿管理","success");
            } else {
                return $this->create_skip("投稿提交失败！请重新投稿", "create.html?url=article","投稿管理","error");
            }
        } else {
            abort(404);
        }
    }

    public function works_collect_form(){
        $data=input('post.');
        $array1 = array(
            'Collect',
            'Line',
        );
        $num = count($data[$array1[0]]);
        for ($j = 0; $j < $num; $j++) {
            $video[$j]['Uid'] = $data['manga_id'];
            $video[$j]['part_time'] = time();
            $video[$j]['part_static']=0;
            for ($i = 0; $i < count($array1); $i++) {
                $video[$j][$array1[$i]] = $data[$array1[$i]][$j];
                $video[$j][$array1[$i]] = $this->infusion($array1[$i], $video[$j]);
            }
            $form=Db::table('manga_parts')->insert($video[$j]);
        }
        if ($form) {
            $news=array(
                'user_id'=>$data['user_id'],
                'uid'=>1,
                'new_classify'=>1,
                'static'=>0,
                'new_text'=>$data['title']."正在等待审核！",
                'time'=>time()
            );
            Db::table('news_new')->insert($news);
            return $this->create_skip("已上交投稿，请等待审核!", "create.html?url=article","投稿管理","success");
        } else {
            return $this->create_skip("投稿提交失败！请重新投稿", "create.html?url=article","投稿管理","error");
        }
    }

}