<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Article extends Comment{
     public function article_list(){
         $data=input('article');
         $this->header_nav();
         $this->nav_login();
         $user=Session::get('users');
         $video=Db::table('manga')
             ->alias('v')
             ->where('v.manga_id',$data)
             ->where('v.manga_static',1)
             ->join('member m','v.user_id=m.id')
             ->join('video_nav n','n.id=v.page')
             ->find();
         if(!$video){
             abort(404);
         }
         $article=Db::table('manga_article')
             ->alias('m')
             ->join('member b','b.id=m.manga_article_member')
             ->where('m.manga_article_manga',$data)
             ->order('m.manga_article_time desc')
             ->paginate(50);
         $page=$article->render();
         $this->assign('page',$page);
         $video['number']=count(Db::table('manga_article')->where('manga_article_manga',$data)->select());
         $video['numbers']=1;
         $this->assign('article',$article);
         $article_member=Db::table('manga_article')->where('manga_article_member',$user)->where('manga_article_manga',$data)->select();
         if($article_member){
             $video['member_number']=count($article_member);
         }else{
             $video['member_number']=0;
         }
         $this->assign('vi',$video);
         return $this->fetch();
     }

    public function articles_list(){
        $data=input('articles');
        $this->header_nav();
        $this->nav_login();
        $article=Db::table('manga_article')->alias('v')
            ->join('member m','v.manga_article_member=m.id')
            ->where('v.manga_article_id',$data)
            ->find();
        if(!$article){
            abort(404);
        }
        $video=Db::table('manga')
            ->alias('v')
            ->where('v.manga_id',$article['manga_article_manga'])
            ->find();
        $this->assign('article',$article);
        $this->assign('vi',$video);
        return $this->fetch();
    }

    public function essay_list(){
        $data=input('article');
        $this->header_nav();
        $this->nav_login();
        $user=Session::get('users');
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$data)
            ->where('v.mange_theme_static',1)
            ->join('member m','v.mange_theme_user=m.id')
            ->join('video_nav n','n.id=v.mange_theme_page')
            ->find();
        if(!$video){
            abort(404);
        }
        $article=Db::table('mange_article')
            ->alias('m')
            ->join('member b','b.id=m.mange_article_member')
            ->where('m.mange_article_mange',$data)
            ->order('m.mange_article_time desc')
            ->paginate(50);
        $page=$article->render();
        $this->assign('page',$page);
        $video['number']=count(Db::table('mange_article')->where('mange_article_mange',$data)->select());
        $video['numbers']=1;
        $this->assign('article',$article);
        $article_member=Db::table('mange_article')->where('mange_article_member',$user)->where('mange_article_mange',$data)->select();
        if($article_member){
            $video['member_number']=count($article_member);
        }else{
            $video['member_number']=0;
        }
        $this->assign('vi',$video);
        return $this->fetch();
    }

    public function essays_list(){
        $data=input('articles');
        $this->header_nav();
        $this->nav_login();
        $article=Db::table('mange_article')->alias('v')
            ->join('member m','v.mange_article_member=m.id')
            ->where('v.mange_article_id',$data)
            ->find();
        if(!$article){
            abort(404);
        }
        $video=Db::table('mange_theme')
            ->alias('v')
            ->where('v.mange_theme_id',$article['mange_article_mange'])
            ->find();
        $this->assign('article',$article);
        $this->assign('vi',$video);
        return $this->fetch();
    }
}