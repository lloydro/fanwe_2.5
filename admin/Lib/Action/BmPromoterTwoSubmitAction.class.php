<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterTwoSubmitAction extends CommonAction
{
    public function index()
    {
        $map['pid']=array('gt',0);
        $map['status']=array('in','0,2');
        if (trim($_REQUEST['name']) != '') {
            $map['name'] = array('like', '%' . trim($_REQUEST['name']) . '%');
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

        $status = intval($_REQUEST['status']);
        if(isset($_REQUEST['status']) && $status >=0){
            $map['status'] = array('eq', $status);
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
        $vo['parent_name'] = M("BmPromoter")->where("user_id=".intval($vo['pid'])."")->getField("name");
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
            $this->error("请选择审核的推广商");
        }
        if($data['status'] ==1){
            if (!check_empty($data_info['mobile'])) {
                $this->error("登录手机号不能为空");
            }

            if (!check_mobile($data_info['mobile'])) {
                $this->error("登录手机号不正确");
            }

            if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where mobile='".$data_info['mobile']."' and status =1 and id <> ".intval($data_info['id'])."") )>0){
                $this->error("登录手机号已存在");
            }

            if (!check_empty($data_info['name'])) {
                $this->error("推广商名称不能为空");
            }

            if(intval($GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where name='".$data_info['name']."' and status =1 and id <> ".intval($data_info['id'])."") )>0){
                $this->error("推广商名称已存在");
            }

            $check_user_info=$this->check_user($data_info['user_id']);

            if($check_user_info['status'] ==0){
                $this->error($check_user_info['info']);
            }

            $data['is_effect']=1;
        }else{
            $data['is_effect']=0;
        }

        if($data['status'] ==2 && $data['memo']==''){
            $this->error("请输入备注");
        }

        // 更新数据
        $list = M("BmPromoter")->save($data);
        if (false != $list) {
            if($data['status'] ==1){
                $GLOBALS['db']->query("update ".DB_PREFIX."bm_promoter set child_count=(SELECT temp.id from (SELECT COUNT(id) as id from ".DB_PREFIX."bm_promoter where pid=".intval($data_info['pid'])." and is_effect=1 and status=1)temp ) where user_id= ".intval($data_info['pid'])."");
            }
            $this->assign("jumpUrl", u(MODULE_NAME . "/index", array("id" => $data['id'])));
            //成功提示
            save_log($log_info . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($log_info . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_FAILED"), 0, $log_info . L("UPDATE_FAILED"));
        }
    }

    public function foreverdelete() {
        //彻底删除指定记录
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST ['id'];
        if (isset ( $id )) {
            $condition = array ('id' => array ('in', explode ( ',', $id ) ) );
            $rel_data = M("BmPromoter")->where($condition)->findAll();
            foreach($rel_data as $data)
            {
                $info[] = $data['adm_name'];
                if($data["status"]==1 || $data["pid"]==0)
                {
                    $this->error($data['name']."(ID:".$data["id"].")不能删除",$ajax);
                }
            }
            if($info) $info = implode(",",$info);
            $list = M("BmPromoter")->where ( $condition )->delete();
            if ($list!==false) {
                save_log($info.l("FOREVER_DELETE_SUCCESS"),1);
                $this->success (l("FOREVER_DELETE_SUCCESS"),$ajax);
            } else {
                save_log($info.l("FOREVER_DELETE_FAILED"),0);
                $this->error (l("FOREVER_DELETE_FAILED"),$ajax);
            }
        } else {
            $this->error (l("INVALID_OPERATION"),$ajax);
        }
    }

    //检查绑定会员
    public function check_user($user_id)
    {
        $return=array("status"=>0,"info"=>"");
        $user_id=intval($user_id);

        //是否有会员
        $user_info=$GLOBALS['db']->getRow("select id,bm_pid,is_effect,nick_name from ".DB_PREFIX."user where id= ".$user_id." ");
        if(!$user_info){
            $return["status"]=0;
            $return["info"]="绑定会员未注册，请注册后再绑定";
            return $return;
        }

        //会员是否有效
        if($user_info['is_effect'] ==0){
            $return["status"]=0;
            $return["info"]="无效的会员";
            return $return;
        }

        //是否是三级推广会员
        /*if($user_info['bm_pid'] >0){
            $return["status"]=0;
            $return["info"]="该会员已是推广会员，不能绑定";
            return $return;
        }*/

        //是否已是绑定推广商
        $count_promoter=$GLOBALS['db']->getOne("select count(*) from ".DB_PREFIX."bm_promoter where user_id=".intval($user_info['id'])." and status=1");
        if($count_promoter >0){
            $return["status"]=0;
            $return["info"]="该会员已绑定推广商";
            return $return;
        }

        $return["status"]=1;

        return $return;
    }

}

?>