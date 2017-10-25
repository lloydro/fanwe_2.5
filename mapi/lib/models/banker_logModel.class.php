<?php
/**
 *
 */
class banker_logModel extends NewModel
{
    /**
     * 新增上庄日志（申请上庄）
     * @param integer $video_id 直播间id
     * @param integer $user_id  用户id
     * @param integer $coin     上庄金额
     */
    public function addLog($video_id, $user_id, $coin)
    {
        $log = $this->field('id')->selectOne(['user_id' => $user_id, 'video_id' => $video_id, 'status' => 1]);
        if ($log) {
            return false;
        }
        $data = array(
            'video_id'    => $video_id,
            'user_id'     => $user_id,
            'coin'        => $coin,
            'apply_coin'  => $coin,
            'status'      => 1,
            'create_time' => NOW_TIME,
        );
        return $this->insert($data);
    }
    /**
     * 选庄
     * @param  integer $banker_log_id 上庄日志id
     * @param  integer $video_id      直播间id
     * @return integer                受影响行数
     */
    public function chooseBanker($banker_log_id, $video_id)
    {
        $res = $this->update(['status' => 3], ['id' => $banker_log_id, 'video_id' => $video_id]);
        if ($res) {
            $this->returnCoin(['status' => 1, 'video_id' => $video_id], '落选上庄底金返还');
        }
        return $res;
    }
    /**
     * 退还上庄金额
     * @param  array  $where 退还条件
     * @param  string $msg   退还消息
     * @return bool          是否退还成功
     */
    public function returnCoin($where, $msg)
    {
        $model          = self::build('user');
        $coin_log_model = self::build('coin_log');
        $lose_banker    = $this->field('user_id,coin,status')->select($where);
        Connect::beginTransaction();
        foreach ($lose_banker as $value) {
            if ($value['status'] == 3) {
                $user_id = $value['user_id'];
                $coin    = $value['coin'];

                $res = $model->coin($value['user_id'], $value['coin']);
                if (!$res) {
                    Connect::rollback();
                    return false;
                }
                $a_coin = $model->coin($user_id);
                $coin_log_model->addLog($user_id, -1, $coin, $a_coin, $msg);
            }
        }
        $res = $this->update(['status' => ['`status`+1']], $where);
        if (!$res) {
            Connect::rollback();
            return false;
        }
        Connect::commit();
        return true;
    }
    /**
     * 获取申请上庄列表
     * @param  integer $video_id 直播间id
     * @param  integer $limit    列表限制
     * @param  string  $order    列表排序
     * @return array             上庄列表
     */
    public function getBankerList($video_id, $limit = 5, $order = 'coin desc')
    {
        $table = 'user u,banker_log l';
        $where = [
            'l.user_id'  => ['u.id'],
            'l.coin'     => ['<=', 'u.coin', 'AND', 1],
            'l.video_id' => $video_id,
            'l.status'   => 1,
        ];
        if (defined('OPEN_DIAMOND_GAME_MODULE') && OPEN_DIAMOND_GAME_MODULE == 1) {
            $where['l.coin'] = ['<=', 'u.diamonds', 'AND', 1];
        } else {
            $where['l.coin'] = ['<=', 'u.coin', 'AND', 1];
        }
        $field = 'l.id banker_log_id,l.user_id banker_id,u.nick_name banker_name,u.head_image banker_img,l.coin coin';
        $list  = $this->table($table)->field($field)->limit($limit)->order($order)->select($where);
        foreach ($list as $key => $value) {
            $list[$key]['banker_img'] = get_abs_img_root($value['head_image']);
        }
        return $list;
    }
}
