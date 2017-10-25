<?php

/**
 * Created by PhpStorm.
 * User: Administrator
 * Date: 2017/7/5
 * Time: 11:28
 */
class ChildRoomAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . "/admin/Lib/Action/VideoCommonAction.class.php";
    }

    //子房间列表
    public function index()
    {
        require_once APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require_once(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $now = get_gmtime();
        $parameter = '';
        $sql_w = '';
        if (intval($_REQUEST['cate_id']) > 0) {
            $parameter .= "cate_id=" . intval($_REQUEST['cate_id']) . "&";
            $sql_w .= "v.cate_id=" . intval($_REQUEST['cate_id']) . " and ";

        }
        if (intval($_REQUEST['classified_id']) > 0) {

            $parameter .= "classified_id=" . intval($_REQUEST['classified_id']) . "&";
            $sql_w .= "v.classified_id=" . intval($_REQUEST['classified_id']) . " and ";
        }

        if (strim($_REQUEST['nick_name']) != '') {
            //name
            $user = M("User")->where("nick_name like '%" . trim($_REQUEST['nick_name']) . "%' ")->findAll();
            foreach ($user as $k => $v) {
                $user_arr_id[$k] = intval($v['id']);
            }
            $parameter .= "user_id in (" . implode(",", $user_arr_id) . ")&";
            $sql_w .= "v.user_id in (" . implode(",", $user_arr_id) . ") and ";

        } else {
            if (intval($_REQUEST['user_id']) > 0) {
                $parameter .= "user_id=" . intval($_REQUEST['user_id']) . "&";
                $sql_w .= "v.user_id=" . intval($_REQUEST['user_id']) . " and ";
            }
        }

        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date($now, 'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;
        if (trim($_REQUEST['create_time_1']) != '') {
            $parameter .= "create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "'&";
            $sql_w .= "v.create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "' and ";
        }

        $parameter .= "live_in in (1,2,3)&";
        $sql_w .= "v.live_in in (1,2,3) and ";

        $model = D();

        $sql_str = "SELECT v.*," .
            "(SELECT count(1) FROM " . DB_PREFIX . "child_room_viewer crv WHERE crv.child_room = v.id) as watch_number,cr.parent_id,watch_number + v.virtual_watch_number + v.robot_num as all_watch_number " .
            " FROM " . DB_PREFIX . "video v," . DB_PREFIX . "child_room cr WHERE v.id = cr.child_id ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "video v , " . DB_PREFIX . "child_room cr  WHERE cr.child_id = v.id and 1=1 ";

        $sql_str .= " and " . $sql_w . " 1=1";

        $count_sql .= " and " . $sql_w . " 1=1";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'sort_num', 0, $count_sql);

        //取洪峰观看人数
        foreach ($voList as &$value) {
            if (intval($value['live_in']) == 3) {
                $value['max_watch'] = "回播视频不显示";
            } else {
                $value['max_watch'] = "子房间不显示";
            }
            $value['pay_editable'] = 0;
            if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1 && defined('OPEN_EDIT_VIDEO_PAY') && OPEN_EDIT_VIDEO_PAY == 1) {
                $value['pay_editable'] = 1;
                if ($value['is_live_pay'] == 1 && $value['live_pay_type'] == 0) {
                    $value['pay_editable'] = 0;
                }
            }
        }

        if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1) {
            $this->assign('is_pay_live', 1);
        } else {
            $this->assign('is_pay_live', 0);
        }

        $this->assign('url_name', get_manage_url_name());

        $this->assign('list', $voList);
        $cate_list = M("VideoCate")->findAll();
        $classified_list = M("VideoClassified")->findAll();
        $this->assign("classified_list", $classified_list);
        $this->assign("cate_list", $cate_list);
        $this->display();
    }

    //付费设置
    public function set_live_pay()
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_id = intval($_REQUEST['id']);
        $video_redis = new VideoRedisService($video_id);
        $video = $video_redis->getRow_db($video_id, array('id', 'is_live_pay', 'live_fee'));
        $this->assign("video", $video);
        $this->display();
    }

    //修改付费设置
    public function modify_live_pay()
    {
        $video_id = intval($_REQUEST['id']);
        $video['id'] = $video_id;
        $video['live_pay_type'] = 0;
        $video['live_fee'] = 0;
        $video['is_live_pay'] = intval($_REQUEST['is_live_pay']);//是否付费
        $live_fee = intval($_REQUEST['live_fee']);//观看费用
        if ($video['is_live_pay']) {
            $video['live_pay_type'] = 1;
            $video['live_fee'] = $live_fee;
        }

        if ($video['live_fee'] && !preg_match('/^[0-9]*[1-9][0-9]*$/', $video['live_fee'])) {
            $this->error("观看费用必须为大于0的整数");
        }

        $list = M("Video")->save($video);
        if (false !== $list) {
            //redis同步
            require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            sync_video_to_redis($video_id, '*', false);
            save_log($video_id . L("UPDATE_SUCCESS"), 1);
            $this->success(L("UPDATE_SUCCESS"));
        } else {
            //错误提示
            save_log($video_id . L("UPDATE_FAILED"), 0);
            $this->error(L("UPDATE_SUCCESS"));
        }
    }

    //修改上线状态
    public function set_demand_video_status()
    {
        require_once APP_ROOT_PATH . "/mapi/lib/core/common.php";
        $ajax = intval($_REQUEST['ajax']);
        $id = $_REQUEST['id'];
        $result['status'] = 0;
        if (isset ($id)) {
            $condition = array('id' => array('in', explode(',', $id)));
            $rel_data = M('Video')->where($condition)->findAll();
            $success_info = array();
            $fail_info = array();
            foreach ($rel_data as $data) {
                if ($data['live_in'] == 3) {
                    //下架
                    $m_config = load_auto_cache("m_config");
                    if ($m_config['ios_check_version'] != '') {
                        $sql = "select u.mobile from " . DB_PREFIX . "video v left join " . DB_PREFIX . "user u on u.id=v.user_id where v.id = " . $data['id'];
                        $mobile = $GLOBALS['db']->getOne($sql, true, true);
                        if ($mobile == '13888888888' || $mobile == '13999999999') {
                            $sql = "select count(*) from " . DB_PREFIX . "video v left join " . DB_PREFIX . "user u on u.id=v.user_id where v.live_in=3 and (u.mobile = '13888888888' or u.mobile = '13999999999')";
                            $video_count = $GLOBALS['db']->getOne($sql, true, true);
                            if (intval($video_count) <= 1) {
                                $result['status'] = 0;
                                $result['info'] = '下线失败，审核期间必须有一个审核账号的历史直播！';
                                admin_ajax_return($result);
                            }
                        }
                    }
                    $re = video_status($data['id'], 1);
                }
                //redis 同步结束
                $success_info[] = $data['id'];
                if ($re) {
                    $result['status'] = 1;
                } else {
                    $fail_info[] = $data['id'];
                }

                /*}else{
                    $fail_info[] = $data['id'];
                }*/
            }
            if ($success_info) {
                $success_info = implode(",", $success_info);
            }
            if ($fail_info) {
                $fail_info = implode(",", $fail_info);
            }
            if ($re) {
                save_log($success_info . l("DEMAND_VIDEO_STATUS_SUCCESS"), 1);
                $result['info'] = '修改成功！';
            } else {
                if ($success_info) {
                    save_log($success_info . l("DEMAND_VIDEO_STATUS_SUCCESS"), 1);
                }
                save_log($fail_info . l("DEMAND_VIDEO_STATUS_FAILED"), 0);
                $result['info'] = $fail_info . '修改失败！';
            }
        } else {
            $result['status'] = 0;
            $result['info'] = '编号错误';
        }
        admin_ajax_return($result);
    }

    public function video_set()
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_id = intval($_REQUEST['id']);
        $video_redis = new VideoRedisService($video_id);
        $video = $video_redis->getRow_db($video_id,
            array('id', 'virtual_number', 'max_robot_num', 'virtual_watch_number'));
        $this->assign("video", $video);
        $this->display();
    }

    public function modify_video_set()
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $video_id = intval($_REQUEST['id']);

        $video['virtual_number'] = intval($_REQUEST['virtual_number']);//1用户带机器人最大比例
        $video['virtual_watch_number'] = intval($_REQUEST['virtual_watch_number']);//直接设置机器人数
        $video['max_robot_num'] = intval($_REQUEST['robot_num']);//最大机器人头像数

        $robot_num = M("User")->where("is_effect=1 and is_robot = 1")->count();
        if ($video['robot_num'] > $robot_num) {
            $this->error("最大机器人头像数不能大于系统机器人头像总数" . $robot_num);
        }

        $video_redis = new VideoRedisService($video_id);
        $video_redis->update_db($video_id, $video);
        M(MODULE_NAME)->where("id=" . $video_id)->setField("max_robot_num", $video['max_robot_num']);
        $video_redis->update('video_virtual_watch_number', array($video_id => $video['virtual_watch_number']));

        save_log(l("ADMIN_MODIFY_ACCOUNT"), 1);
        $this->success(L("UPDATE_SUCCESS"));
    }

    //关闭房间
    function close_live()
    {
        $common = new VideoCommon();
        $data = $_REQUEST;
        $common->close_live($data);
    }

    public function set_sort()
    {
        $id = intval($_REQUEST['id']);
        $sort = intval($_REQUEST['sort']);
        $name = $this->getActionName();
        $log_info = M($name)->where("id=" . $id)->find();
        //print_r($log_info['user_id']);
        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }
        M("Video")->where("id=" . $id)->setField("sort", $sort);
        //print_r(M($name)->GetLastSql());

        require_once APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require_once APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php';

        $video_redis = new VideoRedisService($log_info['user_id']);
        //print_r($video_redis);
        //更新视频排序信息
        $return = $video_redis->update_video_sort($log_info['id'], $sort);
        save_log($log_info['title'] . l("SORT_SUCCESS"), 1);
        //clear_auto_cache("get_help_cache");
        $this->success(l("SORT_SUCCESS"), 1);
    }

    //设置推荐
    public function set_recommend()
    {
        $id = intval($_REQUEST['id']);
        $recommend = intval($_REQUEST['recommend']);
        $c_is_effect = M('Video')->where("id=" . $id)->getField("is_recommend");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        $result = M('Video')->where("id=" . $id)->setField("is_recommend", $n_is_effect);
        save_log("房间号" . $id . l("SET_RECOMMEND_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_RECOMMEND_" . $n_is_effect), 1);

    }

    public function set_hot_on()
    {
        $id = intval($_REQUEST['id']);
        log_result($id);
        $ajax = intval($_REQUEST['ajax']);
        $user_info = M("User")->getById($id);
        $c_is_effect = M("User")->where("id=" . $id)->getField("is_hot_on");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        $result = M("User")->where("id=" . $id)->setField("is_hot_on", $n_is_effect);
        $user_data = array();
        if ($result) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/UserRedisService.php');
            $user_redis = new UserRedisService();
            $user_data['is_hot_on'] = $n_is_effect;
            $user_redis->update_db($id, $user_data);
        }
        save_log($user_info['nick_name'] . l("SET_HOT_ON_" . $n_is_effect), 1);
        $this->ajaxReturn($n_is_effect, l("SET_HOT_ON_" . $n_is_effect), 1);
    }

    //添加子房间
    public function add_child_room()
    {
        $video_list = $GLOBALS['db']->getAll("SELECT v.id , v.user_id , u.nick_name FROM " . DB_PREFIX . "video AS v , " . DB_PREFIX . "user AS u WHERE v.user_id = u.id AND v.live_in = 1 AND NOT EXISTS( SELECT * FROM " . DB_PREFIX . "child_room AS r WHERE r.child_id = v.id) order by v.sort");

        $this->assign('parent_room_list', $video_list);
        $this->display();
    }

    //编辑子房间
    public function edit_child_room()
    {
        $child_room_id = intval($_REQUEST['child_room_id']);

        $video_info = $GLOBALS['db']->getRow("SELECT v.id,v.user_id,v.cate_id,v.live_image,v.room_title FROM " . DB_PREFIX . "video v ," . DB_PREFIX . "child_room cr where v.id ={$child_room_id}");
        $video_info['room_id'] = $child_room_id;
        $video_info['nick_name'] = $GLOBALS['db']->getOne("SELECT nick_name FROM " . DB_PREFIX . "user WHERE id =" . intval($video_info['user_id']));
        $video_info['title'] = $GLOBALS['db']->getOne("SELECT title FROM " . DB_PREFIX . "video_cate WHERE id =" . intval($video_info['cate_id']));
        $video_info['parent_id'] = $GLOBALS['db']->getOne("SELECT parent_id FROM " . DB_PREFIX . "child_room WHERE child_id =" . $child_room_id);


        $video_list = $GLOBALS['db']->getAll("SELECT v.id , v.user_id , u.nick_name FROM " . DB_PREFIX . "video AS v , " . DB_PREFIX . "user AS u WHERE v.user_id = u.id AND v.live_in = 1 AND NOT EXISTS( SELECT * FROM " . DB_PREFIX . "child_room AS r WHERE r.child_id = v.id) order by v.sort");
        $this->assign('parent_room_list', $video_list);
        $this->assign('vo', $video_info);
        $this->display();
    }

    //插入数据
    public function insert_child_room()
    {
        $user_id = intval($_REQUEST['user_id']);
        $title = strim($_REQUEST['title']);
        $parent_id = intval($_REQUEST['parent_id']);
        $live_image = strim($_REQUEST['live_image']);
        $room_title = strim($_REQUEST['room_title']);

        if ($user_id == 0) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入用户id'));
        }
        if (empty($room_title)) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入直播间标题'));
        }
        $has_room = M('Video')->where('user_id =' . $user_id)->count();
        if ($has_room) {
            admin_ajax_return(array('status' => '0', 'error' => '该用户的房间未结束'));
        }
        $is_private = false;
        $monitor_time = to_date(NOW_TIME + 3600, 'Y-m-d H:i:s');
        $data = $this->create_video($user_id, $title, $is_private, $monitor_time, $room_title);//视频信息写入video表
        $data['live_image'] = $live_image;
        if ($parent_id) {
            $parent_info = $GLOBALS['db']->getRow("SELECT v.live_in,v.video_vid,v.play_url,v.push_rtmp,v.play_flv,v.play_mp4,v.play_hls,v.group_id,v.live_pay_type,v.live_fee,v.is_live_pay,v.create_type FROM " . DB_PREFIX . "video v  where v.live_in in (1,3) and v.id =" . $parent_id);
            if (empty($parent_info)) {
                admin_ajax_return(array('status' => '0', 'error' => '主房间不存在'));
            }
            $data['video_vid'] = $parent_info['video_vid'];
            $data['play_flv'] = $parent_info['play_flv'];
            $data['play_url'] = $parent_info['play_url'];
            $data['play_mp4'] = $parent_info['play_mp4'];
            $data['play_hls'] = $parent_info['play_hls'];
            $data['live_in'] = $parent_info['live_in'];
            $data['prop_table'] = DB_PREFIX . "child_video_prop";
            $data['group_id'] = $parent_info['group_id'];

            $data['live_pay_type'] = $parent_info['live_pay_type'];
            $data['live_fee'] = $parent_info['live_fee'];
            $data['is_live_pay'] = $parent_info['is_live_pay'];
            $data['create_type'] = $parent_info['create_type'];
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'INSERT');
        if ($GLOBALS['db']->affected_rows()) {
            $child_data['child_id'] = $data['id'];
            $child_data['parent_id'] = $parent_id;
            if ($GLOBALS['db']->autoExecute(DB_PREFIX . "child_room", $child_data, 'INSERT')) {
                $res['status'] = 1;
                $res['error'] = '添加子房间成功';
                //同步到redis
                fanwe_require(APP_ROOT_PATH . "/mapi/lib/core/common.php");
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                sync_video_to_redis($data['id'], '*', false);
                $video_redis = new VideoRedisService();
                $status['online_status'] = 1;
                $video_redis->update_db($data['id'], $status);
                $video_redis->redis->sAdd('video_child_room_' . $parent_id, $data['id']);
            } else {
                $res['status'] = 0;
                $res['error'] = '关联主房间失败！';
            }
        } else {
            $res['status'] = 0;
            $res['error'] = '添加子房间失败！';
        }
        admin_ajax_return($res);
    }

    //更新数据
    public function update_child_room()
    {
        $data['user_id'] = intval($_REQUEST['user_id']);
        $data['title'] = strim($_REQUEST['title']);
        $parent_id = intval($_REQUEST['parent_id']);
        $data['live_image'] = strim($_REQUEST['live_image']);
        $data['room_title'] = strim($_REQUEST['room_title']);
        $room_id = intval($_REQUEST['room_id']);
        if ($data['user_id'] == 0) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入用户id'));
        }
        if (empty($data['room_title'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入直播间标题'));
        }
        $has_room = M('Video')->where('user_id =' . $data['user_id'])->count();
        if (!$has_room) {
            admin_ajax_return(array('status' => '0', 'error' => '子房间不存在'));
        }
        //同步到redis
        fanwe_require(APP_ROOT_PATH . "/mapi/lib/core/common.php");
        if ($parent_id) {
            $parent_info = $GLOBALS['db']->getRow("SELECT v.live_in,v.video_vid,v.play_url,v.push_rtmp,v.play_flv,v.play_mp4,v.play_hls,v.group_id,v.live_pay_type,v.live_fee,v.is_live_pay,v.create_type FROM " . DB_PREFIX . "video v  where v.live_in in (1,3) and v.id =" . $parent_id);
            if (empty($parent_info)) {
                admin_ajax_return(array('status' => '0', 'error' => '主房间不存在'));
            }
            $data['video_vid'] = $parent_info['video_vid'];
            $data['play_flv'] = $parent_info['play_flv'];
            $data['play_url'] = $parent_info['play_url'];
            $data['play_mp4'] = $parent_info['play_mp4'];
            $data['play_hls'] = $parent_info['play_hls'];
            $data['live_in'] = $parent_info['live_in'];
            $data['prop_table'] = DB_PREFIX . "child_video_prop";
            $data['group_id'] = $parent_info['group_id'];

            $data['live_pay_type'] = $parent_info['live_pay_type'];
            $data['live_fee'] = $parent_info['live_fee'];
            $data['is_live_pay'] = $parent_info['is_live_pay'];
            $data['create_type'] = $parent_info['create_type'];
        }

        if ($GLOBALS['db']->autoExecute(DB_PREFIX . "video", $data, 'UPDATE', 'id =' . $room_id)) {
            $child_data['parent_id'] = $parent_id;
            if ($GLOBALS['db']->autoExecute(DB_PREFIX . "child_room", $child_data, 'UPDATE', 'child_id =' . $room_id)) {
                $res['status'] = 1;
                $res['error'] = '编辑成功';
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php');
                fanwe_require(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
                sync_video_to_redis($room_id, '*', false);
                $video_redis = new VideoRedisService();
                $status['online_status'] = 1;
                $video_redis->update_db($room_id, $status);
                $video_redis->redis->sAdd('video_child_room_' . $parent_id, $room_id);
            } else {
                $res['status'] = 0;
                $res['error'] = '关联主房间失败！';
            }
        } else {
            $res['status'] = 0;
            $res['error'] = '编辑失败！';
        }
        admin_ajax_return($res);
    }

    public function create_video(
        $user_id,
        $title,
        $is_private,
        $monitor_time,
        $room_title,
        $cate_id = '',
        $province = '',
        $city = '',
        $share_type = ''
    ) {
        $condition['title'] = $title;
        if ($cate_id == 0 && $title != '') {
            $cate_id = M('video_cate')->where($condition)->getfield('id');
            if ($cate_id) {
                $is_newtitle = 0;
            } else {
                $is_newtitle = 1;
            }
        }

        if ($is_newtitle) {
            $data_cate = array();
            $data_cate['title'] = $title;
            $data_cate['is_effect'] = 1;
            $data_cate['is_delete'] = 0;
            $data_cate['create_time'] = NOW_TIME;
            M('video_cate')->add($data_cate);
            $cate_id = M('video_cate')->where($condition)->getfield('id');
        }

        if ($province == '') {
            $province = '火星';
        }

        if ($city == '') {
            $city = '火星';
        }

        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/common.php');
        $v_id = get_max_room_id(0);//视频ID
        $data = array();
        $data['id'] = $v_id;

        //room_type 房间类型 : 1私有群（Private）,0公开群（Public）,2聊天室（ChatRoom）,3互动直播聊天室（AVChatRoom）
        $data['room_type'] = 3;

        $m_config = load_auto_cache("m_config");
        $data['virtual_number'] = intval($m_config['virtual_number']);
        $data['max_robot_num'] = intval($m_config['robot_num']);//允许添加的最大机器人数;

        $sql = "select sex,ticket,refund_ticket,user_level,fans_count,head_image,thumb_head_image from " . DB_PREFIX . "user where id = " . $user_id;
        $user = $GLOBALS['db']->getRow($sql, true, true);
        if (!$user) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '用户ID不存在'
            ));
        }

        $info = origin_image_info($user['head_image']);
        $data['head_image'] = get_spec_image($info['file_name']);
        $data['thumb_head_image'] = $user['thumb_head_image'];

        $data['sex'] = intval($user['sex']);//性别 0:未知, 1-男，2-女
        $data['video_type'] = 1;//0:腾讯云互动直播;1:腾讯云直播

        $data['monitor_time'] = $monitor_time;

        $data['create_type'] = 0;// 0:APP端创建的直播;1:PC端创建的直播
        $data['push_url'] = '';//video_type=1;1:腾讯云直播推流地址
        $data['play_url'] = '';//video_type=1;1:腾讯云直播播放地址(rmtp,flv)

        $data['share_type'] = $share_type;
        $data['title'] = $title;
        $data['cate_id'] = $cate_id;
        $data['user_id'] = $user_id;
        $data['live_in'] = 2;//live_in:是否直播中 1-直播中 0-已停止;2:正在创建直播;
        $data['watch_number'] = '';//'当前观看人数';
        $data['vote_number'] = '';//'获得票数';
        $data['province'] = $province;//'省';
        $data['city'] = $city;//'城市';
        $data['room_title'] = $room_title;
        $data['create_time'] = NOW_TIME;//'创建时间';
        $data['begin_time'] = NOW_TIME;//'开始时间';
        $data['end_time'] = '';//'结束时间';
        $data['is_hot'] = 1;//'1热门; 0:非热门';
        $data['is_new'] = 1; //'1新的; 0:非新的,直播结束时把它标识为：0？'

        $data['online_status'] = 1;//主播在线状态;1:在线(默认); 0:离开

        //sort_init(初始排序权重) = (用户可提现印票：fanwe_user.ticket - fanwe_user.refund_ticket) * 保留印票权重+ 直播/回看[回看是：0; 直播：9000000000 直播,需要排在最上面 ]+ fanwe_user.user_level * 等级权重+ fanwe_user.fans_count * 当前有的关注数权重
        $sort_init = (intval($user['ticket']) - intval($user['refund_ticket'])) * floatval($m_config['ticke_weight']);

        $sort_init += intval($user['user_level']) * floatval($m_config['level_weight']);
        $sort_init += intval($user['fans_count']) * floatval($m_config['focus_weight']);

        $data['sort_init'] = 200000000 + $sort_init;
        $data['sort_num'] = $data['sort_init'];
        return $data;
    }

    //观众列表
    public function viewer_list()
    {
        $child_id = intval($_REQUEST['child_room_id']);
        $user_id = intval($_REQUEST['user_id']);
        $nick_name = strim($_REQUEST['nick_name']);

        $sql_w = '';
        $parameter = '';
        //查询ID
        if ($user_id != '') {
            $parameter .= "u.id =" . $user_id . "&";
            $sql_w .= "u.id =" . $user_id . " and ";
        }
        //查询昵称
        if ($nick_name != '') {
            $parameter .= "u.nick_name like " . urlencode('%' . $nick_name . '%') . "&";
            $sql_w .= "u.nick_name like '%" . $nick_name . "%' and ";
        }
        $model = D('child_room_viewer');
        //子房间主播
        $child_video = M('Video')->where('id =' . $child_id)->field('user_id')->find();
        if (empty($child_video)) {
            $child_video = M('VideoHistory')->where('id =' . $child_id)->field('user_id')->find();
        }
        $where = " cv.child_room =" . $child_id . " and u.id != " . $child_video['user_id'];
        $sql_str = "SELECT u.id,u.nick_name,u.head_image,u.user_level,u.login_ip FROM " . DB_PREFIX . "user as u LEFT JOIN " . DB_PREFIX . "child_room_viewer as cv
                 ON u.id = cv.user_id WHERE " . $where . " and " . $sql_w . " 1=1 ";
        $count_sql = "SELECT COUNT(u.id) as tpcount FROM " . DB_PREFIX . "user as u LEFT JOIN " . DB_PREFIX . "child_room_viewer as cv
                 ON u.id = cv.user_id WHERE " . $where . " and " . $sql_w . " 1=1 ";

        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 1, 0, $count_sql);

        $this->assign("child_room_id", $child_id);
        $this->assign("list", $volist);
        $this->display();
    }

    //送礼物日志
    public function user_prop()
    {
        $user_id = intval($_REQUEST['user_id']);
        $room_id = intval($_REQUEST['room_id']);

        $user_info = M("User")->getById($user_id);
        $prop_list = M("prop")->where("is_effect <>0")->findAll();
        $prop_table = M("Video")->where("id =" . $room_id)->find();
        if (empty($prop_table)) {
            $prop_table = M("VideoHistory")->where("id =" . $room_id)->find();
        }

        $model = D("{$prop_table['prop_table']}");

        if (!isset($_REQUEST['prop_id'])) {
            $_REQUEST['prop_id'] = -1;
        }
        //查询礼物
        $parameter = '';
        $sql_w = '';
        if ($_REQUEST['prop_id'] != -1) {
            if (isset($data['prop_id'])) {
                $parameter .= "l.prop_id=" . intval($_REQUEST['prop_id']) . "&";
                $sql_w .= "l.prop_id=" . intval($_REQUEST['prop_id']) . " and ";
            }
        }
        $where = "l.from_user_id=" . $user_id . " and  video_id =" . $room_id;
        $sql_str = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,l.from_ip
                         FROM {$prop_table['prop_table']} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1  ";

        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM   {$prop_table['prop_table']} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1  ";

        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            if ($volist[$k]['prop_id'] == 12) {
                $volist[$k]['total_ticket'] = '';
            }
            $volist[$k]['create_time'] = date('Y-m-d', $volist[$k]['create_time']);
        }
        $this->assign("user_info", $user_info);
        $this->assign("room_id", $room_id);
        $this->assign("prop", $prop_list);
        $this->assign("list", $volist);
        $this->display();
    }

    //送礼物日志
    public function prop_list()
    {
        $room_id = intval($_REQUEST['room_id']);

        $prop_list = M("prop")->where("is_effect <>0")->findAll();
        $prop_table = M("Video")->where("id =" . $room_id)->find();
        if (empty($prop_table)) {
            $prop_table = M("VideoHistory")->where("id =" . $room_id)->find();
        }

        $model = D("{$prop_table['prop_table']}");

        if (!isset($_REQUEST['prop_id'])) {
            $_REQUEST['prop_id'] = -1;
        }

        //查询礼物
        $parameter = '';
        $sql_w = '';
        if ($_REQUEST['prop_id'] != -1) {
            $parameter .= "l.prop_id=" . intval($_REQUEST['prop_id']) . "&";
            $sql_w .= "l.prop_id=" . intval($_REQUEST['prop_id']) . " and ";
        }
        $where = "video_id =" . $room_id;
        $sql_str = "SELECT l.id,l.create_ym,l.to_user_id, l.create_time,l.prop_id,l.prop_name,l.from_user_id,l.create_date,l.num,l.total_ticket,u.nick_name,l.from_ip
                         FROM {$prop_table['prop_table']} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1  ";

        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM   {$prop_table['prop_table']} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1  ";

        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            if ($volist[$k]['prop_id'] == 12) {
                $volist[$k]['total_ticket'] = '';
            }
            $volist[$k]['create_time'] = date('Y-m-d', $volist[$k]['create_time']);
        }
        $this->assign("room_id", $room_id);
        $this->assign("prop", $prop_list);
        $this->assign("list", $volist);
        $this->display();
    }

    public function pay_list()
    {
        $room_id = intval($_REQUEST['room_id']);


        $where = "video_id =" . $room_id;
        $sql_str = "SELECT l.id , l.video_id, l.create_ym , l.to_user_id , l.create_time , l.from_user_id , l.create_date , l.live_fee , u.nick_name
                         FROM " . DB_PREFIX . "child_live_pay_log as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " 
                         WHERE $where ";

        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM  " . DB_PREFIX . "child_live_pay_log as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l . to_user_id = u . id" . " 
                         WHERE $where ";

        $model = D("child_live_pay_log");
        $volist = $this->_Sql_list($model, $sql_str, '', 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            $volist[$k]['create_time'] = date('Y-m-d H:i:s', $volist[$k]['create_time']);
        }
        $this->assign("room_id", $room_id);
        $this->assign("list", $volist);
        $this->display();
    }

    //子房间列表
    public function history_list()
    {
        require_once APP_ROOT_PATH . 'mapi/lib/redis/BaseRedisService.php';
        require_once(APP_ROOT_PATH . 'mapi/lib/redis/VideoRedisService.php');
        $now = get_gmtime();
        $parameter = '';
        $sql_w = '';
        if (intval($_REQUEST['cate_id']) > 0) {
            $parameter .= "cate_id=" . intval($_REQUEST['cate_id']) . "&";
            $sql_w .= "v.cate_id=" . intval($_REQUEST['cate_id']) . " and ";

        }
        if (intval($_REQUEST['classified_id']) > 0) {

            $parameter .= "classified_id=" . intval($_REQUEST['classified_id']) . "&";
            $sql_w .= "v.classified_id=" . intval($_REQUEST['classified_id']) . " and ";
        }

        if (strim($_REQUEST['nick_name']) != '') {
            //name
            $user = M("User")->where("nick_name like '%" . trim($_REQUEST['nick_name']) . "%' ")->findAll();
            foreach ($user as $k => $v) {
                $user_arr_id[$k] = intval($v['id']);
            }
            $parameter .= "user_id in (" . implode(",", $user_arr_id) . ")&";
            $sql_w .= "v.user_id in (" . implode(",", $user_arr_id) . ") and ";

        } else {
            if (intval($_REQUEST['user_id']) > 0) {
                $parameter .= "user_id=" . intval($_REQUEST['user_id']) . "&";
                $sql_w .= "v.user_id=" . intval($_REQUEST['user_id']) . " and ";
            }
        }

        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date($now, 'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;
        if (trim($_REQUEST['create_time_1']) != '') {
            $parameter .= "create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "'&";
            $sql_w .= "v.create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "' and ";
        }

        $parameter .= "live_in=0&";
        $sql_w .= "v.live_in=0 and ";

        $model = D();

        $sql_str = "SELECT v.*," .
            "(SELECT count(1) FROM " . DB_PREFIX . "child_room_viewer crv WHERE crv.child_room = v.id) as watch_number,cr.parent_id,watch_number + v.virtual_watch_number + v.robot_num as all_watch_number " .
            " FROM " . DB_PREFIX . "video_history v," . DB_PREFIX . "child_room cr WHERE v.id = cr.child_id ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "video_history v , " . DB_PREFIX . "child_room cr  WHERE cr.child_id = v.id and 1=1 ";

        $sql_str .= " and " . $sql_w . " 1=1";

        $count_sql .= " and " . $sql_w . " 1=1";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'sort_num', 0, $count_sql);

        //取洪峰观看人数
        foreach ($voList as &$value) {
            if (intval($value['live_in']) == 3) {
                $value['max_watch'] = "回播视频不显示";
            } else {
                $value['max_watch'] = "子房间不显示";
            }
            $value['pay_editable'] = 0;
            if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1 && defined('OPEN_EDIT_VIDEO_PAY') && OPEN_EDIT_VIDEO_PAY == 1) {
                $value['pay_editable'] = 1;
                if ($value['is_live_pay'] == 1 && $value['live_pay_type'] == 0) {
                    $value['pay_editable'] = 0;
                }
            }
        }

        if (defined('OPEN_LIVE_PAY') && OPEN_LIVE_PAY == 1) {
            $this->assign('is_pay_live', 1);
        } else {
            $this->assign('is_pay_live', 0);
        }

        $this->assign('url_name', get_manage_url_name());

        $this->assign('list', $voList);
        $cate_list = M("VideoCate")->findAll();
        $classified_list = M("VideoClassified")->findAll();
        $this->assign("classified_list", $classified_list);
        $this->assign("cate_list", $cate_list);
        $this->display();
    }

    //子房间账户关联列表
    public function account()
    {
        $now = get_gmtime();
        $parameter = '';
        $sql_w = '';

        if (strim($_REQUEST['nick_name']) != '') {
            //name
            $user = M("User")->where("nick_name like '%" . trim($_REQUEST['nick_name']) . "%' ")->findAll();
            foreach ($user as $k => $v) {
                $user_arr_id[$k] = intval($v['id']);
            }
            $parameter .= "user_id in (" . implode(",", $user_arr_id) . ")&";
            $sql_w .= "a.p_user_id in (" . implode(",", $user_arr_id) . ") and ";

        } else {
            if (intval($_REQUEST['user_id']) > 0) {
                $parameter .= "user_id=" . intval($_REQUEST['user_id']) . "&";
                $sql_w .= "a.p_user_id=" . intval($_REQUEST['user_id']) . " and ";
            }
        }

        $parameter .= " ";
        $sql_w .= " ";

        $model = D();
        $sql_str = "SELECT a.*," .
            " u.nick_name,u.head_image,u.thumb_head_image " .
            " FROM " . DB_PREFIX . "child_room_account a," . DB_PREFIX . "user u WHERE a.p_user_id = u.id ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "child_room_account a," . DB_PREFIX . "user u WHERE a.p_user_id = u.id ";

        $sql_str .= " and " . $sql_w . " 1=1";

        $count_sql .= " and " . $sql_w . " 1=1";

        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'a.id', 0, $count_sql);


        foreach ($voList as &$value) {
            $value['head_image'] = get_spec_image($value['head_image']);
            if (empty($value['head_image'])) {
                $value['head_image'] = get_spec_image($value['thumb_head_image']);
            }
        }

        $this->assign('url_name', get_manage_url_name());
        $this->assign('list', $voList);
        $this->display();
    }

    //设置是否有效
    public function set_effect()
    {
        $id = intval($_REQUEST['id']);//树苗id
        $ajax = intval($_REQUEST['ajax']);

        $c_is_effect = M('ChildRoomAccount')->where("id=" . $id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        $result = M('ChildRoomAccount')->where("id=" . $id)->setField("is_effect", $n_is_effect);
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    //新增关联
    public function add_child_room_account()
    {
        $this->display();
    }

    //插入数据
    public function insert_child_room_account()
    {
        $data['room_title'] = strim($_REQUEST['room_title']);
        $data['price'] = strim($_REQUEST['price']);
        $data['p_user_id'] = intval($_REQUEST['p_user_id']);
        $data['c_user_id'] = strim($_REQUEST['c_user_id']);
        $data['video_code'] = strim($_REQUEST['video_code']);
        $data['is_effect'] = 1;

        if ($data['video_code'] != '') {
            if (!preg_match("/^[A-Za-z0-9]+$/", $data['video_code'])) {
                $return['error'] = "验证码只能是字母和数字";
                $return['status'] = 0;
                admin_ajax_return($return);
            }
        }
        if (empty($data['p_user_id'])) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '请输入主账号ID'
            ));
        }
        if (empty($data['c_user_id'])) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '请输子主账号ID'
            ));
        }
        if (M('ChildRoomAccount')->where('p_user_id =' . $data['p_user_id'])->count()) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '主账号用户子房间已存在'
            ));
        }
        $p_user = M('User')->where('id =' . $data['p_user_id'])->count();
        if (!$p_user) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '主账号用户不存在'
            ));
        }
        $user_id_arr = explode(',', $data['c_user_id']);
        $error_user = '';
        foreach ($user_id_arr as $value) {
            $c_user = M('User')->where('id =' . $value)->count();
            if (!$c_user) {
                $error_user .= $value . ',';
            }
        }

        if (!empty(trim($error_user))) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '子账号用户' . $error_user . '不存在'
            ));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "child_room_account", $data, 'INSERT');
        if ($GLOBALS['db']->affected_rows()) {
            admin_ajax_return(array(
                'status' => 1,
                'error' => '添加成功'
            ));
        }
    }

    //编辑关联
    public function edit_child_room_account()
    {
        $id = intval($_REQUEST['id']);
        $account_info = M('ChildRoomAccount')->where('id =' . $id)->find();
        $this->assign('vo', $account_info);
        $this->display();
    }

    //更新数据
    public function update_child_room_account()
    {
        $id = intval($_REQUEST['id']);
        $data['room_title'] = strim($_REQUEST['room_title']);
        $data['price'] = strim($_REQUEST['price']);
        $data['p_user_id'] = intval($_REQUEST['p_user_id']);
        $data['c_user_id'] = strim($_REQUEST['c_user_id']);
        $data['video_code'] = strim($_REQUEST['video_code']);

        if ($data['video_code'] != '') {
            if (!preg_match("/^[A-Za-z0-9]+$/", $data['video_code'])) {
                $return['error'] = "验证码只能是字母和数字";
                $return['status'] = 0;
                admin_ajax_return($return);
            }
        }

        $account_info = M('ChildRoomAccount')->where('id =' . $id)->find();
        if (empty($data['p_user_id'])) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '请输入主账号ID'
            ));
        }
        if (empty($data['c_user_id'])) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '请输子主账号ID'
            ));
        }
        if ($data['p_user_id'] != $account_info['p_user_id']) {
            if (M('ChildRoomAccount')->where('p_user_id =' . $data['p_user_id'])->count()) {
                admin_ajax_return(array(
                    'status' => 0,
                    'error' => '主账号用户子房间已存在'
                ));
            }
        }

        $p_user = M('User')->where('id =' . $data['p_user_id'])->count();
        if (!$p_user) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '主账号用户不存在'
            ));
        }
        $user_id_arr = explode(',', $data['c_user_id']);
        $error_user = '';
        foreach ($user_id_arr as $value) {
            $c_user = M('User')->where('id =' . $value)->count();
            if (!$c_user) {
                $error_user .= $value . ',';
            }
        }

        if (!empty(trim($error_user))) {
            admin_ajax_return(array(
                'status' => 0,
                'error' => '子账号用户' . $error_user . '不存在'
            ));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "child_room_account", $data, 'UPDATE', 'id =' . $id);
        if ($GLOBALS['db']->affected_rows()) {
            admin_ajax_return(array(
                'status' => 1,
                'error' => '编辑成功'
            ));
        }
    }
}