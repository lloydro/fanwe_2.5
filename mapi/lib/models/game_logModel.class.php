<?php
/**
 *
 */
class game_logModel extends NewModel
{
    public function getList($field = '', $where = '', $order = '', $limit = 20)
    {
        return $this->field($field)->order($order)->limit($limit)->select($where);
    }
    /**
     * 新增游戏日志
     * @param integer $podcast_id 直播id
     * @param integer $long_time  游戏时长
     * @param integer $game_id    游戏（种类）id
     * @param integer $banker_id  庄家用户id
     */
    public function addLog($podcast_id, $long_time, $game_id, $banker_id = 0)
    {
        $data = array(
            'podcast_id'      => intval($podcast_id),
            'long_time'       => intval($long_time),
            'game_id'         => intval($game_id),
            'banker_id'       => intval($banker_id),
            'create_time'     => NOW_TIME,
            'create_date'     => to_date(NOW_TIME, 'Y-m-d H:i:s'),
            'create_time_ymd' => to_date(NOW_TIME, 'Y-m-d'),
            'create_time_y'   => to_date(NOW_TIME, 'Y'),
            'create_time_m'   => to_date(NOW_TIME, 'm'),
            'create_time_d'   => to_date(NOW_TIME, 'd'),
        );
        return $this->insert($data);
    }
    /**
     * 停止游戏
     * @param  integer $id 游戏日志id
     * @return integer     受影响行数
     */
    public function stop($id)
    {
        return $this->update(['long_time' => 0], ['id' => intval($id)]);
    }
    /**
     * [multiAddLog description]
     * @param  [type] $game_log_id    [description]
     * @param  [type] $result         [description]
     * @param  [type] $bet            [description]
     * @param  [type] $suit_patterns  [description]
     * @param  [type] $podcast_income [description]
     * @param  [type] $income         [description]
     * @return [type]                 [description]
     */
    public function resultLog($game_log_id, $result, $bet, $suit_patterns, $podcast_income, $income)
    {
        $status = 2;
        $data   = compact('result', 'bet', 'suit_patterns', 'podcast_income', 'income', 'status');
        return $this->update($data, ['id' => intval($game_log_id)]);
    }
}
