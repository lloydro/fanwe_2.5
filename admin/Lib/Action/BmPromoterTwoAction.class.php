<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterTwoAction extends CommonAction
{
    public function index()
    {
        //更新推广商子集个数
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/models/bm_promoterModel.class.php');
        $bm_promoter_obj = new bm_promoterModel();
        $bm_promoter_obj->update_promoter_two_child(0,600,'admin');

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

        $binding_user_id = intval($_REQUEST['binding_user_id']);
        if($binding_user_id){
            $map['user_id'] = array('eq', $binding_user_id);
        }

        $is_effect = intval($_REQUEST['is_effect']);
        if(isset($_REQUEST['is_effect']) && $is_effect >=0){
            $map['is_effect'] = array('eq', $is_effect);
        }

        $parent_name = strim($_REQUEST['parent_name']);
        if($parent_name){
            $parents=$GLOBALS['db']->getALL("select user_id from ".DB_PREFIX."bm_promoter where name like '%".$parent_name."%'");
            if($parents){
                $parent_ids=array_map('array_shift',$parents);
                $map['pid'] = array('in', $parent_ids);
            }else{
                $map['pid'] = -1;
            }
        }

        $p_promoter_id = intval($_REQUEST['p_promoter_id']);
        if($p_promoter_id >0){
            $p_user_id=$GLOBALS['db']->getOne("select user_id from ".DB_PREFIX."bm_promoter where id=".$p_promoter_id." ");
            $map['pid'] = array('eq', $p_user_id);
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

        if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where mobile='".$data['mobile']."' and status =1 and id <> ".intval($data_info['id'])."") )>0){
            $this->error("登录手机号已存在");
        }

        if (!check_empty($data['name'])) {
            $this->error("推广商名称不能为空");
        }

        if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where name='".$data['name']."' and status =1 and id <> ".intval($data_info['id'])."") )>0){
            $this->error("推广商名称已存在");
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
        $user_info=$GLOBALS['db']->getRow("select id,bm_pid,is_effect,nick_name from ".DB_PREFIX."user where mobile= ".$binding_mobile." ");
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
            $return["info"]="该会员已是推广会员，不能绑定";
            return $this->return_info($return,$ajax);
        }

        //是否已是绑定推广商
        $count_promoter=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where user_id=".intval($user_info['id'])."");
        if($count_promoter >0){
            $return["status"]=0;
            $return["info"]="该会员已绑定推广商";
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
}

?>