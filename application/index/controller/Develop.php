<?php
namespace app\index\controller;
use think\Controller;
use think\Db;
use think\Session;
use think\Request;
class Develop extends Comment{
    public function develop(){
        $this->nav_login();
        $this->header_nav();
        $develop_text=Db::table('develop_text')->select();
        $develop_data=Db::table('develop_data_header')->select();
        $user=Session::get("users");
        for($i=0;$i<count($develop_data);$i++){
            $develop_data[$i]['central']=Db::table('develop_data_central')->where('develop_data_central_uid',$develop_data[$i]['develop_data_header_id'])->select();
        }
        $develop_function=Db::table('develop_function')->where('develop_function_uid',0)->select();
        for($j=0;$j<count($develop_function);$j++){
            $develop_function[$j]['list']=Db::table('develop_function')->where('develop_function_uid',$develop_function[$j]['develop_function_id'])->select();
            for($z=0;$z<count($develop_function[$j]['list']);$z++){
                $develop_function[$j]['list'][$z]['comment']=Db::table("develop_comment")->where('develop_comment_uid',$develop_function[$j]['list'][$z]['develop_function_id'])->select();
                for($x=0;$x<count($develop_function[$j]['list'][$z]['comment']);$x++){
                    $member=Db::table('member')->where('id',$develop_function[$j]['list'][$z]['comment'][$x]['develop_comment_user'])->find();
                    $level=Db::table('level_class')->where('level_class_id',$member['level'])->find();
                    $supports=Db::table('develop_support')->where('develop_support_user',$user)->where('develop_support_nid',$develop_function[$j]['list'][$z]['comment'][$x]['develop_comment_id'])->find();
                    if($supports){
                        $develop_function[$j]['list'][$z]['comment'][$x]['support_static']=1;
                    }else{
                        $develop_function[$j]['list'][$z]['comment'][$x]['support_static']=0;
                    }
                    $develop_function[$j]['list'][$z]['comment'][$x]['comment_name']=$member["name"];
                    $develop_function[$j]['list'][$z]['comment'][$x]['comment_level']=$level['level_class_name'];
                }
            }
            $level_name=Db::table('level_class')->where('level_class_id',$develop_function[$j]['develop_function_level'])->find();
            $develop_function[$j]['level_name']=$level_name['level_class_name'];
            $develop_function[$j]['praise_num']="width:".(($develop_function[$j]['develop_function_praise']/$develop_function[$j]['develop_function_praise_up'])*100)."%";
            if($user){
                $develop_function[$j]['login_static']=1;
                $support=Db::table('develop_support')->where('develop_support_user',$user)->where('develop_support_uid',$develop_function[$j]['develop_function_id'])->find();
                if($support){
                    $develop_function[$j]['support_static']=1;
                }else{
                    $develop_function[$j]['support_static']=0;
                }
            }else{
                $develop_function[$j]['login_static']=0;
                $develop_function[$j]['support_static']=0;
            }
        }
        $this->assign("develop_function",$develop_function);
        $this->assign("develop_data",$develop_data);
        $this->assign("develop_text",$develop_text);
        return $this->fetch();
    }

    public function develop_support_ajax(){
        $data=input('post.');
        $user=Session::get("users");
        $text['num']=0;
        if($user){
            if($data['val']==1) {
                $if = Db::table('develop_support')->insert(array('develop_support_uid' => $data['id'], 'develop_support_user' => $user, 'develop_support_time' => time()));
                $function=Db::table('develop_function')->where('develop_function_id',$data['id'])->find();
                if($function['develop_function_praise']+1>$function['develop_function_praise_up']){
                    $where=array(
                        'develop_function_praise_up'=>$function['develop_function_praise_up']."0",
                        'develop_function_level'=>$function['develop_function_level']+1,
                        'develop_function_praise'=>$function['develop_function_praise']+1
                    );
                }else{
                    $where=array(
                        'develop_function_praise_up'=>$function['develop_function_praise_up'],
                        'develop_function_praise'=>$function['develop_function_praise']+1,
                         'develop_function_level'=>$function['develop_function_level']
                    );
                }
                $level=Db::table('level_class')->where('level_class_id',$where['develop_function_level'])->find();
                $text['level']=$level['level_class_name']."级";
                $text['width']=(($where['develop_function_praise']/$where['develop_function_praise_up'])*100)."%";
                $text['data']=$where['develop_function_praise']."/".$where['develop_function_praise_up'];
                Db::table('develop_function')->where('develop_function_id',$data['id'])->update($where);
                $text['text'] = " <button class='develop-level-support'>已支持</button>";
            }
            if($if){
                $text['num']=1;
                return json($text);
            }else{
                return json($text);
            }
        }else{
            return json($text);
        }
    }

    public function develop_list(){
        $data=input('get.');
        $this->nav_login();
        $this->header_nav();
        $num=1;
        if(array_key_exists('page',$data)) {
            $num = $num * $data['page'];
        }
        $develop=Db::table('develop_support')
            ->alias('d')
            ->join('member m','m.id=d.develop_support_user')
            ->where('develop_support_uid',$data['static'])->paginate(100,false,[
            'query'=>array('static'=>$data['static'])
        ]);
        $page=$develop->render();
        $this->assign('page',$page);
        $this->assign('num',$num);
        $this->assign('develop',$develop);
        return $this->fetch();
    }

    public function develop_comment_ajax(){
        $data=input('post.');
        $if=Db::table('develop_function')->where('develop_function_uid',$data['id'])->select();
        $html="";
        for($i=0;$i<count($if);$i++){
            $html .="<option value='".$if[$i]['develop_function_id']."'>".$if[$i]['develop_function_name']."</option>";
        }
        $text['text']=$html;
        if($if){
            $text['num']=1;
            return json($text);
        }else{
            $text['num']=0;
            return json($text);
        }

    }

    public function develop_comments_ajax(){
        $data=input('post.');
        $user=Session::get('users');
        $where=array(
            'develop_comment_uid'=>$data['uid'],
            'develop_comment_user'=>$user,
            'develop_comment_title'=>$data['input'],
            'develop_comment_text'=>$data['text'],
            'develop_comment_time'=>time()
        );
        $if=Db::table('develop_comment')->insert($where);
        if($if){
            $data['num']=1;
            return json($data);
        }else{
            $data['num']=0;
            return json($data);
        }
    }

    public function develop_user_support_ajax(){
        $data=input('post.');
        $user=Session::get('users');
        $where=array(
            'develop_support_nid'=>$data['id'],
            'develop_support_user'=>$user,
            'develop_support_time'=>time()
        );
        $if=Db::table('develop_support')->insert($where);
        Db::table('develop_comment')->where('develop_comment_id',$data['id'])->setInc('develop_comment_support',1);
        if($if){
            $text['num']=1;
            return json($text);
        }else{
            $text['num']=0;
            return json($text);
        }
    }
}