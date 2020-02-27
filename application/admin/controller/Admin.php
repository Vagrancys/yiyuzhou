<?php
namespace app\admin\controller;
use think\Controller;
use think\Db;
use think\Request;
use think\Session;
class Admin extends Commons
{
    public function admin()
    {
        $dat=Session::get('user');
        $form=Db::table('system_category')->where('cid',0)->select();
        if (!($dat == 1)) {
            $group = Db::table('auth_group_access')->where('uid', $dat)->find();
            $auth = Db::table('auth_group')->where('id', $group['group_id'])->find();
            $power = explode(",", $auth['item']);
            for ($z = 0; $z < count($power); $z++) {
                $power[$z] = Db::table('auth_power')->where('id', $power[$z])->find();
            }
            for ($i = 0; $i < count($form); $i++) {
                $form[$i]['data'] = Db::table('system_category')->where('cid', $form[$i]['id'])->select();
                if (!count($form[$i]['data']) == 0) {
                    foreach ($form[$i]['data'] as $key=>$v1) {
                        foreach($power as $key2=>$v2){
                            if($v1['module_name'] == $v2['title']){
                                unset($form[$i]['data'][$key]);
                            }
                        }
                    }
                    if (count($form[$i]['data']) == 0) {
                    }else{
                        $admin[$i] = $form[$i];
                    }
                }
            }
        } else {
            for ($i = 0; $i < count($form); $i++) {
                $form[$i]['data'] = Db::table('system_category')->where('cid', $form[$i]['id'])->select();
            }
            $admin = $form;
        }
        $data=Session::get('admin_name');
        $this->assign('data',$data);
        $this->assign('dat',$dat);
        $this->assign('form',$admin);
        return $this->fetch();
    }

    public function welcome(){
        $array=array(
            'number',
            'examine'
        );
        $array1=array(
            'zi',
            'video',
            'member',
            'news_comment',
            'admin_static',
            'admin_user'
        );

        for($i=0;$i<count($array1);$i++){
            if($array1[$i]=="zi"){
                $video[0][$i]['number']="总数";
                $video[1][$i]['number']="待审核";
            }elseif($array1[$i]=="video"){
                $video[0][$i]['number']=count(Db::table('manga')->select());
                $video[1][$i]['number']=count(Db::table('manga')->where('manga_static',1)->select());
            }else{
                $video[0][$i]['number']=count(Db::table($array1[$i])->select());
                $video[1][$i]['number']=0;
            }
        }
        $info = array(
            '操作系统'=>PHP_OS,
            '运行环境'=>$_SERVER["SERVER_SOFTWARE"],
            '主机名'=>$_SERVER['SERVER_NAME'],
            'WEB服务端口'=>$_SERVER['SERVER_PORT'],
            '网站文档目录'=>$_SERVER["DOCUMENT_ROOT"],
            '浏览器信息'=>substr($_SERVER['HTTP_USER_AGENT'], 0, 40),
            '通信协议'=>$_SERVER['SERVER_PROTOCOL'],
            '请求方法'=>$_SERVER['REQUEST_METHOD'],
            'ThinkPHP版本'=>THINK_VERSION,
            '服务器解译引擎'=>$_SERVER['SERVER_SOFTWARE'],
            '上传附件限制'=>ini_get('upload_max_filesize'),
            '执行时间限制'=>ini_get('max_execution_time').'秒',
            '服务器时间'=>date("Y年n月j日 H:i:s"),
            '北京时间'=>gmdate("Y年n月j日 H:i:s",time()+8*3600),
            '服务器域名/IP'=>$_SERVER['SERVER_NAME'].' [ '.gethostbyname($_SERVER['SERVER_NAME']).' ]',
            '服务器的IP地址'=>$_SERVER['REMOTE_ADDR'],
            '剩余空间'=>round((disk_free_space(".")/(1024*1024)),2).'M',
        );
        $this->assign('info',$info);
        $static=Db::table('admin_static')->where('admin_id',Session::get('user'))->find();
        $this->assign("static",$static);
        $this->assign("video",$video);
        return $this->fetch();
    }
}