<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmPromoterGameAction extends CommonAction
{
    public function index()
    {
        $nick_name = trim($_REQUEST['nick_name']);//推广会员名称
        $name      = trim($_REQUEST['name']);//上线推广商:
        $p_name    = trim($_REQUEST['p_name']);//上线推广中心:

        $year  = intval($_REQUEST['year']);
        $month = intval($_REQUEST['month']);
        $y     = date('Y');
        $m     = date('m');
        if (!($year && $month)) {
            $year  = $y;
            $month = $m;
        }
        $years  = range($y, $y - 5);
        $months = range(1, 12);

        $start = strtotime("{$year}-{$month}-1 00:00:00");
        $end   = strtotime('+1 month', $start);
        $where = "l.create_time >= {$start} AND l.create_time <= {$end} and bp.is_effect=1 and bp.status=1";
        if ($nick_name) {
            $where .= " and u.nick_name like '%{$nick_name}%'";
        }
        if ($name) {
            $where .= " and bp.`name` like '%{$name}%'";
        }
        if ($p_name) {
            $where .= " and bpp.`name` like '%{$p_name}%'";
        }
        $pre = DB_PREFIX;

        $sql = "SELECT
                    count(1) as count
                FROM
                    (
                    SELECT
                        1
                    FROM
                        {$pre}bm_promoter_game_log AS l
                    LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                    LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                    LEFT JOIN {$pre}bm_promoter AS bpp ON bp.pid = bpp.user_id
                    WHERE
                        {$where}
                    GROUP BY
                        l.user_id
                    ) AS a";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p         = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit     = (($p - 1) * $page_size) . "," . $page_size;

        $count     = $GLOBALS['db']->getOne($sql, true, true);
        $page      = new Page($count, $page_size);
        $page_show = $page->show();

        $sql = "SELECT
                    l.bm_pid,
                    l.user_id,
                    l.game_id,
                    sum(l.sum_bet) AS sum_bet,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.sum_win) AS sum_win,
                    sum(l.promoter_gain) AS promoter_gain,
                    sum(l.platform_gain) AS platform_gain,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.gain) AS gain,
                    l.create_time,
                    l.is_count,
                    bp.`name`,
                    bpp.`name` AS p_name,
                    bp.id,
                    u.nick_name
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                LEFT JOIN {$pre}bm_promoter AS bpp ON bp.pid = bpp.user_id
                WHERE
                    {$where}
                GROUP BY
                    l.user_id
                LIMIt {$limit}";
        $list = $GLOBALS['db']->getAll($sql, true, true);
        $sql = "SELECT
                    sum(l.sum_bet) AS sum_bet,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.sum_win) AS sum_win,
                    sum(l.promoter_gain) AS promoter_gain,
                    sum(l.platform_gain) AS platform_gain,
                    sum(l.sum_gain) AS sum_gain,
                    sum(l.gain) AS gain
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                LEFT JOIN {$pre}bm_promoter AS bpp ON bp.pid = bpp.user_id
                WHERE
                    {$where}";
        $sum = $GLOBALS['db']->getRow($sql, true, true);

        $this->assign("year", $year);
        $this->assign("month", $month);
        $this->assign("years", $years);
        $this->assign("months", $months);
        $this->assign('sum', $sum);
        $this->assign('list', $list);
        $this->assign('page', $page_show);
        $this->display();
    }
    public function detail()
    {
        $id      = intval($_REQUEST['id']);
        $user_id = intval($_REQUEST['user_id']);
        $game_id = intval($_REQUEST['game_id']);
        $win     = intval($_REQUEST['win']);

        $create_time_1 = intval($_REQUEST['create_time_1']);
        $create_time_2 = intval($_REQUEST['create_time_2']);

        $where = "bp.id = {$id} AND l.user_id = {$user_id} AND bp.is_effect=1 and bp.status=1";
        if ($game_id) {
            $where .= ' AND l.game_id=' . $game_id;
        }
        if ($win == 1) {
            $where .= ' AND l.sum_win > 0';
        } else if ($win == -1) {
            $where .= ' AND l.sum_win < 0';
        }
        if ($create_time_1) {
            $where .= ' AND l.create_time > ' . $create_time_1;
        }
        if ($create_time_2) {
            $where .= ' AND l.create_time < ' . $create_time_2;
        }
        $pre = DB_PREFIX;
        $sql = "SELECT
                    count(1) as count
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where}";

        $p = $_REQUEST['p'];
        if ($p == '') {
            $p = 1;
        }
        $p         = $p > 0 ? $p : 1;
        $page_size = 10;
        $limit     = (($p - 1) * $page_size) . "," . $page_size;

        $count     = $GLOBALS['db']->getOne($sql, true, true);
        $page      = new Page($count, $page_size);
        $page_show = $page->show();
        $sql       = "SELECT
                    sum(l.sum_bet) as sum_bet,
                    sum(l.sum_gain) as sum_gain,
                    sum(l.sum_win) as sum_win,
                    sum(l.promoter_gain) as promoter_gain,
                    sum(l.platform_gain) as platform_gain,
                    sum(l.user_gain) as user_gain,
                    sum(l.gain) as gain
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where}";
        $sum  = $GLOBALS['db']->getRow($sql, true, true);

        $sql       = "SELECT
                    l.bm_pid,
                    l.user_id,
                    l.sum_bet,
                    l.sum_gain,
                    l.sum_win,
                    l.promoter_gain,
                    l.platform_gain,
                    l.user_gain,
                    l.gain,
                    l.game_id,
                    l.create_time,
                    l.is_count,
                    bp.`name`,
                    bp.id,
                    u.nick_name
                FROM
                    {$pre}bm_promoter_game_log AS l
                LEFT JOIN {$pre}user AS u ON l.user_id = u.id
                LEFT JOIN {$pre}bm_promoter AS bp ON l.bm_pid = bp.user_id
                WHERE
                    {$where}
                LIMIT $limit";
        $list  = $GLOBALS['db']->getAll($sql, true, true);
        $res   = $GLOBALS['db']->getAll("SELECT id,name FROM {$pre}games WHERE is_effect = 1", true, true);
        $games = [];
        foreach ($res as $value) {
            $games[$value['id']] = $value['name'];
        }
        foreach ($list as $key => $value) {
            $list[$key]['game_type']   = $games[$value['game_id']];
            $list[$key]['create_time'] = to_date($value['create_time']);
        }
        $this->assign('sum', $sum);
        $this->assign('list', $list);
        $this->assign('games', $games);
        $this->assign('page', $page_show);
        $this->display();
    }
}
