<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterOneAction extends CommonAction
{
    public function index()
    {
        $map['pid']=0;
        $map['status']=1;
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . trim($_REQUEST['name']) . '%');
        }

        $mobile = trim($_REQUEST['mobile']);
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

    public function add()
    {
        $this->display();
    }

    public function edit()
    {
        $id = intval($_REQUEST ['id']);
        $condition['id'] = $id;
        $vo = M("BmPromoter")->where($condition)->find();

        $vo['user'] = M("User")->where("id=".intval($vo['user_id'])."")->find();

        $this->assign('vo', $vo);

        $this->display();
    }

    public function insert()
    {
        B('FilterString');
        $ajax = intval($_REQUEST['ajax']);
        $data = M("BmPromoter")->create();
        //开始验证有效性
        $this->assign("jumpUrl", u(MODULE_NAME . "/add"));

        if (!check_empty($data['mobile'])) {
            $this->error("登录手机号不能为空");
        }

        if (!check_mobile($data['mobile'])) {
            $this->error("请输入正确的手机号");
        }

        if (!check_empty($data['name'])) {
            $this->error("推广中心名称不能为空");
        }

        if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where mobile='".$data['mobile']."' and status =1 ") )>0){
            $this->error("登录手机号已存在");
        }

        if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where name='".$data['name']."' and status =1 ") )>0){
            $this->error("推广中心名称已存在");
        }

        $binding_mobile=strim($_REQUEST['binding_mobile']);
        if (!check_empty($binding_mobile)) {
            $this->error("绑定会员不能为空");
        }
        $check_user_info=$this->check_user($binding_mobile,0);

        if($check_user_info['status'] ==0){
            $this->error($check_user_info['info']);
        }

        // 更新数据
        $log_info = $data['name'];
        $data['pwd']=$data['pwd']==''?md5('123456'):md5($data['pwd']);
        $data['user_id'] = $check_user_info['user']['id'];
        $data['status'] = 1;
        $data['create_time'] = get_gmtime();

        $list = M("BmPromoter")->add($data);
        if ($list) {
            //成功提示
            save_log($log_info . L("INSERT_SUCCESS"), 1);
            $this->success(L("INSERT_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("INSERT_FAILED"), 0);
            $this->error(L("INSERT_FAILED"));
        }
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
            $this->error("推广中心名称不能为空");
        }

        if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where name='".$data['name']."' and status =1 and id <> ".intval($data_info['id'])."") )>0){
            $this->error("推广中心名称已存在");
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

        if($check_user_id>0 && $data_info['user_id'] !=$check_user_id){
            $data['user_id']=$check_user_id;
        }

        $list = M("BmPromoter")->save($data);
        if (false !== $list) {
            if($check_user_id>0 && $data_info['user_id'] >0 && $data_info['user_id'] != $check_user_id){
                $GLOBALS['db']->query("update ".DB_PREFIX."bm_promoter set pid= ".intval($check_user_id)." where pid=".intval($data_info['user_id'])."");
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
        if($binding_user_id_old>0 && $binding_user_id_old == $user_info['id']){
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
        /*if($user_info['bm_pid'] >0){
            $return["status"]=0;
            $return["info"]="该会员已是推广会员，不能绑定";
            return $this->return_info($return,$ajax);
        }*/

        //是否已是绑定推广商
        $count_promoter=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where user_id=".intval($user_info['id'])." and status=1");
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