<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class XyPromoterTwoAction extends CommonAction
{
    public function index()
    {
        $map['pid']=array('gt',0);
        $map['status']=array('eq',1);
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . trim($_REQUEST['name']) . '%');
        }

        $mobile = strim($_REQUEST['mobile']);
        if ($mobile != '') {
            $map['mobile'] = array('eq', $mobile);
        }

        $id = intval($_REQUEST['id']);
        if ($id) {
            $map['id'] = array('eq', $id);
        }

        $create_time_2=empty($_REQUEST['create_time_2'])?to_date(get_gmtime(),'Y-m-d'):strim($_REQUEST['create_time_2']);
        $create_time_2=to_timespan($create_time_2)+24*3600;
        if(trim($_REQUEST['create_time_1'])!='')
        {
            $map["create_time"] = array('between',array(to_timespan($_REQUEST['create_time_1']),$create_time_2));
        }

        $user_id = intval($_REQUEST['user_id']);
        if($user_id){
            $map['user_id'] = array('eq', $user_id);
        }

        $is_effect = intval($_REQUEST['is_effect']);
        if(isset($_REQUEST['is_effect']) && $is_effect >=0){
            $map['is_effect'] = array('eq', $is_effect);
        }

        $parent_name = strim($_REQUEST['parent_name']);
        if($parent_name){
            $parents=$GLOBALS['db']->getALL("select user_id from ".DB_PREFIX."bm_promoter where name like '%".$parent_name."%' and pid=0");
            if($parents){
                $parent_ids=array_map('array_shift',$parents);
                $map['pid'] = array('in', $parent_ids);
            }else{
                $map['pid'] = -1;
            }
        }

        $pid = intval($_REQUEST['pid']);
        if($pid){
            $map['pid'] = array('eq', $pid);
        }

        if (method_exists($this, '_filter')) {
            $this->_filter($map);
        }
        $name = 'BmPromoter';
        $model = M($name);
        if (!empty ($model)) {
            $this->_list($model, $map);
        }

        $this->display();
    }


    public function edit()
    {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M("BmPromoter")->where($condition)->find();
        $vo['parent_name'] = M("BmPromoter")->where("user_id=".intval($vo['pid']))->getField("name");
        $vo['user'] = M("User")->where("id=".intval($vo['user_id'])."")->find();

        $this->assign('vo', $vo);

        $this->display();
    }

    public function update()
    {
        B('FilterString');
        $data = M("BmPromoter")->create();

        $data_info = M("BmPromoter")->where("id=" . intval($data['id']))->find();
        $log_info = $data_info['name'];
        if (!$data_info) {
            $this->error("请选择编辑的推广商");
        }
        if (!check_empty($data['mobile'])) {
            $this->error("登录手机号不能为空");
        }

        if (!check_mobile($data['mobile'])) {
            $this->error("请输入正确的手机号");
        }

        if (!check_empty($data['name'])) {
            $this->error("推广商名称不能为空");
        }

        $binding_mobile=strim($_REQUEST['binding_mobile']);
        if (!check_empty($binding_mobile)) {
            $this->error("绑定会员不能为空");
        }

        $check_user_info=$this->check_user($binding_mobile,$data_info['user_id']);
        $check_user=$check_user_info['user'];
        $check_user_id=intval($check_user['id']);
        if($check_user_info['status'] ==0){
            $this->error($check_user_info['info']);
        }

        // 更新数据
        if($data['pwd'] !=''){
            $data['pwd']=md5($data['pwd']);
        }else{
            unset($data['pwd']);
        }
        if($check_user_id>0 && $data_info['user_id'] != $check_user_id){
            $data['user_id']=$check_user_id;
        }
        $list = M("BmPromoter")->save($data);
        if (false !== $list) {
            if($check_user_id>0 && $data_info['user_id'] >0 && $data_info['user_id'] != $check_user_id){
                $GLOBALS['db']->query("update ".DB_PREFIX."user set bm_pid= ".intval($check_user_id)." where bm_pid=".intval($data_info['user_id'])."");
            }
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function set_effect()
    {
        $id = intval($_REQUEST['id']);
        $ajax = intval($_REQUEST['ajax']);
        $user_info = M("BmPromoter")->getById($id);
        $c_is_effect = M("BmPromoter")->where("id=".$id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        $result=M("BmPromoter")->where("id=".$id)->setField("is_effect",$n_is_effect);
        save_log($user_info['user_name'].l("SET_EFFECT_".$n_is_effect),1);
        $this->ajaxReturn($n_is_effect,l("SET_EFFECT_".$n_is_effect),1);
    }

    //账户管理
    public function account()
    {
        require_once APP_ROOT_PATH."/admin/Lib/Action/UserCommonAction.class.php";
        require_once APP_ROOT_PATH."/system/libs/user.php";
        $common = new UserCommon();
        $data = $_REQUEST;
        $status = $common->account($data);
    }
    //账户修改
    public function modify_account()
    {
        require_once APP_ROOT_PATH."/admin/Lib/Action/UserCommonAction.class.php";
        require_once APP_ROOT_PATH."/system/libs/user.php";
        $common = new UserCommon();
        $data = $_REQUEST;
        $status = $common->modify_account($data);
        if($status){
            $this->success(L("UPDATE_SUCCESS"));
        }else{
            $this->error("累计充值数据有误！");
        }
    }

    public function delete()
    {
    }



    public function foreverdelete()
    {
    }


    //检查绑定会员
    public function check_user($binding_mobile,$binding_user_id_old)
    {
        $return=array("status"=>0,"info"=>"");
        $binding_mobile=$binding_mobile?$binding_mobile:strim($_REQUEST['binding_mobile']);
        $binding_user_id_old=$binding_user_id_old?$binding_user_id_old:intval($_REQUEST['binding_user_id_old']);
        $ajax=intval($_REQUEST['ajax']);
        if(!check_mobile($binding_mobile)){
            $return["info"]="请输入正确的会员的手机号";
            $this->return_info($return,$ajax);
        }

        //是否有会员
        $user_info=$GLOBALS['db']->getRow("select id,bm_pid,is_effect,nick_name,society_id from ".DB_PREFIX."user where mobile= ".$binding_mobile." ");
        if(!$user_info){
            $return["status"]=0;
            $return["info"]="会员未注册，请注册后再绑定";
            return $this->return_info($return,$ajax);
        }

        //编辑时，如果和原来的user_id值一样，直接返回会员信息
        if($binding_user_id_old == $user_info['id']){
            $return["status"]=1;
            $return["user"]=$user_info;

            return $this->return_info($return,$ajax);
        }

        //会员是否有效
        if($user_info['is_effect'] ==0){
            $return["status"]=0;
            $return["info"]="无效的会员";
            return $this->return_info($return,$ajax);
        }

        //是否是三级推广会员
        if($user_info['bm_pid'] >0){
            $return["status"]=0;
            $return["info"]="该会员已是公会，不能绑定";
            return $this->return_info($return,$ajax);
        }

        //是否是公会成员
        if($user_info['society_id'] >0){
            $return["status"]=0;
            $return["info"]="该会员已是公会成员，不能绑定";
            return $this->return_info($return,$ajax);
        }

        //是否已是绑定推广商
        $count_promoter=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where user_id=".intval($user_info['id'])."");
        if($count_promoter >0){
            $return["status"]=0;
            $return["info"]="该会员被绑定了";
            return $this->return_info($return,$ajax);
        }

        $return["status"]=1;
        $return["user"]=$user_info;

        return $this->return_info($return,$ajax);
    }

    public function return_info($data,$ajax)
    {

        if(intval($ajax) ==1){
            echo admin_ajax_return($data);
            exit;
        }else{
            return $data;
        }
    }

    //收礼物日志
    public function closed_prop(){
        $now=get_gmtime();
        $promoter_id = intval($_REQUEST['promoter_id']);
        $promoter_info=$GLOBALS['db']->getRow("select id,name,user_id from ".DB_PREFIX."bm_promoter where id= ".$promoter_id."");
        if(!$promoter_info){
            $this->error("请选择查看对象");
        }

        $prop_list = M("prop")->where("is_effect <>0")->findAll();

        //输出搜索年月
        $current_Year = date('Y');
        $current_YM = to_date(NOW_TIME,'Ym');
        for ($i=0; $i<5; $i++)
        {
            $years_list[$i] = $current_Year - $i;
        }

        for ($i=01; $i<13; $i++)
        {
            $month_list[$i] = str_pad(0+$i,2,0,STR_PAD_LEFT);
        }

        $user_id = intval($promoter_info['user_id']);
        $user_ids=$GLOBALS['db']->getALL("select id from ".DB_PREFIX."user where society_id in (select society_id from ".DB_PREFIX."user where bm_pid=".$user_id.")");
        if(!$user_ids){
            $this->assign("promoter_info",$promoter_info);
            $this->assign("prop",$prop_list);
            $this->assign("years_list",$years_list);
            $this->assign("month_list",$month_list);
            $this->assign("list", array());
            $this->assign("count",intval($count));
            $this->assign('total_ticket',intval($total_ticket));
            $this->display ();
            exit;
        }

        $user_ids=array_map('array_shift',$user_ids);
        $where = "l.to_user_id in(".implode(',',$user_ids).")";
        $model = D ("video_prop");

        //赠送时间
        $years =isset($_REQUEST['years'])?strim($_REQUEST['years']):-1;
        $month =isset($_REQUEST['month'])?strim($_REQUEST['month']):-1;
        if($years !=-1 && $years==-1){
            $this->error("请选择月份");
        }
        if($years==-1 && $month !=-1){
            $this->error("请选择年份");
        }

        if($years !=-1 && $month !=-1){
            $time=$years.''.$month;
        }else{
            $time=$current_YM;
        }

        //查询ID
        $from_user_id = intval($_REQUEST['from_user_id']);
        if($from_user_id>0){
            $parameter.= "l.from_user_id=".$from_user_id. "&";
            $sql_w .= "l.from_user_id=".$from_user_id." and ";
        }
        //查询昵称
        $nick_name = strim($_REQUEST['nick_name']);
        if($nick_name!='')
        {
            $parameter.= "u.nick_name like " . urlencode ( '%'.$nick_name.'%' ) . "&";
            $sql_w .= "u.nick_name like '%".$nick_name."%' and ";

        }

        $prop_id = intval($_REQUEST['prop_id']);
        if($prop_id>0) {//查询礼物
            $parameter .= "l.prop_id=" . intval($data['prop_id']) . "&";
            $sql_w .= "l.prop_id=" . intval($data['prop_id']) . " and ";
        }

        $sql_str = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name
                         FROM   ".DB_PREFIX."video_prop_".$time." as l
                         LEFT JOIN ".DB_PREFIX."user AS u  ON l.from_user_id = u.id" ." LEFT JOIN ".DB_PREFIX."prop AS v ON l.prop_name = v.name" ."
                         WHERE $where "."  and ".$sql_w." 1=1  ";

        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM   ".DB_PREFIX."video_prop_".$time." as l
                         LEFT JOIN ".DB_PREFIX."user AS u  ON l.from_user_id = u.id" ." LEFT JOIN ".DB_PREFIX."prop AS v ON l.prop_name = v.name" ."
                         WHERE $where "."  and ".$sql_w." 1=1  ";

        $total_ticket_sql = "SELECT SUM(l.total_ticket)  as tpcount
                         FROM   ".DB_PREFIX."video_prop_".$time." as l
                         LEFT JOIN ".DB_PREFIX."user AS u  ON l.from_user_id = u.id" ." LEFT JOIN ".DB_PREFIX."prop AS v ON l.prop_name = v.name" ."
                         WHERE $where "."   and ".$sql_w." 1=1  ";

        $count = $GLOBALS['db']->getOne($count_sql);
        $total_ticket = $GLOBALS['db']->getOne($total_ticket_sql);

        $volist = $this->_Sql_list($model,$sql_str,'&'.$parameter,1,0,$count_sql);
        foreach($volist as $k=>$v){
            if($volist[$k]['prop_id']==12){
                $volist[$k]['total_ticket']='';
            }
            $volist[$k]['create_time']=date('Y-m-d',$volist[$k]['create_time']);
        }

        $this->assign("promoter_info",$promoter_info);
        $this->assign("prop",$prop_list);
        $this->assign("years_list",$years_list);
        $this->assign("month_list",$month_list);
        $this->assign("list", $volist);
        $this->assign("count",intval($count));
        $this->assign('total_ticket',intval($total_ticket));
        $this->display ();
    }
}

?>