<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
class System extends Commons
{
    public function system_module(){
        $data=Db::table('system_category')->paginate(10);
        $page=$data->render();
        $number=Db::table('system_category')->count();
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function system_module_add(){
        $li=Db::table('system_category')->where('cid',0)->select();
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function system_module_form(){
        $data=input('post.');
        $form=Db::table('system_category')->insert($data);
        if($form){
            return $this->success('模块添加成功！','system_module');
        }else{
            return $this->error('模块添加失败！','system_module');
        }
    }

    public function system_module_update(){
        $data=input('get.');
        $form=Db::table('system_category')->where('id',$data['id'])->find();
        $li=Db::table('system_category')->select();
        $this->assign('form',$form);
        $this->assign('li',$li);
        return $this->fetch();
    }

    public function module_update(){
        $data=input('post.');
        $form=Db::table('system_category')->where('id',$data['id'])->update($data);
        if($form){
            return $this->success('模块编辑成功！','system_module');
        }else{
            return $this->error('模块编辑失败！','system_module');
        }
    }

    public function module_del(){
        $data=input('post.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('system_category')->where('id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","system_module");
            }else{
                return $this->error("批量删除失败","system_module");
            }
        }
        $form=Db::table('system_category')->where('id',$data['id'])->delete();
        if(!$form){
            $data='删除成功！';
            return json($data);
        }else{
            $data='删除失败！';
            return json($data);
        }
    }

    public function system_money(){
        $data=Db::table('system_money')->paginate(10);
        $page=$data->render();
        $number=Db::table('system_money')->count();
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('number',$number);
        return $this->fetch();
    }

    public function system_money_add(){
        return $this->fetch();
    }

    public function system_capital_add(){
        return $this->fetch();
    }

    public function system_money_form(){
        $data=input('post.');
        $data['money_time']=time();
        $form=Db::table('system_money')->insert($data);
        if($form){
            return $this->success('添加资源类型成功！','system_money');
        }else{
            return $this->error('添加资源类型失败！','system_money');
        }
    }

    public function system_capital_form(){
        $data=input('post.');
        $data['system_capital_time']=time();
        $form=Db::table('system_capital')->insert($data);
        if($form){
            return $this->success('添加资金成功！','system_capital');
        }else{
            return $this->error('添加资金失败！','system_capital');
        }
    }

    public function system_money_static(){
        $data=input('post.');
        $form=Db::table('system_money')->where('money_id',$data['id'])->setField('money_static',$data['static']);
        if($form){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function system_capital_static(){
        $data=input('post.');
        $num=Db::table('system_capital')->where('system_capital_id',$data['id'])->find();
        $money=Db::table('system_money')->where('money_id',1)->find();
        if($data['static']==1){
            $where=array(
                'money_number'=>$money['money_number']+$num['system_capital_data'],
                'money_time'=>time()
            );
            Db::table('system_money')->where('money_id',1)->update($where);
        }else{
            $where=array(
                'money_number'=>$money['money_number']-$num['system_capital_data'],
                'money_time'=>time()
            );
            Db::table('system_money')->where('money_id',1)->update($where);
        }
        $form=Db::table('system_capital')->where('system_capital_id',$data['id'])->setField('system_capital_wu',$data['static']);
        if($form){
            return json(1);
        }else{
            return json(0);
        }
    }

    public function system_money_update(){
        $data=input('get.');
        $form=Db::table('system_money')->where('money_id',$data['id'])->find();
        $this->assign('form',$form);
        return $this->fetch();
    }

    public function money_update(){
        $data=input('post.');
        $data['money_time']=time();
        $form=Db::table('system_money')->where('money_id',$data['money_id'])->update($data);
        if($form){
            return $this->success('修改资源类型成功！','system_money');
        }else{
            return $this->error('修改资源类型失败！','system_money');
        }
    }

    public function system_money_del(){
        $data=input('post.');
        $da=input('get.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('system_money')->where('money_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","system_money");
            }else{
                return $this->error("批量删除失败","system_money");
            }
        }
        $da=Db::table('system_money')->where('money_id',$da['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function system_capital_del(){
        $data=input('post.');
        $da=input('get.');
        if(!empty($data['item'])){
            for($i=0;$i<count($data['item']);$i++){
                $fo=Db::table('system_capital')->where('system_capital_id',$data['item'][$i])->delete();
            }
            if($fo){
                return $this->success("批量删除成功","system_capital");
            }else{
                return $this->error("批量删除失败","system_capital");
            }
        }
        $da=Db::table('system_capital')->where('system_capital_id',$da['id'])->delete();
        if($da){
            return json('n');
        }else{
            return json('j');
        }
    }

    public function system_capital(){
        $data=Db::table('system_capital')->paginate(10);
        $page=$data->render();
        $number=Db::table('system_capital')->count();
        $this->assign('data',$data);
        $this->assign('page',$page);
        $this->assign('number',$number);
        return $this->fetch();
    }
}
?>

