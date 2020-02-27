<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class Goods extends Commons
{
    public function manga_list(){
        $data=Db::table('manga')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function mange_list(){
        $data=Db::table('manga')->where('manga_static',0)->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function manges_static(){
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                if(!Db::table('manga')->where('manga_id', $item['item'][$j])->where('manga_static',1)->find()){
                    Db::table('manga')->where('manga_id', $item['item'][$j])->setInc('manga_static',1);
                }
                $c = Db::table('manga_part')->where('UId',$item['item'][$j] )->select();
                if ($c == 0) {
                    return $this->success("批量审核成功", "mange_list");
                } else {
                    for ($i = 0; $i < count($c); $i++) {
                        if(!Db::table('manga_part')->where('manga_part_id', $c[$i]['manga_part_id'])->where('part_static',1)->find()){
                            $x = Db::table('manga_part')->where('manga_part_id', $c[$i]['manga_part_id'])->setInc('part_static',1);
                        }else{
                            $x=1;
                        }
                    }
                }
            }
            if(!empty($x)){
                $x=1;
            }
            if($x){
                return $this->success("批量审核成功","mange_list");
            }else{
                return $this->error("批量审核失败","mange_list");
            }
        }
    }

    public function manga_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $v = Db::table('manga')->where('manga_id', $item['item'][$j])->find();
                Db::table('manga')->where('manga_id', $item['item'][$j])->delete();
                $c = Db::table('manga_part')->where('UId', $v['manga_id'])->select();
                if ($c == 0) {
                    return $this->success("批量删除成功", "manga_list");
                } else {
                    for ($i = 0; $i < count($c); $i++) {
                        $x = Db::table('manga_part')->where('manga_part_id', $c[$i]['manga_part_id'])->delete();
                    }
                }
            }
            if(!empty($x)){
                $x=1;
            }
            if($x){
                return $this->success("批量删除成功","manga_list");
            }else{
                return $this->error("批量删除失败","manga_list");
            }
        }
        $v=Db::table('manga')->where('manga_id',$data['id'])->find();
        Db::table('manga')->where('manga_id',$data['id'])->delete();
        $c=Db::table('manga_part')->where('UId',$v['manga_id'])->select();
        if($c==0){
            return json('n');
        }else{
            for($i=0;$i<count($c);$i++){
                $x=Db::table('manga_part')->where('manga_part_id',$c[$i]['manga_part_id'])->delete();
            }
        }
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function manga_show(){
        $data=input('get.');
        $user=Db::table('manga')
            ->alias('v')
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->join('video_nav s','s.id=v.single')
            ->join('video_nav r','r.id=v.region')
            ->join('video_nav a','a.id=v.play')
            ->join('video_nav b','b.id=v.age')
            ->where('v.manga_id',$data['id'])->field('
            n.name as page,
            m.name as user_id,
            s.name as single,
            v.title,
            v.img,
            r.name as region,
            a.name as play,
            v.nature,
            b.name as age,
            v.manga_time,
        v.manga_text')->find();
        $title ='%'.$user['title'].'%';
        $video=Db::table('manga_copy')->where('title','like',$title)->select();
        $this->assign('video',$video);
        $this->assign('user',$user);
        return $this->fetch();
    }

    public function manga_collects_list(){
        $item=input('get.');
        $data=Db::table('manga_part')->alias('c')->join('manga v','v.manga_id=c.Uid')->where('c.Uid',$item['id'])->paginate(10,false,[
            'query'=>array('id'=>$item['id'])
        ]);
        $this->assign('item',$item['id']);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function manga_collects_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($i=0;$i<count($item['item']);$i++){
                $fo=Db::table('manga_part')->where('manga_part_id',$item['item'][$i])->delete();
            }
            $c=Db::table('manga_part')->where('Uid',$item['Uid'])->select();
            if(0==count($c)){
                Db::table('manga')->where('manga_id',$item['Uid'])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","manga_list");
            }else{
                return $this->error("批量删除失败","manga_lsit");
            }
        }
        $fo=Db::table('manga_part')->where('manga_part_id',$data['id'])->delete();
        $c=Db::table('manga_part')->where('Uid',$data['Uid'])->select();
        if(0==count($c)){
            Db::table('manga')->where('manga_id',$data['Uid'])->delete();
        }
        if($fo){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function manga_part_static(){
        $data=input('post.');
        $form=Db::table('manga_part')->where('manga_part_id',$data['id'])->setField('part_static',$data['static']);
        if($form){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function manga_new_list(){
        $data=Db::table('manga_copy')->paginate(10);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function manga_new_show(){
        $data=input('get.');
        $user=Db::table('manga_copy')
            ->alias('v')
            ->join('member m','v.user_id=m.id')
            ->join('video_nav n','n.id=v.page')
            ->join('video_nav s','s.id=v.single')
            ->join('video_nav r','r.id=v.region')
            ->join('video_nav a','a.id=v.play')
            ->join('video_nav b','b.id=v.age')
            ->where('v.manga_id',$data['id'])->field('
            n.name as page,
            m.name as user_id,
            s.name as single,
            v.title,
            v.img,
            r.name as region,
            a.name as play,
            v.nature,
            b.name as age,
            v.manga_time,
        v.manga_text')->find();
        $title ='%'.$user['title'].'%';
        $video=Db::table('manga')->where('title','like',$title)->select();
        $this->assign('video',$video);
        $this->assign('user',$user);
        return $this->fetch();
    }

    public function manga_collect_show(){
        $data=input('get.');
        $user=Db::table('manga_part_copy')
            ->alias('v')
            ->where('v.Uid',$data['id'])->select();
        $this->assign('user',$user);
        return $this->fetch();
    }

    public function manga_new_del(){
        $data=input('get.');
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $v = Db::table('manga_copy')->where('manga_id', $item['item'][$j])->find();
                Db::table('manga_copy')->where('manga_id', $item['item'][$j])->delete();
                $c = Db::table('manga_part_copy')->where('UId', $v['manga_id'])->select();
                if (count($c) == 0) {
                    return $this->success("批量删除成功", "manga_new_list");
                } else {
                    for ($i = 0; $i < count($c); $i++) {
                        $x = Db::table('manga_part_copy')->where('manga_part_id', $c[$i]['manga_part_id'])->delete();
                    }
                }
            }
            if(!empty($x)){
                $x=1;
            }else{
                $x=0;
            }
            if($x){
                return $this->success("批量删除成功","manga_new_list");
            }else{
                return $this->error("批量删除失败","manga_new_list");
            }
        }
        $v=Db::table('manga_copy')->where('manga_id',$data['id'])->find();
        Db::table('manga_copy')->where('manga_id',$data['id'])->delete();
        $c=Db::table('manga_part_copy')->where('UId',$v['manga_id'])->select();
        if(count($c)==0){
            return json('n');
        }else{
            for($i=0;$i<count($c);$i++){
                $x=Db::table('manga_part_copy')->where('manga_part_id',$c[$i]['manga_part_id'])->delete();
            }
        }
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function manga_two_list(){
        $data=Db::table('manga_parts')->paginate(100);
        $page=$data->render();
        $this->assign('page',$page);
        $this->assign('data',$data);
        return $this->fetch();
    }

    public function manga_new_static(){
        $data=input('post.');
        if(!empty($data)){
            $video=Db::table('manga_copy')->where('manga_id',$data['id'])->field('title,page,single,region,play,nature,user_id,manga_time,img,age,manga_text')->find();
            $id=Db::table('manga')->where('title','like','%'.$video['title'].'%')->find();
            $title=Db::table('manga')->where('title',$video['title'])->find();
            if($id&&$title){
                $id=$id['manga_id'];
            }else{
                Db::table('manga')->insert($video);
                $id=Db::table('manga')->getLastInsID();
                Db::table('taobao')->insert(array('taobao_manga'=>$id));
            }
            Db::table('manga_copy')->where('manga_id',$data['id'])->delete();
            $collect=Db::table('manga_part_copy')->where('Uid',$data['id'])->select();
            for($i=0;$i<count($collect);$i++){
                    $co['part_time']=time();
                    $co['Uid']=$id;
                    $co['part_static']=1;
                    $co['Collect']=$collect[$i]['Collect'];
                    $co['Line']=$collect[$i]['Line'];
                    Db::table('manga_part')->insert($co);
                    Db::table('manga_part_copy')->where('manga_part_id',$collect[$i]['manga_part_id'])->delete();
            }
            if($id){
                return json('n');
            }else{
                return json('j');
            };
        }

    }

    public function manga_two_static(){
        $item=input('post.');
        if(!empty($item)){
            for($j=0;$j<count($item['item']);$j++) {
                $c = Db::table('manga_parts')->where('manga_part_id',$item['item'][$j])->find();
                $where=array(
                    'Collect'=>$c['Collect'],
                    'Uid'=>$c['Uid'],
                    'part_time'=>time(),
                    'Line'=>$c['Line'],
                    'part_static'=>1
                );
                Db::table('manga_part')->insert($where);
                $where1=array(
                    'manga_time'=>time()
                );
                Db::table('manga')->where('manga_id',$c['Uid'])->update($where1);
                $c = Db::table('manga_parts')->where('manga_part_id',$item['item'][$j])->delete();
            }
            if($c){
                return $this->success("批量审核成功","manga_two_list");
            }else{
                return $this->error("批量审核失败","manga_two_list");
            }
        }
    }

    public function manga_two_del(){
        $data=input('get.');
        $x=Db::table('manga_parts')->where('manga_part_id',$data['id'])->delete();
        if($x){
            return json('n');
        }else{
            return json('j');
        }
    }
}