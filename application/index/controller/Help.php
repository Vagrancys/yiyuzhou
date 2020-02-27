<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Help extends Comment
{
    public function help(){
        $this->header_nav();
        $this->nav_login();
        $list=Db::table('help_classify')->where('helpUid',0)->select();
        for($i=0;$i<count($list);$i++){
            $list[$i]['Uid']=Db::table('help_classify')->where('helpUid',$list[$i]['id'])->select();
            if(!empty($list[$i]['Uid'])){
                $list[$i]['id']=$list[$i]['Uid'][0]['id'];
            }
        }
        $help=Db::table('help_classify')
            ->alias('h')->where('h.id',9)
            ->join('help_content c','h.id=c.classifyId')
            ->select();
        for($i=0;$i<count($help);$i++){
            $help[$i]['text']=html_entity_decode($help[$i]['text']);
        }
        $num="1";
        $this->assign('num',$num);
        $this->assign('list',$list);
        $this->assign('help',$help);
        return $this->fetch();
    }

    public function help_ajax(){
        $data=input('post.');
        $help=Db::table('help_classify')
            ->alias('h')->where('h.id',$data['id'])
            ->join('help_content c','h.id=c.classifyId')
            ->select();
        for($i=0;$i<count($help);$i++){
            $help[$i]['text']=html_entity_decode($help[$i]['text']);
        }
        $Help['title']=$help[0]['name'];
        $Help['text']="";
        for($i=0;$i<count($help);$i++){
            $j=0;
            $Help['text'] .="
                <div class='help-title-wrap'>
                    <div class='help-subtitle'>
                        <div class='help-arrow'></div>
                            <span class='help-subindex'>".++$j."</span>
                            ".$help[$i]['title']."
                        </div>
                    <div class='help-subcontent'>
                        ".$help[$i]['text']."
                    </div>
                </div>";
        }
        return json($Help);
    }

    public function help_list_ajax(){
        $data=input('post.');
        $help=Db::table('help_classify')
            ->alias('h')->where('h.id',$data['id'])
            ->join('help_content c','h.id=c.classifyId')
            ->select();
        for($i=0;$i<count($help);$i++){
            $help[$i]['text']=html_entity_decode($help[$i]['text']);
        }
        $Help['title']=$help[0]['name'];
        $Help['text']="";
        for($i=0;$i<count($help);$i++){
            $j=0;
            $Help['text'] .="
                <div class='help-title-wrap'>
                    <div class='help-subtitle'>
                        <div class='help-arrow'></div>
                            <span class='help-subindex'>".++$j."</span>
                            ".$help[$i]['title']."
                        </div>
                    <div class='help-subcontent'>
                        ".$help[$i]['text']."
                    </div>
                </div>";
        }
        return json($Help);
    }

    public function article(){
        $this->header_nav();
        $this->nav_login();
        $article=Db::table('article')
            ->alias('a')
            ->join('article_column c','a.fid=c.id')
            ->join('article_type t','t.id=a.lid')
            ->where('a.status',1)
            ->field('a.article_id,a.title,t.name fid,c.name lid,a.article_time')
            ->paginate(10);
        $page=$article->render();
        $this->assign('article',$article);
        $this->assign('page',$page);
        return $this->fetch();
    }

    public function articles(){
        $data=input('id');
        $this->header_nav();
        $this->nav_login();
        $article=Db::table('article')->alias('a')
            ->join('article_column c','a.fid=c.id')
            ->join('article_type t','t.id=a.lid')
            ->where('a.article_id',$data)
            ->where('a.status',1)
            ->field('a.article_id,a.title,t.name fid,c.name lid,a.article_time,a.brief,a.number,a.keywords,a.abstract,a.editorValue')
            ->find();
        if(!$article){
            $text="该公告不见了！";
            $this->assign('text',$text);
            return $this->fetch('comment/404');
        }
        Db::table('article')->where('article_id',$article['article_id'])->setInc('number',1);
        $this->assign('article',$article);
        return $this->fetch();
    }
}