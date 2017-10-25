<?php

class LivePayLogAction extends CommonAction
{
    public function index()
    {
        $room_id = intval($_REQUEST['room_id']);

        $table_name = 'live_pay_log';
        $video = M('Video')->where(array('id' => $room_id))->find();
        if (empty($video)) {
            $table_name = 'live_pay_log_history';
        }

        $where = "video_id =" . $room_id;
        $sql_str = "SELECT l.id , l.video_id, l.create_ym , l.to_user_id , l.create_time , l.from_user_id , l.create_date ,l.total_diamonds ,l.live_fee, l.live_pay_time , u.nick_name
                         FROM " . DB_PREFIX . "{$table_name} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.to_user_id = u.id" . " 
                         WHERE $where ";

        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM  " . DB_PREFIX . "{$table_name} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l . to_user_id = u . id" . " 
                         WHERE $where ";

        $model = D($table_name);
        $volist = $this->_Sql_list($model, $sql_str, '', 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            $volist[$k]['create_time'] = to_date('Y-m-d H:i:s', $volist[$k]['create_time']);
        }

        $this->assign("room_id", $room_id);
        $this->assign("list", $volist);
        $this->display();
    }
}
