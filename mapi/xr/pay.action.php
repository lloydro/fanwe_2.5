<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class payCModule  extends baseCModule
{
    var $type_order = array(
        'red_photo'=>11,
        'weixin'=>12,
        'goods'=>13,
        'photo'=>14,
        'reward'=>15,
        'chat'=>16,
    );
    var $type_list = array(
        'red_photo','weixin','goods','photo','reward','chat'
    );
    var $user_type_list = array(
       'weixin','reward','chat'
    );
    public function pay_list(){
        $root = array();
        $root['status'] = 1;
        $root['error'] = '';
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }
        $pay_list = load_auto_cache("pay_list");

        $root['list'] = $pay_list;
        $root['weixin_price'] = $GLOBALS['db']->getOne("select weixin_price  from ".DB_PREFIX."user where id = ".$GLOBALS['user_info']['id']);
        ajax_return($root);
    }
    /**
     * 用户充值支付
     */
    public function pay(){

        $root = array();
        $root['status'] = 1;
        $root['error'] = "";
        //$GLOBALS['user_info']['id'] = 1;
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 2;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);//用户ID

            $weibo_id = intval($_REQUEST['weibo_id']);//支付项目id
            if(!$weibo_id){
                $root['error'] = "动态id无效";
                $root['status'] = 0;
                ajax_return($root);
            }
            $weibo = $GLOBALS['db']->getRow("select type,price,user_id from ".DB_PREFIX."weibo where id = ".$weibo_id);
            if(!$weibo){
                $root['error'] = "该动态id无效";
                $root['status'] = 0;
                ajax_return($root);
            }
            $to_user_id =  $weibo['user_id'];
            if($to_user_id==$user_id){
                $root['error'] = "无法购买自己的商品！";
                $root['status'] = 0;
                ajax_return($root);
            }
            $type = $weibo['type'];
            if(!in_array($type,$this->type_list)){
                $root['error'] = "该动态类型无效";
                $root['status'] = 0;
                ajax_return($root);
            }
            $money = floatval($_REQUEST['money']);//支付金额
            if($weibo['price']!=$money){
                $root['error'] = "金额无效!";
                $root['status'] = 0;
                ajax_return($root);
            }

            $pay_id = intval($_REQUEST['pay_id']);//支付id

            if($pay_id == 0){
                $root['error'] = "支付id无效";
                $root['status'] = 0;
            }elseif( $money == 0){
                $root['error'] = "动态id无效或充值金额不能为0";
                $root['status'] = 0;
            }else{
                $sql = "select id,name,class_name,logo from ".DB_PREFIX."payment where online_pay = 3 and id =".$pay_id;
                $pay = $GLOBALS['db']->getRow($sql,true,true);

                if(!$pay){
                    $root['error'] = "支付id无效";
                    $root['status'] = 0;
                }else{
                        $payment_notice['create_time'] = NOW_TIME;
                        $payment_notice['user_id'] = $user_id;
                        $payment_notice['payment_id'] = $pay_id;
                        $payment_notice['money'] = $money;
                        $type_des = array(
                            'red_photo'=>'图片红包',
                            'goods'=>'购买虚拟商品',
                            'photo'=>'购买写真图片',
                            'weixin'=>'购买微信号',
                            'reward'=>'打赏',
                            'chat'=>'聊天付费',
                        );
                        $payment_notice['recharge_name'] = $type_des[$type];
                        $payment_notice['type'] = 11;
                        $payment_notice['type_cate'] = $type;
                        $payment_notice['order_id'] = $weibo_id;
                        $payment_notice['to_user_id'] = $weibo['user_id'];
                        do{
                            $payment_notice['notice_sn'] = to_date(NOW_TIME,"YmdHis").rand(100,999);
                            $GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$payment_notice,"INSERT","","SILENT");
                            $notice_id = $GLOBALS['db']->insert_id();
                        }while($notice_id==0);

                    $class_name = $pay['class_name']."_payment";
                    fanwe_require(APP_ROOT_PATH."system/payment/".$class_name.".php");
                    $o = new $class_name;
                    $pay= $o->get_payment_code($notice_id);

                    $root['pay'] = $pay;
                }
            }
        }

        ajax_return($root);
    }
    public function pay_user(){
        $root = array();
        $root['status'] = 1;
        $root['error'] = "";
        //$GLOBALS['user_info']['id'] = 1;
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 2;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }else{
            $user_id = intval($GLOBALS['user_info']['id']);//用户ID

            $to_user_id = intval($_REQUEST['to_user_id']);//支付项目id
            if(!$to_user_id){
                $root['error'] = "会员id无效";
                $root['status'] = 0;
                ajax_return($root);
            }

            if($to_user_id==$user_id){
                $root['error'] = "无法给自己打赏！";
                $root['status'] = 0;
                ajax_return($root);
            }

            $type = strim($_REQUEST['type']);
            if(!in_array($type,$this->user_type_list)){
                $root['error'] = "该动态类型无效";
                $root['status'] = 0;
                api_ajax_return($root);
            }
            $account= '';
            if($type=='weixin'){
                $account = strim($_REQUEST['account']);
                $sql_demo = "select weixin_account from ".DB_PREFIX."user where  id =".$to_user_id;
                $memo = $GLOBALS['db']->getOne($sql_demo,true,true);
                
                if(!$account){
                    $root['error'] = "请输入微信账号!";
                    $root['status'] = 0;
                    api_ajax_return($root);
                }
            }
            $money = floatval($_REQUEST['money']);//支付金额

            $pay_id = intval($_REQUEST['pay_id']);//支付id

            if($pay_id == 0){
                $root['error'] = "支付id无效";
                $root['status'] = 0;
            }elseif( $money == 0){
                $root['error'] = "充值金额不能为0";
                $root['status'] = 0;
            }else{
                $sql = "select id,name,class_name,logo from ".DB_PREFIX."payment where online_pay = 3 and id =".$pay_id;
                $pay = $GLOBALS['db']->getRow($sql,true,true);

                if(!$pay){
                    $root['error'] = "支付id无效";
                    $root['status'] = 0;
                }else{
                    $payment_notice['create_time'] = NOW_TIME;
                    $payment_notice['user_id'] = $user_id;
                    $payment_notice['payment_id'] = $pay_id;
                    $payment_notice['money'] = $money;
                    $type_des = array(
                        'red_photo'=>'图片红包',
                        'goods'=>'购买虚拟商品',
                        'photo'=>'购买写真图片',
                        'weixin'=>'购买微信号',
                        'reward'=>'打赏',
                        'chat'=>'聊天付费',
                    );
                    $payment_notice['pay_user_info'] = $account;
                    $payment_notice['recharge_name'] = $type_des[$type];
                    $payment_notice['type'] = 11;
                    $payment_notice['type_cate'] = $type;
                    $payment_notice['to_user_id'] = $to_user_id;
                    $payment_notice['from_user_info'] = $account;
                    $payment_notice['memo'] = $memo;
                    do{
                        $payment_notice['notice_sn'] = to_date(NOW_TIME,"YmdHis").rand(100,999);
                        $GLOBALS['db']->autoExecute(DB_PREFIX."payment_notice",$payment_notice,"INSERT","","SILENT");
                        $notice_id = $GLOBALS['db']->insert_id();
                    }while($notice_id==0);

                    $class_name = $pay['class_name']."_payment";
                    fanwe_require(APP_ROOT_PATH."system/payment/".$class_name.".php");
                    $o = new $class_name;
                    $pay= $o->get_payment_code($notice_id);

                    $root['pay'] = $pay;
                }
            }
        }

        ajax_return($root);
    }
    //
    public function pay_user_info(){
        if(!$GLOBALS['user_info']){
            $root['error'] = "用户未登陆,请先登陆.";
            $root['status'] = 0;
            $root['user_login_status'] = 0;//有这个参数： user_login_status = 0 时，表示服务端未登陆、要求登陆，操作
        }
        $to_user_id = intval($_REQUEST['to_user_id']);
        if(!$to_user_id){
            $root['error'] = "会员ID不能为空.";
            $root['status'] = 0;
        }
        $to_user = $GLOBALS['db']->getRow("select id as user_id,nick_name,head_image,is_authentication from ".DB_PREFIX."user where id = ".$to_user_id);
        if(!$to_user){
            $root['error'] = "会员信息不存在.";
            $root['status'] = 0;
        }
        $to_user['head_image'] = deal_weio_image($to_user['head_image'],'head_image');
        $root['user'] = $to_user;
        $root['money_list'] = array(
            '0.01','8','25','69','122','258','520','666','1314','1999'
        );
        $root['status'] = 1;
        api_ajax_return($root);
    }

}