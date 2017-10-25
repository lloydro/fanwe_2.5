<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class missionModule extends baseModule
{
    /**
     * 构造函数，导入模型库
     */
    public function __construct()
    {
        parent::__construct();
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        Model::$lib = dirname(__FILE__);
    }

    public function __destruct()
    {
        parent::__destruct();
    }
    /**
     * api返回信息
     * @param  string  $error  [description]
     * @param  integer $status [description]
     * @param  array   $data   [description]
     * @return [type]          [description]
     */
    protected static function returnError($error = '出错了！', $status = 0, $data = [])
    {
        $data['status'] = $status;
        $data['error']  = $error;
        if ($error == '参数错误') {
            $data['data'] = $_REQUEST;
        }
        api_ajax_return($data);
    }
    /**
     * 日志写入
     * @param  [type] $data [description]
     * @return [type]       [description]
     */
    protected static function pushLog($data)
    {
        if (IS_DEBUG) {
            fanwe_require(APP_ROOT_PATH . 'mapi/lib/tools/PushLog.class.php');
            PushLog::log($data);
        }
    }
    /**
     * 获取用户id
     * @return [type] [description]
     */
    protected static function getUserId()
    {
        $user_id = intval($GLOBALS['user_info']['id']);
        if ($_REQUEST['test'] == 'test' && IS_DEBUG) {
            return 1;
        }
        if (!$user_id) {
            self::returnError('未登录');
        }
        return $user_id;
    }
    public function commitMission()
    {
        $user_id  = self::getUserId();
        $type     = intval($_REQUEST['type']);
        $m_config = load_auto_cache("m_config");
        if (!$m_config['mission_switch'] && OPEN_MISSION == 1) {
            self::returnError('未开启每日在线任务');
        }
        $res = Model::build('mission')->commitMission($user_id, $type);
        if (is_array($res)) {
            self::returnError('领取成功', 1, $res);
        } else {
            self::returnError($res);
        }
    }
    public function getMission()
    {
        $user_id = self::getUserId();
        $m_config = load_auto_cache("m_config");
        if (!$m_config['mission_switch'] && OPEN_MISSION == 1) {
            self::returnError('未开启每日在线任务');
        }
        $mission = Model::build('mission')->getMissionInfo($user_id);
        self::returnError('领取成功', 1, $mission);
    }
    public function getMissionList()
    {
        $user_id = self::getUserId();
        $m_config = load_auto_cache("m_config");
        if (!$m_config['mission_switch'] && OPEN_MISSION == 1) {
            self::returnError('未开启每日在线任务');
        }
        $list    = Model::build('mission')->getMissionList($user_id);
        self::returnError('', 1, compact('list'));
    }
}
