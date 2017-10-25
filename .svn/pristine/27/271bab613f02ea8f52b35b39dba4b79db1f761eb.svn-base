<?php

class VideoPropAction extends CommonAction
{
    public function index()
    {
        $room_id = intval($_REQUEST['room_id']);

        $prop_list = M("prop")->where("is_effect <>0")->findAll();

        $prop_table = M("Video")->where("id =" . $room_id)->find();

        $user_info = M("User")->where("id =" . $prop_table['user_id'])->find();

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
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1 group by id ";


        $count_sql = "SELECT count(l.id)  as tpcount
                         FROM   {$prop_table['prop_table']} as l
                         LEFT JOIN " . DB_PREFIX . "user AS u  ON l.from_user_id = u.id" . " LEFT JOIN " . DB_PREFIX . "prop AS v ON l.prop_name = v.name" . "
                         WHERE $where " . "   and " . $sql_w . " 1=1  ";

        $volist = $this->_Sql_list($model, $sql_str, '&' . $parameter, 1, 0, $count_sql);
        foreach ($volist as $k => $v) {
            if ($volist[$k]['prop_id'] == 12) {
                $volist[$k]['total_ticket'] = '';
            }
            $volist[$k]['create_time'] = date('Y-m-d', $volist[$k]['create_time']);
            $volist[$k]['nick_name'] = emoji_decode($v['nick_name']);
        }
        $user_info['nick_name'] = emoji_decode($user_info['nick_name']);


        $this->assign("room_id", $room_id);
        $this->assign("prop", $prop_list);
        $this->assign("list", $volist);
        $this->assign("user_info", $user_info);
        $this->display();
    }
}
