<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;

class Ranking extends Comment
{
    public function ranking(){
        $data=input('get.');
        if(!empty($data)){
            if(!array_key_exists('page',$data)){
                if(!array_key_exists('total',$data)){
                    $where=array(
                        'v.manga_static'=>1
                    );
                }
            }
            if(array_key_exists('total',$data)){
                if(!empty($data['total'])){
                    $where=array(
                        'v.manga_static'=>1
                    );
                }
            }
            if(array_key_exists('page',$data)){
                if(empty($data['page'])){
                    $where=array(
                        'v.manga_static'=>1
                    );
                    $page=array(
                        'page'=>""
                    );
                }else{
                    $where=array(
                        'v.page'=>(int)$data['page'],
                        'v.manga_static'=>1
                    );
                    $page=array(
                        'page'=>$data['page']
                    );
                }
            }
            if(array_key_exists('honor',$data)){
                $where=array(
                    'v.page'=>$data['page'],
                    'v.manga_static'=>1,
                    'd.honor'=>['>',$data['honor']]
                );
                $page=array(
                    'page'=>$data['page']
                );
            }

        }else{
            $where=array(
                'v.manga_static'=>1
            );
            $page=array(
                'page'=>""
            );;
        }
        $this->nav_login();
        $this->header_nav();
        $this->ranking_nav(24);
        $rank=Db::table('manga')
            ->alias('v')
            ->join('member m','m.id=v.user_id')
            ->where($where)
            ->order('v.manga_time desc')->limit(500)->paginate(50,false,[
                'query'=>$page,
                'var_page' => 'pages'
            ]);
        $rank->toArray();
        foreach($rank as $k=>$v){
            $n=Db::table('level_date')->where('level_date_manga',$v['manga_id'])->find();
            if($n['level_date_click']>10000&&$n['level_date_click']<100000000){
                $n['level_date_click']=intval($n['level_date_click']/10000)."万";
            }elseif($n['level_date_click']>100000000){
                $n['level_date_click']=intval($n['level_date_click']/100000000)."亿";
            }
            $v['clicks']=$n['level_date_click'];
            $v['honor']=$n['level_date_honor'];
            $v['channel']=Db::table('manga_collect')->where('manga_collect_manga',$v['manga_id'])->count();
            $rank->offsetSet($k,$v);
        }
        $page=$rank->render();
        if(!array_key_exists('total',$data)){
            $data['total']=25;
        }
        if(!array_key_exists('page',$data)){
            $data['page']="";
        }
        if(array_key_exists('pages',$data)){
            if($data['pages']==1){
                $number=0;
            }else{
                $number=$data['pages']-1;
            }
            $number=$number*50;
        }else{
            $number=0;
        }
        $this->assign('pa',$data);
        $this->assign('page',$page);
        $this->assign('ras',$rank);
        $this->assign('number',$number);
        return $this->fetch();
    }
}