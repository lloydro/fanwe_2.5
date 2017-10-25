<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class Tg_distributionAction extends AuthAction
{
    //首页
    public function index()
    {
        if (trim($_REQUEST['nick_name']) != '') {
            $where = " and u.nick_name like '%" . trim($_REQUEST['nick_name']) . "%'";
        }
        if (trim($_REQUEST['mobile']) != '') {

            $where .= "and u.mobile like '%" . trim($_REQUEST['mobile']) . "%'";
        }

        if (intval($_REQUEST['id']) != '') {

            $where .= "and u.id =" . intval($_REQUEST['id']) . "";
        }

        // $sql = "select u1.id,u1.nick_name,u1.mobile,sum(u3.first_distreibution_money+u3.second_distreibution_money) as num from ".DB_PREFIX."user as u1,".DB_PREFIX."game_distribution as u3 where u1.id=u3.user_id $where GROUP BY u1.game_distribution_top_id";
        $pre = DB_PREFIX;
        $sql = "SELECT
                    u.id,
                    u.nick_name,
                    u.mobile,
                    ifnull(a.num, 0) AS num,
                    b.count
                FROM
                    {$pre}user AS u
                LEFT JOIN (
                    SELECT
                        a1.id,
                        SUM(
                            a2.first_distreibution_money + a2.second_distreibution_money
                        ) AS num
                    FROM
                        {$pre}user AS a1,
                        {$pre}game_distribution AS a2
                    WHERE
                        a1.id = a2.user_id
                        {$where}
                    GROUP BY
                        a1.game_distribution_top_id
                ) AS a ON a.id = u.id
                LEFT JOIN (
                    SELECT
                        a1.game_distribution_top_id id,
                        count(1) count
                    FROM
                        {$pre}user AS a1
                    GROUP BY
                        a1.game_distribution_top_id
                ) AS b ON b.id = u.id
                WHERE
                    u.id = u.game_distribution_top_id";

        $model     = M('user');
        $user_list = $model->query($sql);

        $this->assign('user_list', $user_list);
        $this->display();
    }

    //游戏分销
    public function yx_distribution()
    {
        $root = $this->getRoot();
        $this->assign('sum_money', intval($root['sum_money']));
        $this->assign('sum_distribution', intval($root['sum_distribution']));
        $this->display();
    }

    //游戏消费
    public function yx_consumption()
    {
        $root = $this->getRoot();
        $this->assign('sum_bet', intval($root['sum_bet']));
        $this->assign('sum_gain', intval($root['sum_gain']));
        $this->display();
    }

    //礼物分销
    public function lw_distribution()
    {
        $root = $this->getRoot();
        $this->assign('sum_money', intval($root['sum_money']));
        $this->assign('sum_distribution', intval($root['sum_distribution']));
        $this->display();
    }

    //礼物消费
    public function lw_consumption()
    {
        $this->assign('total_diamonds', intval($root['total_diamonds']));
        $this->display();
    }

    public function getRoot()
    {
        $root = $this->getRoot();

        $id = $_REQUEST['id'];
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        NewModel::$lib = APP_ROOT_PATH . 'mapi/lib/';

        $type        = intval($_REQUEST['type']); //0游戏分销，1礼物分销，2游戏消费，3礼物消费
        $year        = intval($_REQUEST['year']);
        $month       = intval($_REQUEST['month']);
        $user_id     = intval($_REQUEST['user_id']);
        $game_log_id = intval($_REQUEST['game_log_id']);
        if (!isset($_REQUEST['is_group'])) {
            $_REQUEST['is_group'] = 1;
        }

        $where = [];

        $y = date('Y');
        $m = date('m');
        if (!($year && $month)) {
            $year  = $y;
            $month = $m;
        }
        $start                  = strtotime("{$year}-{$month}-1 00:00:00");
        $end                    = strtotime('+1 month', $start);
        $where['l.create_time'] = ['between', [$start, $end]];
        if ($user_id) {
            $where['d.id'] = $user_id;
        }
        if ($game_log_id) {
            $where['l.game_log_id'] = $game_log_id;
        }
        $d = NewModel::build('user')->field('game_distribution_top_id topid')->selectOne(['id' => $id]);
        if ($d['topid']) {
            $model = NewModel::build('game_distribution');
            switch ($type) {
                case 1:
                    $root = $model->childPropDistribution($id, $where, intval($_REQUEST['is_group']));
                    break;
                case 2:
                    $root = $model->childGame($id, $where, intval($_REQUEST['is_group']));
                    break;
                case 3:
                    unset($where['l.create_time']);
                    unset($where['l.game_log_id']);
                    $root = $model->childProp($id, $where, $start, intval($_REQUEST['is_group']));
                    break;
                default:
                    $root = $model->childGameDistribution($id, $where, intval($_REQUEST['is_group']));
                    break;
            }
        }
        $root['type']        = $type;
        $root['page_title']  = "个人中心-游戏分销";
        $root['year']        = $year;
        $root['month']       = $month;
        $root['user_id']     = $user_id;
        $root['game_log_id'] = $game_log_id;
        $root['years']       = range($y, $y - 5);
        $root['months']      = range(1, 12);
        $root['act']         = 'game_distribution';

        $this->assign("is_group", $_REQUEST['is_group']);
        $this->assign("year", $root['year']);
        $this->assign("month", $root['month']);
        $this->assign("years", $root['years']);
        $this->assign("months", $root['months']);
        $this->assign('list', $root['list']);
        $this->assign('sum_first', intval($root['sum_first']));
        $this->assign('sum_second', intval($root['sum_second']));
        $this->assign('sum_child', intval($root['sum_child']));
    }
}
