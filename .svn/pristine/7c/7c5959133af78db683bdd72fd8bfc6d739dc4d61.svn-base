<?php


class messageService{

    /**
     * 消息列表
     * $data = array("user_id"=>$user_id,"page"=>$page,"page_size"=>$page_size);
     * return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$page);
     */
    public function getlist($data){

        $user_id = (int)$data['user_id'];
        $page = (int)$data['page'];
        $page_size = (int)$data['page_size'];

        $limit = (($page-1)*$page_size).",".$page_size;

        $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."user_notice WHERE user_id=".$user_id,true,true);
        $list = array();

        $pages['page'] = $page;
        $pages['has_next'] = 0;

        if($rs_count > 0){
            $list = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."user_notice WHERE user_id=".$user_id." ORDER BY id desc limit ".$limit,true,true);
            foreach($list as $k=>$v){
                if(intval($v['send_id'])){
                    $head_image = $GLOBALS['db']->getOne("select head_image from ".DB_PREFIX."user where id=".intval($v['send_id']),true,true);
                    if($head_image){
                        $list[$k]['send_user_avatar'] = get_spec_image($head_image);
                    }else{
                        $list[$k]['send_user_avatar'] = '';
                    }
                }else{
                    $list[$k]['send_user_avatar'] = '';
                }
            }
            $total = ceil($rs_count/$page_size);
            if($total > $page)
                $pages['has_next'] = 1;
        }

        return array("rs_count"=>$rs_count,"list"=>$list,"page"=>$pages);
    }

    /**
     * 消息推送
     * $data = array("send_type"=>$send_type,"user_ids"=>$user_ids,"send_user_id"=>$send_user_id,"send_status"=>$send_status,"content"=>$content);
     * return array("status"=>$status);
     */
    public function send($data){

        $send_type = trim($data['send_type']);//消息类型
        $user_ids = $data['user_ids'];//推送会员，array(1,2,3,4,5);
        $send_user_id = (int)$data['send_user_id'];//发送人
        $send_status = (int)$data['send_status'];//0 仅消息,1 推送, 2 消息+推送
        $content = trim($data['content']);//推送内容

        if(empty($send_type)){
            $root['status'] = 10021;
            return $root;
        }
        if(sizeof($user_ids)<=0){
            $root['status'] = 10033;
            return $root;
        }
        $create_time = get_gmtime();
        $message['type'] = $send_type;
        $message['send_id'] = $send_user_id;
        $message['content'] = $content;
        $message['create_time'] = $create_time;
        $message['create_date'] = to_date($create_time,'Y-m-d H:i:s');
        $message['create_time_ymd'] = to_date($create_time,'Y-m-d');
        $message['create_time_y'] = to_date($create_time,'Y');
        $message['create_time_m'] = to_date($create_time,'m');
        $message['create_time_d'] = to_date($create_time,'d');
        $message['is_read'] = 0;
        $message['send_user_name'] = '官方';
        if($send_user_id){
            $send_user_name = $GLOBALS['db']->getOne("select nick_name from ".DB_PREFIX."user where id=".$send_user_id,true,true);
            if($send_user_name!=''){
                $message['send_user_name'] = $send_user_name;
            }
        }
        $count = 0;
        foreach($user_ids as $v) {
            //消息入库
            if ($send_status == 0 || $send_status == 2) {
                $message['user_id'] = (int)$v;
                $GLOBALS['db']->autoExecute(DB_PREFIX . "user_notice", $message, "INSERT");
                if ($GLOBALS['db']->insert_id()) {
                    $count++;
                }
            }
        }
        if($count){
            $root['status'] = 1;
        }else{
            $root['status'] = 10022;
        }
        return $root;
    }


    /**
     * 删除消息
     * $data = array("id"=>$id,"user_id"=>$user_id);
     * return array("status"=>$status);
     */
    public function del($data){

        $id = (int)$data['id'];//收货地址id
        $user_id = (int)$data['user_id'];//所有用户id

        $GLOBALS['db']->query("delete from ".DB_PREFIX."user_notice where id = ".$id." and user_id = ".$user_id);
        if($GLOBALS['db']->affected_rows()>0){
            $root['status'] = 1;
        }else{
            $root['status'] = 10023;
        }
        return $root;
    }

    /**
     * IM推送服务
     * $data = array("pai_id"=>$pai_id,"page"=>$page,"page_size"=>$page_size);
     * return array("rs_count"=>$rs_count,"info"=>$info,"joins"=>$joins,"page"=>$page);
     */
    public function paiinfo($data){
        $pai_id = (int)$data['pai_id'];
        $page = (int)$data['page'];
        $page_size = (int)$data['page_size'];

        $limit = (($page-1)*$page_size).",".$page_size;

        $joins = array();
        $rs_count = 0;
        $pages['page'] = $page;
        $pages['has_next'] = 0;

        $info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."pai_goods where id = ".$pai_id,true,true);
        if($info){
            $rs_count = $GLOBALS['db']->getOne("SELECT count(*) FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id,true,true);

            if($rs_count > 0){
                $joins = $GLOBALS['db']->getAll("SELECT * FROM ".DB_PREFIX."pai_join WHERE pai_id=".$pai_id." ORDER BY pai_diamonds desc limit ".$limit,true,true);
                foreach($joins as $k=>$v){
                    if($v['consignee_district']!=''){
                        $joins[$k]['consignee_district'] = json_decode($v['consignee_district'],true);
                        if($joins[$k]['consignee_district']==''){
                            $joins[$k]['consignee_district'] = array();
                        }
                    }else{
                        $joins[$k]['consignee_district'] = array();
                    }
                }
                $total = ceil($rs_count/$page_size);
                if($total > $page)
                    $pages['has_next'] = 1;
            }
        }

        return array("info"=>$info,"joins"=>$joins,"page"=>$pages,"rs_count"=>$rs_count);
    }

    /**
     * 消息内容
     * $data = array("user_id"=>$user_id,"id"=>$id);
     * return array("status"=>$status,"data"=>$data);
     */
    public function info($data){

        $status = 1;
        $user_id = (int)$data['user_id'];
        $id = (int)$data['id'];

        $info = $GLOBALS['db']->getRow("SELECT * FROM ".DB_PREFIX."user_notice WHERE user_id=".$user_id." and id = ".$id,true,true);

        if(empty($info)){
            $status = 10001;
        }

        return array("status"=>$status,"data"=>$info);
    }

}


?>