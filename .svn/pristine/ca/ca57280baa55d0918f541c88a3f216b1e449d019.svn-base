<?php
class UserInvestorAction extends CommonAction{
	public function index(){
        if(intval($_REQUEST['id'])>0)
        {
            $map[DB_PREFIX.'user.id'].= intval($_REQUEST['id']);
        }
        if(trim($_REQUEST['nick_name'])!='')
        {
            $map[DB_PREFIX.'user.nick_name'] = array('like','%'.trim($_REQUEST['nick_name']).'%');
        }
        if(trim($_REQUEST['contact'])!='')
        {
            $map[DB_PREFIX.'user.contact'] = array('like','%'.trim($_REQUEST['contact']).'%');
        }
        if(trim($_REQUEST['mobile'])!='')
        {
            $map[DB_PREFIX.'user.mobile'] = array('like','%'.trim($_REQUEST['mobile']).'%');
        }

        $map[DB_PREFIX.'user.is_authentication'] = 1;
        $map[DB_PREFIX.'user.is_effect'] = 1;
        $map[DB_PREFIX.'user.is_robot'] = 0;
        if (method_exists ( $this, '_filter' )) {
            $this->_filter ( $map );
        }

        //$name=$this->getActionName();
        $model = D ('User');
        if (! empty ( $model )) {
            $this->_list ( $model, $map );
        }
        $this->display ();
	}
    public function batch_content(){
        $id=$_REQUEST['id'];
        $implode_id = implode(',',(explode(',',$id)));

        $user = $GLOBALS['db']->getAll("select id,authentication_name,identify_number,identify_positive_image,identify_nagative_image,identify_hold_image,is_authentication from ".DB_PREFIX."user where id in($implode_id)");

        //是否显示身份证号码
        $m_config = load_auto_cache("m_config");
        $show_identify_number = intval($m_config['is_show_identify_number']);
        $this->assign('show_identify_number',$show_identify_number);

        $this->assign('list',$user);

        $this->assign('implode_id',$implode_id);
//        $this->assign('show_bnt',$show_bnt);
        $this->display();
    }
	public function show_content(){
		$id=intval($_REQUEST['id']);
		$status=intval($_REQUEST['status']);
		
		$user=M("user")->getById($id);
		if($status==1){
			$user['do_info']='审核通过';
		}elseif($status==3){
			$user['do_info']='审核';
			$show_bnt=3;
		}else{
			$user['do_info']='审核不通过';
		}
		$user['is_investor_name']=get_investor($user['user_type']);
		$user['investor_status_name']=get_investor_status($user['is_authentication']);
		
		$user['identify_hold_image']=get_spec_image($user['identify_hold_image']);
		$user['identify_positive_image']=get_spec_image($user['identify_positive_image']);
		$user['identify_nagative_image']=get_spec_image($user['identify_nagative_image']);

		$data['order_type'] = 'shop';
		$data['order_status'] = 4;
		$data['viewer_id'] = $id;

//		if(define(DISTRIBUTION_MODULE) && DISTRIBUTION_MODULE == 1){
//			$order = M('goods_order')->where($data)->count('id');
//			$user['order'] = intval($order);
//		}
        //是否显示身份证号码
        $m_config = load_auto_cache("m_config");
        $show_identify_number = intval($m_config['is_show_identify_number']);
        //①用户认证状态为非默认状态(未认证) AND 身份证号码为空, 则不显示身份证号码
        if(intval($user['is_authentication'])!=0 && trim($user['identify_number'])==''){
            $show_identify_number = 0;
        }
//        //②用户认证状态为未通过(3)状态 AND 需要身份验证, 则显示身份验证
//        if(intval($user['is_authentication'])==3 && intval($m_config['is_show_identify_number'])==1){
//            $show_identify_number = 1;
//        }        
        //查询有哪些家族 ljz
        if((defined('OPEN_FAMILY_JOIN')&&OPEN_FAMILY_JOIN==1)){
            $family = $GLOBALS['db']->getAll("select name,id,user_id from " . DB_PREFIX . "family");
            $family_bj = 0;//标记位，记录是否是族长
            //判断该成员是否是族长
            foreach ($family as $key => $val){
                if($id == $val['user_id']){
                    $family = array();
                    $family[] = array('id' => $val['id'],'name' => $val['name']);
                    $family_bj = 1;
                }
            }
            if($family_bj == 0){
                $family = $GLOBALS['db']->getAll("select name,id,user_id,status from " . DB_PREFIX . "family where status=1");
            }
            //查询该成员的家族ID
            $user_family = $GLOBALS['db']->getOne("select family_id from ".DB_PREFIX."user where id=".$id);
            $this->assign('user_family',$user_family);
            
            $this->assign('family',$family);
        }else{
            $family = 0;
            $this->assign('family',$family);
        }
        //公会邀请码是否开启
        if(defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE && $m_config['society_pattern'] && $m_config['open_society_code']){
            $this->assign('open_code',1);
        }else {
            $this->assign('open_code',0);
        }
        
 		$this->assign('user',$user);
		$this->assign('status',$status);
		$this->assign('show_bnt',$show_bnt);
        $this->assign('show_identify_number',$show_identify_number);
		$this->display();
 	}
    public function investor_go_allow()
    {
        $id = $_REQUEST['id'];

        $status = intval($_REQUEST['is_authentication']);
        $v_explain = strim($_REQUEST['v_explain']);
        
        
        if ($_REQUEST['investor_send_info']) {
            $investor_send_info = strim($_REQUEST['investor_send_info']);
        }
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
        $user_redis = new UserRedisService();
        $user = $GLOBALS['db']->getAll("select * from " . DB_PREFIX . "user where id in($id)");
        foreach ($user as $k => $v) {
            $authentication_type[$k]=$user[$k]['authentication_type'];
            $user_id[$k]=$user[$k]['id'];
        }


            if ($user) {
                for($i=0;$i<count($user);$i++) {
                    //$user_data['id'] = $user['id'];
                    $user_data['is_authentication'] = $status;
                    if ($status == 3) {
                        $m_config = load_auto_cache("m_config");
                        $user_data['v_explain'] = '';
                        $user_data['v_icon'] = '';
                        $user_data['investor_time'] = get_gmtime() + $m_config['attestation_time'];

                    } else {
                        $user_data['v_explain'] = $v_explain;
                        if($user_data['v_explain']==''){
                            $user_data['v_explain'] =$authentication_type[$i];
                        }
                        $user_data['v_icon'] = get_spec_image(M('AuthentList')->where("name='" . trim($authentication_type[$i] . "'"))->getField("icon"));
                    }

                    //认证ID
                    $user_data['authent_list_id'] = get_spec_image(M('AuthentList')->where("name='" . trim($authentication_type[$i] . "'"))->getField("id"));

                    if ($investor_send_info) {
                        $user_data['investor_send_info'] = $investor_send_info;
                    } else {
                        $user_data['investor_send_info'] = '';
                    }
                    
                    //ljz
                    if((defined('OPEN_FAMILY_JOIN')&&OPEN_FAMILY_JOIN==1)){
                        $family_id = intval($_REQUEST['family']);//家族ID
                        if($family_id){//更新家族ID
                            $user_data['family_id'] = $family_id;
                        }else{
                            $user_data['family_id'] = 0;
                        }
                    }
                    
                    //公会邀请码ljz
                    if(defined('OPEN_SOCIETY_MODULE') && OPEN_SOCIETY_MODULE){
                        $m_config = load_auto_cache("m_config");
                        if($m_config['society_pattern'] != 0 && $m_config['open_society_code'] == 1 && $status == 2 && strlen($user[$i]['society_code']) > 0){
                            //查询用户是否已经有公会
                            if($user[$i]['society_id'] > 0){
                                $GLOBALS['db']->query("update ".DB_PREFIX."user set society_code=null where id=".$user_id[$i]);
                                $user_data['society_code'] = null;
                                //$this->error('抱歉你已有公会，公会邀请码失效');
                            }else{
                                //查询邀请码对应的公会
                                $society_id = $GLOBALS['db']->getOne("select id from ".DB_PREFIX."society where status=1 and society_code='".$user[$i]['society_code']."'");
                                if(!empty($society_id)){
                            
                                    $oTime = time();//获取当前时间戳
                                    //$GLOBALS['db']->query("update ".DB_PREFIX."user set society_id=".$society_id." where id=".$user_id[$i]);
                                    //$user_data['society_id'] = $society_id;
                                    //$GLOBALS['db']->query("delete from " . DB_PREFIX . "society_apply where society_id=".$society_id." and apply_type=0 and user_id=".$user_id[$i]);
                                    //$GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply',array('society_id'=>$society_id,'user_id'=>$user_id[$i],'create_time'=>$oTime,'apply_type'=>0,'status'=>0));
                                    $GLOBALS['db']->autoExecute(DB_PREFIX . 'society_apply',array('society_id'=>$society_id,'user_id'=>$user_id[$i],'create_time'=>$oTime,'apply_type'=>0,'status'=>0));
                                }else{
                                    $user_data['society_code'] = null;
                                    //$this->error('公会邀请码不存在');
                                }
                            }
                            
                        }else{
                            $user_data['society_code'] = null;
                        }
                    }

                    /*$list = M("User")->save($user_data);
                   if ($list !== false){
                       save_log($user_data['id']."审核操作成功",1);
                   }else{
                       save_log($user_data['id']."审核操作失败",0);
                   }*/
                    $where = "id=".intval($user_id[$i]);
                    if ($GLOBALS['db']->autoExecute(DB_PREFIX . "user", $user_data, 'UPDATE', $where)) {
                        save_log($user_id[$i] . "审核操作成功", 1);
                    } else {
                        save_log($user_id[$i] . "审核操作失败", 0);
                    }
                    //redis化
                    $user_redis->update_db($user_id[$i], $user_data);
                    //send_investor_status($user_data);
                }
                $this->success("操作成功");
            } else {
                $this->error("没有该会员信息");
            }


    }
 	
 	
}
?>