<?php

namespace app\index\controller;

use think\Db;

class Api extends Comment{
    public function login(){
        $date=input("post.");
        $da="";
        if(!empty($date)){
            if($date["name"]==""){
                $da["code"]=101;
            }else{
                $data["user_account"]=$date["name"];
                $data["user_password"]=$date["password"];
                $form=Db::table("api_member")->where($data)->find();
                $book=Db::table("api_book")->where("book_member",$form["user_id"])
                    ->count();
                if($form){
                    $da["code"]=103;
                    $da["token"]="liulang";
                    $da["user_name"]=$form["user_name"];
                    $da["user_id"]=$form["user_id"];
                    $da["avatar"]=$form["avatar"];
                    $da["last_login_at"]=time();
                    $da["recited_book"]=$form["recited_book"];
                    if($book>0){
                        $da["book_status"]=1;
                    }else{
                        $da["book_status"]=0;
                    }
                }else{
                    $da["code"]=102;
                }
            }
            return json($da);
        }else{
            $da["code"]=101;
            return json($da);
        }
    }

    public function book_list(){
        $date=input("get.");
        if(!empty($date)){
            $da["ones"]=Db::table("book_one")->where("one_language",$date["language_id"])->select();
            $table1["two_language"]=$date["language_id"];
            $table1["two_one"]=$date["one_number"];
            $da["twos"]=Db::table("book_two")->where($table1)->select();
            $da["code"]=200;
            return json($da);
        }else{
            $da["code"]=403;
            return json($da);
        }
    }

    public function book(){
        $data=input("get.");
        if(!empty($data)){
            $books=Db::table("api_book")->alias("a")
                ->join("book b","b.book_id=a.book_int")
                ->where(array("a.book_member"=>$data["user_id"]))
                ->field("a.book_int book_id,
                b.book_name,
                a.book_member book_user,
                b.book_img,
                b.book_url,
                b.book_total total_num,
                a.book_new new_num,
                a.book_new_star level_star,
                b.book_star star,
                a.book_all_star all_star,
                a.book_start started_at,
                a.book_finish finished_at,
                a.book_last_finish last_finished_at,
                b.book_gate now_gate,
                a.book_gates finish_gate,
                b.book_good good,
                b.book_number number,
                a.book_finished finished,
                b.book_summary summary")->select();
            if($books){
                $da["BookList"]=$books;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function book_delete(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_book")
                ->where(array("book_member"=>$data["user_id"],
                    "book_int"=>$data["book_id"]))
                ->delete();
            Db::table("api_book_gate")
                ->where(array("gate_book"=>$data["book_id"]
                ,"gate_user"=>$data["user_id"]))
                ->delete();
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function book_search(){
        $data=input("get.");
        if(!empty($data)){
            $keyword="%".$data["book_name"]."%";
            if($data["more_status"]==1){
                $num=4;
            }else{
                $num=null;
            }
            $book=array();
            $a=0;
            for($i=0;$i<9;$i++){
                $form=Db::table("book")
                    ->where("book_name","like",$keyword)
                    ->where("book_language",$i)
                    ->limit($num)
                    ->field("book_id,
                book_name,
                book_img,
                book_total,
                book_good,
                book_number,
                book_summary")
                    ->select();
                for($j=0;$j<count($form);$j++){
                    $number=Db::table("api_book")
                        ->where(array("book_member"=>$data["user_id"],
                            "book_int"=>$form[$j]["book_id"]))
                        ->find();
                    if($number){
                        $form[$j]["book_select"]=1;
                    }else{
                        $form[$j]["book_select"]=0;
                    }
                }
                if($form){
                    $book[$a]["books"]=$form;
	      $a++;
                }
            }

            if($book){
                $da["code"]=200;
                $da["languages"]=$book;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function book_tag(){
        $form=Db::table("book_tag")
            ->field("tag_name")
            ->select();
        if($form){
            $da["code"]=200;
            $da["tags"]=$form;
        }else{
            $da["code"]=403;
        }
        return json($da);
    }

    public function book_hint(){
        $data=input("get.");
        if(!empty($data)){
            $keyword="%".$data["book_hint"]."%";
            $form=Db::table("book")
                ->where("book_name","like",$keyword)
                ->where("book_language",0)
                ->limit(7)
                ->field("
                book_name hint_name")
                ->select();
            if($form){
                $da["code"]=200;
                $da["hints"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function book_word(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("word")
                ->where("word_book",$data["book_id"])
                ->field("word_book,
                word_gate,
                word_name,
                word_music_url,
                word_music,
                word_meaning,
                word_sentence,
                word_sentence_url,
                word_sentence_meaning,
                word_root,
                word_letter")
                ->select();
            if($form){
                $da["code"]=200;
                $da["word"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function task(){
        $data=input("get.");
        if(!empty($data)){
            $day=date("Ymd",time())-1;
            $form=Db::table("api_task")->where(array("task_user"=>$data["user_id"],
                "task_status"=>array("EGT",0),
                "task_time"=>array(">=",$day)))
                ->select();
            $num=0;
            $day1=date("Ymd",time());
            $time=strtotime($day1);
            for($i=1;$i<5;$i++){
                $one=Db::table("api_task")->where(array("task_user"=>$data["user_id"],
                    "task_class"=>$i,
                    "task_time"=>array(">=",$time)))
                    ->find();
                if(!$one){
                    $task[$num]["task_class"]=$i;
                    $task[$num]["task_star"]=5;
                    $task[$num]["task_money"]=10;
                    $num++;
                    $da["noaccept"]=$task;
                }
            }
            if($form){
                $da["code"]=200;
                $da["accept"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function task_star(){
        $data=input("get.");
        if(!empty($data)){
            $word=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("recited_book")
                ->find();
            $task=Db::table("api_task")
                ->where("task_id",$data["task_id"])
                ->find();
            if($data["task_class"]==1
                &&$task["task_status"] =2){
                Db::table("api_book")->where("api_book_id",$word["recited_book"])->setInc("book_all_star",$task["task_star"]);
                $form=Db::table("api_task")
                    ->where("task_id",$data["task_id"])
                    ->update(array("task_text_time"=>date("Y.m.d",time()),
                        "task_finish_time"=>time()));
                if($form){
                    $da["code"]=200;
                }else{
                    $da["code"]=403;
                }
            }else if($data["task_class"]==2&&$task["task_no_star"] !=0){
                Db::table("api_book")->where("api_book_id",$word["recited_book"])->setInc("book_all_star",$task["task_no_star"]);
                $form1=Db::table("api_task")
                    ->where("task_id",$data["task_id"])
                    ->update(array("task_no_star"=>0));
                if($form1){
                    $da["code"]=200;
                }else{
                    $da["code"]=403;
                }
            }else{
                $da["code"]=402;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function task_list(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_task")
                ->where(array("task_user"=>$data["user_id"],"task_status"=>2))->
                whereOr("task_status",3)
                ->limit("task_class,
                task_status,
                task_day,
                task_star,
                task_money,
                task_text_time task_time")
                ->limit(10)
                ->select();
            if($form){
                $da["code"]=200;
                $da["lists"]=$form;
            }else{
                $da["code"]=404;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_context(){
        $data=input("get.");
        if(!empty($data)){
            $tag=Db::table("group_tag")->where("group_tag_id",$data["group_class"])->find();
            if($tag){
                $text=$tag["group_tag_name"];
            }else{
                $text="";
            }
            $keyword ='%'.$text.'%';
            $learns=Db::table("api_group")
                ->where("group_tag","like",$keyword)
                ->where("group_star",25)
                ->limit(10)
                ->order("group_done desc")
                ->field("group_id,
                group_member,
                group_member_top group_all_member,
                group_avatar,
                group_name,
                group_done,
                group_tag,
                group_all_star group_star,
                group_summary")
                ->select();
            $active=Db::table("api_group")
                ->where("group_tag","like",$keyword)
                ->limit(10)
                ->order("group_day desc")
                ->field("group_id,
                group_member,
                group_member_top group_all_member,
                group_avatar,
                group_name,
                group_done,
                group_tag,
                group_all_star group_star,
                group_summary")
                ->select();
            $form=Db::table("api_group")
                ->limit(20)
                ->order("group_time desc")
                ->field("group_id,
                group_member,
                group_member_top group_all_member,
                group_avatar,
                group_name,
                group_done,
                group_tag,
                group_all_star group_star,
                group_summary")
                ->select();
            $user=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            if($user["user_group"]==0){
                $da["groups"]["group_status"]=0;
            }else{
                $da["groups"]["group_status"]=1;
            }
            $da["groups"]["group_money"]=$user["user_money"];
            if($form){
                $da["code"]=200;
                $da["learns"]=$learns;
                $da["active"]=$active;
                $da["lists"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_rank(){
        $data=input("get.");
        if(!empty($data)){
            if($data["group_status"]==0){
                $status="group_all_star desc";
            }else if($data["group_status"]==1){
                $status="group_day desc";
            }else if($data["group_status"]==2){
                $status="group_all_star desc";
            }

            $form=Db::table("api_group")
                ->where("group_star",$data["group_star"])
                ->limit(100)
                ->order($status)
                ->field("group_id,
                group_avatar group_img,
                group_name,
                group_level,
                group_all_star,
                group_all_star group_star,
                group_done group_number,
                group_hot,
                group_day ")
                ->select();
            if($form){
                $da["code"]=200;
                $da["ranks"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_finial(){
        $data=input("get.");
        if(!empty($data)){
            if($data["group_status"]==0){
                $status="group_time asc";
            }else if($data["group_status"]==1){
                $status="group_time desc";
            }
            $tag=Db::table("group_tag")->where("group_tag_id",$data["group_class"])->find();
            if($tag){
                $text=$tag["group_tag_name"];
            }else{
                $text="";
            }
            $keyword ='%'.$text.'%';
            $form=Db::table("api_group")
                ->where("group_tag","like",$keyword)
                ->limit(20)
                ->order($status)
                ->field("group_id,
                group_avatar,
                group_member,
                group_member_top group_all_member,
                group_name,
                group_tag,
                group_star,
                group_done,
                group_summary")
                ->select();
            if($form){
                $da["code"]=200;
                $da["lists"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_details(){
        $data=input("get.");
        if(!empty($data)){
            $user=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            $details=Db::table("api_group")
                ->alias("d")
                ->where("d.group_id",$data["group_id"])
                ->join("api_member m","m.user_id=d.group_admin")
                ->field("d.group_id,
                d.group_avatar,
                d.group_name,
                d.group_level,
                d.group_member,
                d.group_member_top group_all_member,
                d.group_admin group_admin_id,
                m.user_name group_admin,
                d.group_all_star group_star,
                d.group_time,
                d.group_summary,
                d.group_tag")
                ->find();
            $medal=Db::table("api_medal")
                ->alias("a")
                ->join("group_medal m","m.medal_id=a.medal_uid")
                ->where("a.medal_group",$data["group_id"])
                ->limit(5)
                ->field("m.medal_img")
                ->select();
            $details["group_medal"]=count($medal);
            $details["medals"]=$medal;
            $tag=explode(",",$details["group_tag"]);
            $details["group_tag"]=count($tag);
            $details["tags"]=$tag;
            if($user["user_group"]==0){
                $details["group_status"]=0;
            }else if($user["user_group"]==$details["group_id"]){
                $details["group_status"]=1;
            }else{
                $details["group_status"]=2;
            }
            if($details){
                $da["code"]=200;
                $da["details"]=$details;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_main(){
        $data=input("get.");
        if(!empty($data)){
            $user=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            $details=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->field("
                group_id,
                group_avatar group_img,
                group_name,
                group_level,
                group_new,
                group_member,
                group_star,
                group_now_star,
                group_new,
                group_admin")
                ->find();
            $details["group_all_star"]=$details["group_star"]*$details["group_member"];
            $medal=Db::table("api_medal")
                ->alias("a")
                ->join("group_medal m","m.medal_id=a.medal_uid")
                ->where("a.medal_group",$data["group_id"])
                ->limit(5)
                ->field("m.medal_img")
                ->select();
            $details["group_common"]=120;
            if($details["group_admin"]==$data["user_id"]){
                $details["group_admin"]=1;
            }else{
                $details["group_admin"]=0;
            }
            $form["user_rank"]=14;
            $form["user_img"]=$user["avatar"];
            $form["user_star"]=$user["user_star"];
            $form["user_star_up"]=$details["group_star"];
            if($details){
                $da["code"]=200;
                $da["mains"]=$details;
                $da["medal"]=$medal;
                $da["rank"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function main_rank(){
        $data=input("get.");
        if(!empty($data)){
            $group=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->find();
            $user=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            $form["items"]=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_star",">=",$group["group_star"])
                ->field("user_id,
                avatar user_img,
                user_name,
                user_star,
                user_praise")
                ->select();
            $form["notitems"]=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_star","<",$group["group_star"])
                ->field("user_id,
                avatar user_img,
                user_name,
                user_star,
                user_praise")
                ->select();
            if($user){
                $da["code"]=200;
                $da["rank"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_board(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("group_board")
                ->alias("b")
                ->where("board_group",$data["group_id"])
                ->join("api_member m","m.user_id=b.board_member")
                ->order("board_time desc")
                ->limit(10)
                ->field("board_id,
                user_id board_member,
                avatar board_img,
                user_name board_name,
                board_praise,
                board_text,
                board_time")
                ->select();
            if($form){
                $da["code"]=200;
                $da["boards"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_list(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->field("user_id,
                avatar user_img,
                user_name")
                ->select();
            if($form){
                $da["lists"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_search(){
        $data=input("get.");
        if(!empty($data)){
            $keyword ='%'.$data["user_name"].'%';
            $form=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_name","like",$keyword)
                ->field("user_id,
                avatar user_img,
                user_name")
                ->select();
            if($form){
                $da["lists"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }

        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function board_send(){
        $data=input("post.");
        if(!empty($data)){
            $text["board_group"]=$data["group_id"];
            $text["board_member"]=$data["user_id"];
            $text["board_a"]=$data["a_id"];
            $text["board_text"]=$data["board_text"];
            $text["board_time"]=time();
            $text["board_praise"]=0;
            $form=Db::table("group_board")
                ->insert($text);
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function board_praise(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("group_board")
                ->where("board_id",$data["board_id"])
                ->setInc("board_praise",1);
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_day(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->find();
            $member=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            if($member["user_star"]>$form["group_star"]){
                $i=1;
            }else{
                $i=0;
            }
            if($form["group_now_star"]>$form["group_day_star"]){
                $j=1;
            }else{
                $j=0;
            }
            $days["group_status"]=$j;
            $days["group_day_star"]=$form["group_day_star"];
            $days["group_star"]=$form["group_star"];
            $days["group_now_star"]=$form["group_now_star"];
            $days["group_all_star"]=$form["group_day_star"];
            $days["group_member"]=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_star",">",$form["group_star"])
                ->count();
            $days["group_not_member"]=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_star","<",$form["group_star"])
                ->count();
            $days["user_status"]=$i;
            $days["user_star"]=$member["user_star"];
            $days["user_praise"]=$member["user_praise"];
            if($form["group_level_top"]>$form["group_level_bottom"]){
                $a=1;
            }else{
                $a=0;
            }
            $days["group_level_status"]=$a;
            $days["group_level"]=$form["group_level"];
            $time=strtotime(date("Ymd",time())-1);
            $days["praises"]=Db::table("member_praise")
                ->alias("p")
                ->join("api_member m","m.user_id=p.user_id")
                ->where("p.user_id",$data["user_id"])
                ->where("p.praise_time",">",$time)
                ->limit(5)
                ->field("m.avatar user_img")
                ->select();
            $days["lists"]=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->where("user_star",">",$form["group_star"])
                ->limit(5)
                ->field("avatar user_img,user_name")
                ->select();
            if($form){
                $da["days"]=$days;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_level(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->field("group_name,
                group_level,
                group_all_star,
                group_level_top group_level_star,
                group_level_bottom group_level_all_star,
                group_now_star
                ")->find();
            $form["items"]=Db::table("group_level")
                ->where("level_group",$data["group_id"])
                ->field("level_level item_level,
                level_star item_star,
                level_number item_number,
                level_time item_time")
                ->select();
            if($form){
                $da["code"]=200;
                $da["level"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_member(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_group",$data["group_id"])
                ->field("user_id,
                avatar user_avatar,
                user_name,
                user_praise,
                user_star,
                user_all_star user_done")
                ->select();
            $group=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->find();
            $user=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->find();
            if($data["manage_status"]==1){
                $name=$group["group_name"];
                $day=$group["group_day"];
                $star=$group["group_all_star"];
            }else{
                $name="";
                $day=$user["user_day"];
                $star=$user["user_all_star"];
            }
            $member["member_name"]=$name;
            $member["member_day"]=$day;
            $member["member_star"]=$star;
            $member["member_img"]=$group["group_avatar"];
            $member["lists"]=$form;
            $member["member_number"]=count($form);
            $out=Db::table("group_update")
                ->alias("u")
                ->join("api_member m","m.user_id=u.update_user")
                ->where("u.update_group",$data["group_id"])
                ->where("u.update_status",0)
                ->field("m.avatar user_avatar")
                ->select();
            $member["outs"]=$out;
            $member["out_number"]=count($out);
            $in=Db::table("group_update")
                ->alias("u")
                ->join("api_member m","m.user_id=u.update_user")
                ->where("u.update_group",$data["group_id"])
                ->where("u.update_status",1)
                ->field("m.avatar user_avatar")
                ->select();
            $member["ins"]=$in;
            $member["in_number"]=count($in);
            if($form){
                $da["member"]=$member;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function member_status(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("group_update")
                ->alias("u")
                ->join("api_member m","m.user_id=u.update_user")
                ->where("u.update_group",$data["group_id"])
                ->where("u.update_status",$data["member_status"])
                ->field("avatar member_avatar,
                user_name member_name,
                update_time member_time")
                ->select();
            if($form){
                $da["members"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_out(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_group"=>0,
                    "user_all_star"=>0,
                    "user_day"=>0,
                    "user_star"=>0));
            if($form){
                Db::table("group_update")
                    ->insert(array("update_user"=>$data["user_id"],
                        "update_status"=>0,
                        "update_time"=>time(),
                        "update_group"=>$data["group_id"]));
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_dismiss(){
        $data=input("get.");
        if(!empty($data)){
            if(Db::table("api_group")->where("group_admin",$data["user_id"])->find()){
                $form=Db::table("api_member")
                    ->where("user_group",$data["group_id"])
                    ->select();
                for ($i=0;$i<count($form);$i++){
                    Db::table("api_member")
                        ->where("user_id",$form[$i]["user_id"])
                        ->update(array("user_group"=>0,
                            "user_all_star"=>0,
                            "user_day"=>0,
                            "user_star"=>0));
                }
                $delete=Db::table("api_group")
                    ->where("group_admin",$data["user_id"])
                    ->where("group_id",$data["group_id"])
                    ->delete();

                if($delete){
                    $da["code"]=200;
                }else{
                    $da["code"]=403;
                }
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_avatar(){
            $form=Db::table("group_avatar")
                ->select();
            if($form){
                $da["code"]=200;
                $da["items"]=$form;
            }else{
                $da["code"]=403;
            }
        return json($da);
    }

    public function group_create(){
        $data=input("get.");
        if(!empty($data)){
            $create["group_name"]=$data["group_name"];
            $create["group_avatar"]=$data["group_avatar"];
            $create["group_tag"]=$data["group_tag"];
            $create["group_summary"]=$data["group_summary"];
            $create["group_link"]=$data["group_link"];
            $create["group_links"]=$data["group_links"];
            $create["group_star"]=$data["group_star"];
            $create["group_re''"]=$data["group_re"];
            $create["group_as"]=$data["group_as"];
            $create["group_admin"]=$data["group_admin"];
            $form=Db::table("api_group")
                ->insert($create);
            if($form){
                Db::table("api_member")->where("user_id",$data["group_admin"])
                    ->setDec("user_money",200);
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_star(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->update(array("group_star"=>$data["group_star"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_delete(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_group"=>0,
                    "user_all_star"=>0,
                    "user_day"=>0,
                    "user_star"=>0));
            if($form){
                Db::table("group_update")
                    ->insert(array("update_user"=>$data["user_id"],
                        "update_status"=>0,
                        "update_time"=>time(),
                        "update_group"=>$data["group_id"]));
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_summary(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->field("group_id,
                group_summary,
                group_link,
                group_links,
                group_tag,group_tag_id")
                ->find();
            if(!empty($form["group_tag"])){
                $tags=explode(",",$form["group_tag"]);
            }else{
                $tags=array();
            }
            if(!empty($form["group_tag_id"])){
                $tags_id=explode(",",$form["group_tag_id"]);
            }else{
                $tags_id=array();
            }
            $form["tags"]=$tags;
            $form["tags_id"]=$tags_id;
            if($form){
                $da["summary"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function summary_add(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->update(array("group_summary"=>$data["group_summary"],
                    "group_tag"=>$data["group_tag"],
                    "group_link"=>$data["group_link"],
                    "group_links"=>$data["group_links"]));
            if($form){
                $da["code"]=200;
                $da["code"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_setting(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->field("group_re group_search,
                group_as group_add")
                ->find();
            if($form){
                $da["code"]=200;
                $da["setting"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function setting_add(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->update(array("group_re"=>$data["group_search"],
                    "group_as"=>$data["group_add"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_add(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_group")
                ->where("group_id",$data["group_id"])
                ->find();
            if($form["group_as"]==0&&$form["group_member"]<$form["group_member_top"]){
                Db::table("api_member")
                    ->where("user_id",$data["user_id"])
                    ->update(array("user_group"=>$data["group_id"]));
                Db::table("api_group")
                    ->where("group_id",$data["group_id"])
                    ->setInc("group_member",1);
                $user=Db::table("group_update")
                    ->insert(array("update_user"=>$data["user_id"],
                        "update_status"=>1,
                        "update_time"=>time(),
                        "update_group"=>$data["group_id"]));
                if($user){
                    $da["code"]=200;
                }else{
                    $da["code"]=403;
                }
            }else{
                $da["code"]=201;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_add_text(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("group_wait")
                ->insert(array("wait_user"=>$data["user_id"],
                    "wait_group"=>$data["group_id"],
                    "wait_text"=>$data["add_text"],
                    "wait_time"=>time()));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function friend_main(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["friend_id"])
                ->field("user_id friend_id,
                avatar friend_avatar,
                user_name friend_name,
                user_sign friend_sign,
                user_group,
                recited_book")
                ->find();
            $friend=Db::table("api_friend")
                ->where("member_id",$data["user_id"])
                ->where("friend_id",$data["friend_id"])
                ->find();
            if($friend){
                $form["friend_status"]=$friend["friend_status"];
            }else{
                $form["friend_status"]=0;
            }
            if($form["user_group"]==0){
                $form["group_status"]=0;
            }else{
                $form["group_status"]=1;
                $form["group"]=Db::table("api_group")
                    ->where("group_id",$form["user_group"])
                    ->field("group_id,
                    group_avatar,
                    group_name,
                    group_level")
                    ->find();
                $medal=Db::table("api_medal")
                    ->alias("m")
                    ->join("group_medal g","g.medal_id=m.medal_uid")
                    ->where("m.medal_group",$form["group"]["group_id"])
                    ->field("medal_img medal_avatar")
                    ->limit(5)
                    ->select();
                $form["group"]["group_medal"]=count($medal);
                $form["group"]["medal"]=$medal;
            }

            if($form["recited_book"]==0){
                $form["word_status"]=0;
            }else{
                $form["word_status"]=1;
                $form["word"]=Db::table("book")
                    ->where("book_id",$form["recited_book"])
                    ->field("book_id word_id,
                    book_img word_avatar,
                    book_name word_name")
                    ->find();
                $form["word"]["pk_status"]=1;
                $form["word"]["word_pk"]=0;
            }
            if($form){
                $da["code"]=200;
                $da["friends"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function friend_add(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_friend")
                ->insert(array("member_id"=>$data["user_id"],
                    "friend_id"=>$data["friend_id"],
                    "friend_status"=>1));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_main(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("user_id,
                avatar user_avatar,
                user_name,
                user_sign,
                user_group,
                user_money")
                ->find();
            $form["sign"]=0;
            if($form["user_group"] !=0){
                $form["group"]=Db::table("api_group")
                    ->where("group_id",$form["user_group"])
                    ->field("group_avatar group_img,
                    group_level,
                    group_name")
                    ->find();
                $medal=Db::table("api_medal")
                    ->alias("m")
                    ->join("group_medal g","g.medal_id=m.medal_uid")
                    ->where("m.medal_group",$form["user_group"])
                    ->field("medal_img")
                    ->select();
                $form["group"]["group_number"]=count($medal);
                $form["group"]["medal"]=$medal;
            }
            if($form){
                $da["mains"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_summary_add(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_sign"=>$data["summary_text"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_share(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("word_day share_date")
                ->find();
            $form["share_year"]=date("Y",time());
            $form["share_month"]=date("m",time());
            $list=Db::table("word_day")
                ->where(array("day_user"=>$data["user_id"],
                    "day_year"=>$form["share_year"],
                    "day_month"=>$form["share_month"]))
                ->select();
            for($i=1;$i<=$this->getMonthDay( $form["share_year"],
                $form["share_month"]);$i++){
                if($i>date("d",time())){
                    $day[$i]["day_status"]=2;
                }else{
                    $day[$i]["day_status"]=0;
                }
                $day[$i]["day_number"]=0;
                for($j=0;$j<count($list);$j++){
                    if($i==$list[$j]["day_time"]){
                        $day[$i]["day_status"]=$list[$j]["day_status"];
                        $day[$i]["day_number"]=$list[$j]["day_number"];
                        break;
                    }
                }
            }
            $form["days"]=$day;
            if($form){
                $da["code"]=200;
                $da["share"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function word_data(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("word_day share_date")
                ->find();
            $list=Db::table("word_day")
                ->where(array("day_user"=>$data["user_id"],
                    "day_year"=>$data["user_year"],
                     "day_month"=>$data["user_month"]))
                ->select();
            for($i=1;$i<=$this->getMonthDay($data["user_year"],
                $data["user_month"]);$i++){
                $day[$i]["day_status"]=0;
                $day[$i]["day_number"]=0;
                for($j=0;$j<count($list);$j++){
                    if($i==$list[$j]["day_time"]){
                        $day[$i]["day_status"]=$list[$j]["day_status"];
                        $day[$i]["day_number"]=$list[$j]["day_number"];
                        break;
                    }
                }
            }
            $form["days"]=$day;
            if($form){
                $da["code"]=200;
                $da["share"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_data(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("user_word data_word,
                user_day data_day,
                user_pk data_pk")
                ->find();
            $form["date_title"]=date("Y",time())."年".date("m",time())."月";
            $list=Db::table("word_day")
                ->where(array("day_user"=>$data["user_id"],
                    "day_year"=>date("Y",time()),
                    "day_month"=>date("m",time())))
                ->select();
            $day=array();
            for($i=0;$i<=$this->getMonthDay(date("Y",time()),
                date("m",time()))-1;$i++){
                if($i>date("d",time())){
                    $day[$i]["day_status"]=2;
                }else{
                    $day[$i]["day_status"]=0;
                }
                $day[$i]["day_number"]=0;
                for($j=0;$j<count($list);$j++){
                    if($i==$list[$j]["day_time"]){
                        $day[$i]["day_status"]=$list[$j]["day_status"];
                        $day[$i]["day_number"]=$list[$j]["day_number"];
                        break;
                    }
                }
            }
            if($form){
                $da["code"]=200;
                $da["data"]=$form;
                $da["day"]=$day;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_friend(){
        $data=input("get.");
        if(!empty($data)){
            $arr["adds"]=Db::table("api_friend")
                ->alias("a")
                ->join("api_member m","m.user_id=a.friend_id")
                ->where("member_id",$data["user_id"])
                ->where("friend_status",2)
                ->field("api_friend_id item_id,
                m.user_id add_id,
                m.avatar add_avatar,
                m.user_name add_name")
                ->select();
            $arr["friends"]=Db::table("api_friend")
                ->alias("a")
                ->join("api_member m","m.user_id=a.friend_id")
                ->where("member_id",$data["user_id"])
                ->where("friend_status",1)
                ->field("m.user_id friend_id,
                m.avatar friend_avatar,
                m.user_name friend_name,
                m.user_sign friend_sign,
                m.user_word friend_word")
                ->select();
            if($arr){
                $da["data"]=$arr;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function friend_update(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_friend")
                ->where("api_friend_id",$data["item_id"])
                ->update(array("friend_status"=>$data["item_status"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function setting_account(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->alias("m")
                ->join("member_more a","a.member_uid=m.user_id")
                ->where("m.user_id",$data["user_id"])
                ->field("m.avatar user_avatar,
                m.user_name,
                a.member_nickname user_title,
                a.member_weibo user_weibo,
                a.member_qq user_qq,
                a.member_phone user_phone")
                ->find();
            if($form){
                $da["account"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function update_name(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_name"=>$data["user_name"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function update_nickname(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_nickname"=>$data["user_nickname"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function update_pass(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->update(array("user_pass"=>$data["user_pass"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function update_phone(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("member_more")
                ->where("member_uid",$data["user_id"])
                ->update(array("member_phone"=>$data["user_phone"]));
            if($form){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_money(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("user_money money_number")
                ->find();
            if($form){
                $da["money"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function user_photo(){
        $form=Db::table("group_medal")
            ->field("medal_id photo_id,
            medal_img photo_url")
            ->select();
        if($form){
            $da["photos"]=$form;
            $da["code"]=200;
        }else{
            $da["code"]=403;
        }
        return json($da);
    }

    public function book_insert(){
        $data=input("get.");
        if(!empty($data)){
            $item=Db::table("api_book")
                ->where(array("book_member"=>$data["user_id"],"book_int"=>$data["book_id"]))
                ->find();
            if($item){
                $book=Db::table("api_member")
                    ->where(array("user_id"=>$data["user_id"],"recited_book"=>$data["book_id"]))
                    ->find();
                if($book){
                    $da["code"]=200;
                    $da["book_status"]=1;
                }else{
                    Db::table("api_member")
                        ->where("user_id",$data["user_id"])
                        ->update(array("recited_book"=>$data["book_id"]));
                    $da["code"]=200;
                    $da["book_status"]=2;
                }
            }else{
                $table["book_member"]=$data["user_id"];
                $table["book_int"]=$data["book_id"];
                $table["book_start"]=time();
                $form=Db::table("api_book")->insert($table);
                if($form){
                $num=Db::table("api_book")
                    ->getLastInsID();
                    $books=Db::table("api_book")->alias("a")
                        ->join("book b","b.book_id=a.book_int")
                        ->where(array("a.api_book_id"=>$num))
                        ->field("a.book_int book_id,
                        a.book_member book_user,
                b.book_name,
                b.book_img,
                b.book_url,
                b.book_total total_num,
                a.book_new new_num,
                a.book_new_star level_star,
                b.book_star star,
                a.book_all_star all_star,
                a.book_start started_at,
                a.book_finish finished_at,
                a.book_last_finish last_finished_at,
                b.book_gate now_gate,
                a.book_gates finish_gate,
                b.book_good good,
                b.book_number number,
                b.book_summary summary")->find();
                    Db::table("api_member")
                        ->where(array("user_id"=>$data["user_id"]))
                        ->update(array("recited_book"=>$data["book_id"]));
                    $da["code"]=200;
                    $da["book"]=$books;
                    $da["book_status"]=3;
                }else{
                    $da["code"]=400;
                }
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function book_item(){
        $data=input("get.");
        if(!empty($data)){
            $table["book_one"]=$data["one_number"];
            $table["book_two"]=$data["two_number"];
            $table["book_language"]=$data["language_id"];
            $form=Db::table("book")->where($table)->select();
            if($form){
                $da["items"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=404;
            }
        }else{
            $da["code"]=403;

        }
        return json($da);
    }

    public function friend(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_friend")->alias("f")
                ->join("api_member m","m.user_id=f.friend_id")
                ->where("f.member_id",$data["user_id"])
                ->field("m.avatar friend_img,
                m.user_name friend_name,
                f.friend_id,
                m.user_sign friend_summary")
                ->select();
            if($form){
                $da["code"]=200;
                $da["friends"]=$form;
            }else{
                $da["code"]=404;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function friend_search(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_member")->alias("m")
                ->where("user_name","like",'%'.$data["user_name"].'%')
                ->field("user_name,
                avatar user_avatar,
                user_id")
                ->limit(10)
                ->page($data["page"])
                ->select();
            if($form){
                for($i=0;$i<count($form);$i++){
                    $status=Db::table("api_friend")->where(array("member_id"=>$data["user_id"],
                        "friend_id"=>$form[$i]["user_id"]))
                        ->find();
                    if($status){
                        $form[$i]["user_status"]=2;
                    }else{
                        $form[$i]["user_status"]=1;
                    }
                }
                $da["search"]=$form;
                $da["code"]=200;
            }else{
                $da["code"]=404;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function pk_data(){
        $data=input("get.");
        if(!empty($data)){
            if(!empty($data["user_id"])){
                $form=Db::table("pk_data")->alias("p")
                    ->join("api_member n","n.user_id=p.data_friend")
                    ->join("api_member d","d.user_id=p.data_member")
                    ->where("p.data_member",$data["user_id"])
                    ->whereOr("p.data_friend",$data["user_id"])
                    ->order("data_time desc")
                    ->field("p.data_id,
                      n.user_id data_one_user,
                      n.avatar data_one_avatar,
                      d.avatar data_two_avatar,
                      n.user_name data_one_name,
                      d.user_id data_two_user,
                      d.user_name data_two_name,
                      p.data_num,
                      p.data_word,
                      p.data_time,
                      p.data_status,
                      p.data_number")
                    ->limit(10)->select();
                if($form){
                    for($i=0;$i<count($form);$i++){
                        if($form[$i]["data_one_user"]==$data["user_id"]){
                            $form[$i]["data_user"]=$form[$i]["data_two_user"];
                            $form[$i]["data_name"]=$form[$i]["data_two_name"];
                            $form[$i]["data_avatar"]=$form[$i]["data_two_avatar"];
                        }else{
                            $form[$i]["data_user"]=$form[$i]["data_one_user"];
                            $form[$i]["data_name"]=$form[$i]["data_one_name"];
                            $form[$i]["data_avatar"]=$form[$i]["data_one_avatar"];
                        }
                    }
                    $da["code"]=200;
                    $da["data"]=$form;
                }else{
                    $da["code"]=403;
                }
            }else{
                $da["code"]=402;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function pk_modern(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("pk_modern")
                ->alias("m")
                ->where("m.modern_admin",$data["user_id"])
                ->join("api_member a","a.user_id=m.modern_user")
                ->join("book b","b.book_id=a.recited_book")
                ->order("m.modern_time desc")
                ->field("m.modern_id,
                a.user_name modern_name,
                a.avatar modern_avatar,
                m.modern_time,
                b.book_name modern_word")
                ->limit(10)
                ->select();
            if($form){
                $da["code"]=200;
                $da["moderns"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function pk_know(){
        $data=input("get.");
        if(!empty($data)){
            $from=Db::table("pk_modern")->where("modern_id",$data["modern_id"])
                ->delete();
            if($from){
                $da["code"]=200;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function group_medal(){
        $data=input("get.");
        if(!empty($data)){
            $medal["medal_number"]=Db::table("api_medal")
                ->where("medal_group",$data["group_id"])
                ->count();
            $rank="";
            switch($medal["medal_number"]){
                case 1;
                    $rank=10;
                    break;
                case 2:
                    $rank=21;
                    break;
                case 3;
                    $rank=35;
                    break;
                case 4:
                    $rank=48.8;
                    break;
                case 5;
                    $rank=52.9;
                    break;
                case 6:
                    $rank=63.5;
                    break;
                case 7;
                    $rank=78.1;
                    break;
                case 8:
                    $rank=84.2;
                    break;
                case 9;
                    $rank=91.2;
                    break;
                case 10:
                    $rank=99.9;
                    break;
            }
            $medal["medal_rank"]=$rank;
            for($i=1;$i<4;$i++){
                if($data["medal_status"]==1){
                    $list=Db::table("group_medal")
                        ->where("medal_class",$i)
                        ->field("medal_id,
                medal_name,
                medal_img,
                medal_un_img")
                        ->select();

                    for($j=0;$j<count($list);$j++){
                        $group=Db::table("api_medal")
                            ->where("medal_group",$data["group_id"])
                            ->where("medal_uid",$list[$j]["medal_id"])
                        ->find();
                        if(!$group){
                            $list[$j]["medal_img"]=$list[$j]["medal_un_img"];
                        }
                    }
                }else{
                    $list=Db::table("api_medal")
                        ->alias("m")
                        ->join("group_medal g","g.medal_id=m.medal_uid")
                        ->where("m.medal_group",$data["group_id"])
                        ->where("g.medal_class",$i)
                        ->field("g.medal_id,
                g.medal_name,
                g.medal_img")
                        ->select();
                }
                if($i==1){
                    $medal["ones"]=$list;
                }elseif($i==2){
                    $medal["twos"]=$list;
                }elseif($i==3){
                    $medal["threes"]=$list;
                }
            }
            if($medal){
                $da["code"]=200;
                $da["medal"]=$medal;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function medal_details(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("api_medal")
                ->alias("a")
                ->join("group_medal m","m.medal_id=a.medal_uid")
                ->where("a.medal_id",$data["medal_id"])
                ->field("a.medal_id,
                m.medal_img,
                m.medal_name,
                m.medal_require medal_task,
                a.medal_time,
                m.medal_number")
                ->find();
            if($form){
                $da["code"]=200;
                $da["details"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function search_tag(){
        $form=Db::table("search_tag")
            ->order("tag_number desc")
            ->limit(10)
            ->select();
        if($form){
            $da["code"]=200;
            $da["tags"]=$form;
        }else{
            $da["code"]=403;
        }
        return json($da);
    }

    public function search_list(){
        $data=input("get.");
        if(!empty($data)){
            $text="%".$data["search_name"]."%";
            $form=Db::table("api_group")
                ->where("group_name","like",$text)
                ->field("group_id,
                group_avatar group_img,
                group_name,
                group_level,
                group_star,
                group_tag")
                ->limit(10)
                ->select();
            for($i=0;$i<count($form);$i++){
                $tag=explode(",",$form[$i]["group_tag"]);
                $form[$i]["tags"]=$tag;
            }
            if($form){
                $da["code"]=200;
                $da["lists"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function register_code(){
        $date=input("get.");
        $da="";
        if(!empty($date)){
            if($date["email"]==""){
                $da["state"]=404;
                $da["text"]="数据短缺！";
            }else{
                $form=Db::table("member")->where($date)->find();
                if(!$form){
                    $da["state"]=200;
                    $da["text"]="帐号未注册！";
                }else{
                    $da["state"]=201;
                    $da["text"]="帐号已注册！";
                }
            }
            return json($da);
        }else{
            $da["state"]=403;
            $da["text"]="服务器错误！";
            return json($da);
        }
    }

    public function register_name(){
        $date=input("post.");
        if(!empty($date)){
            if($date["email"]==""){
                $da["state"]=404;
            }else{
                $form=Db::table("api_member")
                    ->insert($date);
                if($form){
                    $da["state"]=200;
                }else{
                    $da["state"]=403;
                }
            }
        }else{
            $da["state"]=404;
        }
        return json($da);
    }

    public function home_recite(){
        $data=input("get.");
        if(!empty($data)){
            $group=Db::table("api_member")
                ->where("user_id",$data["user_id"])
                ->field("user_group")->find();
            $news=Db::table("api_news")
                ->where("news_user",$data["user_id"])
                ->field("news_group group_news,
                news_task task_number,
                news_pk pk_number")
                ->find();
            if($group["user_group"]==0){
                $news["group_status"]=0;
            }else{
                $news["group_status"]=1;
            }
            $news["group_id"]=$group["user_group"];
            if($news){
                $da["code"]=200;
                $da["mains"]=$news;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function home_check(){
        $books=Db::table("api_item")
            ->where("item_page",1)
            ->limit(4)
            ->select();
        $goods=Db::table("api_item")
            ->where("item_page",2)
            ->limit(3)
            ->select();
        $words=Db::table("api_item")
            ->where("item_page",3)
            ->limit(4)
            ->select();
        if($words){
            $da["books"]=$books;
            $da["goods"]=$goods;
            $da["words"]=$words;
            $da["code"]=200;
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function home_find(){
        $urls=Db::table("api_banner")
            ->limit(5)
            ->select();
        $word=Db::table("api_word")
            ->limit(8)
            ->field("word_id,word_hot,word_img,word_name,word_number")
            ->select();
        $items=Db::table("api_item")
            ->where("item_page",1)
            ->limit(3)
            ->field("item_id good_id,
            item_name good_name,
            item_img good_img,
            item_title good_title")
            ->select();
        $goods=Db::table("api_item")
            ->where("item_page",2)
            ->limit(4)
            ->field("item_id good_id,
            item_name good_name,
            item_img good_img,
            item_title good_title")
            ->select();
        if($goods){
            $da["finds"]["tag_title"]="nihadaada";
            $da["finds"]["banners"]=$urls;
            $da["finds"]["words"]=$word;
            $da["finds"]["items"]=$items;
            $da["finds"]["goods"]=$goods;
            $da["code"]=200;
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function recite_check(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("member_check")
                ->alias("c")
                ->join("api_member m","m.user_id=c.check_uid")
                ->where("c.check_uid",$data["user_id"])
                ->field("c.check_time,
                c.check_gate,
                c.check_book,
                c.check_star,
                c.check_rank,
                m.avatar check_avatar,
                m.user_name check_name")
                ->find();
            if($form){
                $da["code"]=200;
                $da["check"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }

    public function recite_open(){
        $data=input("get.");
        if(!empty($data)){
            $form=Db::table("member_open")
                ->alias("o")
                ->join("api_member m","m.user_id=o.open_uid")
                ->where("o.open_uid",$data["user_id"])
                ->where("o.open_book",$data["book_id"])
                ->field("o.open_time,
                o.open_title,
                o.open_day,
                o.open_word,
                m.avatar open_avatar")
                ->find();
            if($form){
                $da["code"]=200;
                $da["open"]=$form;
            }else{
                $da["code"]=403;
            }
        }else{
            $da["code"]=404;
        }
        return json($da);
    }
}

























































