<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------
class chatCModule extends baseCModule
{
    //推荐
    public function index()
    {
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $page = intval($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $page_size = 6;
        $limit = (($page-1) * $page_size) . "," . $page_size;
        $root = array(
            'has_next'=>1,
            'page'=>$page,
            'status'=>1,
            'error'=>''
        );

        $user_list = $GLOBALS['db']->getAll("select id as user_id,head_image,city,nick_name from ".DB_PREFIX."user where weibo_count>0 or is_authentication = 2  limit $limit");
        foreach($user_list as $k=>$v){
            if($v){
                $user_list[$k]['head_image'] = deal_weio_image($v['head_image'],'head_image');
                if(!$v['city']){
                    $user_list[$k]['city'] = '喵星';
                }
            }
        }
        $root['user_list'] = $user_list;
        if(count($user_list)==$page_size){
            $root['has_next'] = 1;
        }else{
            $root['has_next'] = 0;
        }
        $user_id = $GLOBALS['user_info']['id'];
        $sql = "select count(*)  from ".DB_PREFIX."weibo_comment as com left join ".DB_PREFIX."weibo as wei on com.weibo_id = wei.id
         where ((com.to_user_id = $user_id and com.user_id != $user_id) or wei.user_id = $user_id) and com.is_read = 0 and com.is_del = 0";
        $comment_count = $GLOBALS['db']->getOne($sql);
//        log_result($sql);
        $root['comment_count'] = intval($comment_count);
        api_ajax_return($root);
    }
    //评论列表
    public function my_comment(){
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            api_ajax_return($root);
        }
        $user_id = $GLOBALS['user_info']['id'];
        $page = intval($_REQUEST['page'])?intval($_REQUEST['page']):1;
        $page_size = 20;
        $limit = (($page-1) * $page_size) . "," . $page_size;
        $root = array(
            'has_next'=>1,
            'page'=>$page,
            'status'=>1,
            'error'=>''
        );
        $sql = "select com.comment_id,com.content,com.user_id,com.type as comment_type,u.nick_name,u.head_image,com.create_time,com.weibo_id,w.user_id as weibo_user_id,us.nick_name as weibo_user_name,us.head_image as weibo_head_image,w.photo_image as weibo_photo_image,w.data as weibo_images,w.type as weibo_type,w.content as weibo_content  from ".DB_PREFIX."weibo_comment as com
        left join ".DB_PREFIX."user as u on com.user_id = u.id
         left join ".DB_PREFIX."weibo as w on w.id = com.weibo_id
           left join ".DB_PREFIX."user as us on w.user_id = us.id
         where  ((com.to_user_id = $user_id and com.user_id != $user_id) or w.user_id = $user_id) and com.type = 1 order by com.create_time desc    limit $limit";
        $comment_list = $GLOBALS['db']->getAll($sql);

        foreach($comment_list as $k=>$v){
            if($v){

                $has_first = 0;
                $comment_list[$k]['head_image'] = deal_weio_image($v['head_image'],'head_image');
                if($v['weibo_photo_image']){
                    $comment_list[$k]['weibo_photo_image'] = deal_weio_image($v['weibo_photo_image']);
                    $has_first = 1;
                }
                if($v['weibo_images']){
                    $weibo_images = unserialize($v['weibo_images']);
                    $comment_list[$k]['weibo_images'] = $weibo_images;
                    if(!$has_first){
                        if($weibo_images[0]['is_model']){
                            $comment_list[$k]['weibo_photo_image'] =deal_weio_image($v['weibo_head_image'],'head_image') ;
                        }else{
                            $comment_list[$k]['weibo_photo_image'] = $weibo_images[0]['url'];
                        }
                    }
                }
                unset( $comment_list[$k]['weibo_images']);
                unset( $comment_list[$k]['weibo_head_image']);
            }else{
                unset( $comment_list[$k]);
            }
        }
        $root['comment_list'] = $comment_list;
        if(count($comment_list)==$page_size){
            $root['has_next'] = 1;
        }else{
            $root['has_next'] = 0;
        }
        //设置已经读取
        $sql = "select com.comment_id  from ".DB_PREFIX."weibo_comment as com left join ".DB_PREFIX."weibo as wei on com.weibo_id = wei.id
         where ((com.to_user_id = $user_id and com.user_id != $user_id) or wei.user_id = $user_id) and com.is_read = 0 ";
        $comment_list = $GLOBALS['db']->getAll($sql);
        $comment_ids = array();
        foreach($comment_list as $k=>$v){
                if($v['comment_id']){
                    $comment_ids[] = $v['comment_id'];
                }
        }
        if(count($comment_ids)>0){
            $GLOBALS['db']->query("update ".DB_PREFIX."weibo_comment set is_read = 1 where comment_id in (".implode(',',$comment_ids).") ");

        }

        api_ajax_return($root);
    }


}

?>