<?php
/**
 *
 * @param unknown_type $to_user_id  被查看的人
 * @param unknown_type $user_id  查看人
 * @return Ambigous <mixed, multitype:number unknown mixed >
 */
function get_weibo_userinfo($to_user_id,$user_id=0,$page=1,$page_size=20,$pay_type= array()){
    $root = array();
    fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserFollwRedisService.php');
    fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/UserRedisService.php');
    if($page==1){

//        $user_redis = new UserRedisService();
//        $fields = array('id','fans_count','focus_count','is_authentication','authentication_type','nick_name','signature','sex','province','city','head_image',
//            'user_level','v_type','v_explain','v_icon',
//            'is_remind','birthday','job',
//            'is_robot','room_title','is_nospeaking',
//            'show_image','weibo_count','weixin_account','weixin_price','xpoint','ypoint','weibo_money','weibo_refund_money','weibo_photo_img','weibo_chat_price'
//        );

        //$userinfo = $user_redis->getRow_db($to_user_id,$fields);
        $userinfo = $GLOBALS['db']->getRow("select id,fans_count,focus_count,is_authentication,authentication_type,nick_name,signature,sex,province,city,head_image,user_level,v_type,v_explain,v_icon,is_remind,birthday,job,is_robot,room_title,is_nospeaking,show_image,weibo_count,weixin_account,
            weixin_price,xpoint,ypoint,weibo_money,weibo_refund_money,weibo_photo_img,weibo_chat_price from ".DB_PREFIX."user where id =$to_user_id");
        if($userinfo['id'] === false){
            $userinfo['id'] = $user_id;
        }
        foreach($userinfo as $k=>$v){
            if($v===false){
                if($k=='city'||$k=='head_image'||$k=='v_explain'||$k=='v_icon'){
                    $userinfo[$k] = '';
                }elseif($k=='user_level'){
                    $userinfo[$k] = 1;
                }
                else{
                    $userinfo[$k] = intval($v);
                }
            }
        }

        $userinfo['signature'] = htmlspecialchars_decode($userinfo['signature']);
        $userinfo['nick_name'] = htmlspecialchars_decode($userinfo['nick_name']);
        $userinfo['user_id'] = $to_user_id;
        if($userinfo['show_image']){
            $userinfo['show_image'] = unserialize( $userinfo['show_image']);

            foreach($userinfo['show_image'] as $k=>$v){
                if($v){
                    $userinfo['show_image'][$k] = array(
                        'url'=>get_spec_image($v,100,100,1),
                        'is_model'=>0,
                        'orginal_url'=>get_spec_image($v)
                    );
                }
            }
        }else{
            $userinfo['show_image'] = array();
        }
        $userinfo['head_image'] = deal_weio_image($userinfo['head_image'],'head_image');
        if($userinfo['weibo_photo_img']){
            $userinfo['weibo_photo_img'] = get_spec_image($userinfo['weibo_photo_img'],320,180,1);
        }else{
            $userinfo['weibo_photo_img'] = '';
        }

        if($user_id==$to_user_id){
            $root['is_admin'] = 1;
            $root['is_edit_weixin'] = 1;
            $root['is_show_money'] = 1;
            $root['is_show_talk'] = 0;
            $root['is_show_ds'] = 0;
            $userinfo['money'] = $userinfo['weibo_money'];
            $now=get_gmtime();
            $create_time_1=to_date($now,'Y-m-d');
            $create_time_1=to_timespan($create_time_1);
            $create_time_2=$create_time_1+24*3600;
            $today_money = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."payment_notice where to_user_id = ".$user_id." and is_paid = 1 and create_time >$create_time_1 and create_time<$create_time_2 ");
            $userinfo['today_money'] = floatval($today_money);
            $userinfo['has_focus'] = 0;
            $userinfo["has_weixin"] = 0;
            $userinfo['weibo_chatprice_hadpay'] = 1;
        }else{
            $root['is_admin'] = 0;
            $root['is_edit_weixin'] = 0;
            $root['is_show_money'] = 0;
            $root['is_show_talk'] = 1;
            $root['is_show_ds'] = 1;
            $userinfo['weibo_chatprice_hadpay'] = 0;;
            //关注
            $userfollw_redis = new UserFollwRedisService($user_id);
            if ($userfollw_redis->is_following($to_user_id)){
                $userinfo['has_focus'] = 1;//0:未关注;1:已关注
            }else{
                $userinfo['has_focus'] = 0;//0:未关注;1:已关注
            }
            $userinfo["has_weixin"] = 0;
            //是否支付过微信
            if($user_id>0){
//                $weixin_pay = $GLOBALS['db']->getOne("select order_id from ".DB_PREFIX."payment_notice where user_id = ".$user_id." and  is_paid =1 and type_cate = 'weixin' and to_user_id = ".$to_user_id);
//               if($weixin_pay){
//                   $userinfo['weibo_chatprice_hadpay'] = 1;
//               }
               $weixin_pay = $GLOBALS['db']->getOne("select sum(money) from ".DB_PREFIX."payment_notice where user_id = ".$user_id." and  is_paid =1 and type_cate = 'reward' and to_user_id = ".$to_user_id);
               if($weixin_pay >= $userinfo['weibo_chat_price']){
                   $userinfo['weibo_chatprice_hadpay'] = 1;
               }

            }

        }
        $root['user'] = $userinfo;
    }
    $root['is_focus'] = 0;
    if($to_user_id==$user_id) {
        $list = load_auto_cache("select_weibo_list", array('page' => $page, 'page_size' => $page_size, 'user_id' => $user_id));

    }else{
        $list = load_auto_cache("select_weibo_other_list", array('page' => $page, 'page_size' => $page_size, 'to_user_id' => $to_user_id));
            if($user_id>0){
                $pay_digg_list = load_auto_cache("select_user_pay_list",array('page'=>$page,'page_size'=>$page_size,'user_id'=>$user_id));
                $order_list_array = $pay_digg_list['order'];
                $diggs_array =  $pay_digg_list['digg'];

                //$root['is_focus'] = 0;
                $user_redis = new UserFollwRedisService($user_id);
                $root['is_focus']  = intval( $user_redis->is_following($to_user_id));
            }else{
                $order_list_array = array();
                $diggs_array =  array();
            }

            foreach($list as $k=>$v){
                if(in_array($v['weibo_id'],$diggs_array)){
                    $list[$k]['has_digg'] = 1;
                }
                if($user_id !=$v['user_id']){
                    $list[$k]['is_top'] = 0;
                }
                if($v['price']>0&&in_array($v['type'],$pay_type)){
                    if(in_array($v['weibo_id'],$order_list_array)){
                        $is_pay =1;
                    }else{
                        $is_pay =0;
                    }
                    if($list[$k]['images_count']>0){
                        $images = $list[$k]['images'] ;
                        foreach($images as $k1=>$v1){
                            if($v1['is_model']){
                                if($is_pay){
                                    $images[$k1]['url'] =  deal_weio_image($v1['url']);
                                    $images[$k1]['is_model'] =  0;
                                    $images[$k1]['orginal_url'] =  get_spec_image($v1['url']);
                                }else{
                                    $images[$k1]['url'] =  deal_weio_image($v1['url'],$v['type'],1);
                                    $images[$k1]['orginal_url'] = '';
                                }
                            }
                        }
                        $list[$k]['images'] = $images;
                    }

                }

            }

    }


    if(count($list)==$page_size){
        $root['has_next'] = 1;
    }else{
        $root['has_next'] = 0;
    }
    $root['list'] = $list;
    $root['page'] = $page;
    $root['status'] = 1;
    $root['error'] = 0;
    return $root;
}

function time_tran($the_time)
{
    $now_time = to_date(NOW_TIME,"Y-m-d H:i:s");
    $now_time = to_timespan($now_time);
    $show_time = to_timespan($the_time);
    $dur = $now_time - $show_time;
    if ($dur < 0) {
        return to_date($show_time,"Y-m-d");
    } else {
        if ($dur < 60) {
            return $dur . '秒前';
        } else {
            if ($dur < 3600) {
                return floor($dur / 60) . '分钟前';
            } else {
                if ($dur < 86400) {
                    return floor($dur / 3600) . '小时前';
                } else {
                    if ($dur < 2592000) {//30天内
                        return floor($dur / 86400) . '天前';
                    } else {
                        return to_date($show_time,"Y-m-d");
                    }
                }
            }
        }
    }
}

function get_file_oss_url($url){
    if($url){
        $pos = strpos($url, $GLOBALS['distribution_cfg']['OSS_DOMAIN']);
        $httppos = strpos($url, 'http');
        if ($pos === false && $httppos === false) {
            $url = str_replace("./public/", "/public/", $url);
            return $GLOBALS['distribution_cfg']['OSS_DOMAIN'] . $url;
        }else{
            return $url;
        }
    }
}

function deal_weio_image($url,$type='',$is_mode=0){
    if($type==''){
        return get_spec_image($url,200,200,1);
    }
    if($is_mode==0){
        switch($type){
            case 'photo_info':
                return get_spec_image($url,375,210,1);
                break;
            case 'video':
                if (defined('OPEN_SVIDEO_MODULE') && OPEN_SVIDEO_MODULE) {
                    return get_spec_image($url,250,250,1);
                } else {
                    return get_spec_image($url,250,140,1);
                }
                break;
            case 'video_info':
                return get_spec_image($url,375,210,1);
                break;
            case 'head_image':
                return get_spec_image($url,300,300,1);
                break;
            default:
                return get_spec_image($url,200,200,1);
                break;
        }
    }else{
        switch($type){
            case 'photo_info':
                return get_spec_image($url,375,210,1,30,15);
                break;
            case 'video_info':
                return get_spec_image($url,375,210,1,30,15);
                break;
            default:
                return get_spec_image($url,200,200,1,30,15);
                break;
        }
    }

}