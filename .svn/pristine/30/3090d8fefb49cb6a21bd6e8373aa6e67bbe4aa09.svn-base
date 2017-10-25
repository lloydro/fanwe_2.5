<?php

class QkTreeAction extends CommonAction
{
    public function __construct()
    {
        parent::__construct();
    }

    //树苗列表
    public function index()
    {
        $now = get_gmtime();//获取当前时间
        $parameter = '';
        $sql_w = '';

        if (trim($_REQUEST['title']) != '')//树苗名称
        {
            $parameter .= "title like " . urlencode('%' . trim($_REQUEST['title']) . '%') . "&";
            $sql_w .= "title like '%" . trim($_REQUEST['title']) . "%' and ";
        }

        if (trim($_REQUEST['diamonds']) != '')//钻石
        {
            $parameter .= " diamonds = " . trim($_REQUEST['diamonds']) . "&";
            $sql_w .= " diamonds = " . trim($_REQUEST['diamonds']) . " and ";
        }

        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date($now, 'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;

        if (trim($_REQUEST['create_time_1']) != '')//起始时间
        {
            $parameter .= " create_at between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "'&";
            $sql_w .= " create_at between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "' and ";
        }

        $sql_str = "SELECT * " .
            " FROM " . DB_PREFIX . "qk_tree WHERE 1=1 ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "qk_tree WHERE 1=1 ";

        $sql_str .= " and " . $sql_w . " 1=1";
        $count_sql .= " and " . $sql_w . " 1=1";

        $model = D();
        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'sort', 0, $count_sql);

        foreach ($voList as $k => $v) {
            $voList[$k]['title'] = emoji_decode($v['title']);
            $voList[$k]['image'] = get_spec_image($v['image']);
        }

        $this->assign('url_name', get_manage_url_name());
        $this->assign('list', $voList);
        $this->display();
    }

    //添加树苗
    public function add_tree()
    {
        $this->display();
    }

    //插入树苗数据
    public function insert_tree()
    {
        $data['title'] = strim($_REQUEST['title']);//树苗名称
        $data['image'] = strim($_REQUEST['image']);//树苗图片
        $data['description'] = strim($_REQUEST['description']);//树苗简介
        $data['diamonds'] = strim($_REQUEST['diamonds']);//钻石价格
        $data['sort'] = intval($_REQUEST['sort']);//排序
        $now = get_gmtime();//当前时间
        $data['create_at'] = $now;//创建时间
        $data['is_effect'] = 1;//是否有效
        if (empty($data['title'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入树苗名称'));
        }

        if (empty($data['image'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请上传树苗图片'));
        }

        if (empty($data['description'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入树苗简介'));
        }

        if (empty($data['diamonds'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入钻石价格'));
        }

        if (empty($data['sort'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入排序'));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "qk_tree", $data, 'INSERT');
        if ($GLOBALS['db']->affected_rows()) {
            $res['status'] = 1;
            $res['error'] = '添加树苗成功';
        } else {
            $res['status'] = 0;
            $res['error'] = '添加树苗失败';
        }

        admin_ajax_return($res);
    }

    //设置状态
    public function set_effect()
    {
        $id = intval($_REQUEST['id']);//树苗id
        $ajax = intval($_REQUEST['ajax']);
        $name = $this->getActionName();

        $c_is_effect = M($name)->where("id=" . $id)->getField("is_effect");  //当前状态
        $n_is_effect = $c_is_effect == 0 ? 1 : 0; //需设置的状态
        $result = M($name)->where("id=" . $id)->setField("is_effect", $n_is_effect);
        $this->ajaxReturn($n_is_effect, l("SET_EFFECT_" . $n_is_effect), 1);
    }

    //设置排序
    public function set_sort()
    {
        $id = intval($_REQUEST['id']);//树苗ID
        $sort = intval($_REQUEST['sort']);//排序
        $name = $this->getActionName();
        $tree_info = M($name)->where("id=" . $id)->find();

        if (!check_sort($sort)) {
            $this->error(l("SORT_FAILED"), 1);
        }

        M($name)->where("id=" . $id)->setField("sort", $sort);

        save_log($tree_info['title'] . l("SORT_SUCCESS"), 1);

        $this->success(l("SORT_SUCCESS"), 1);
    }

    //树苗编辑
    public function edit_tree()
    {
        $id = intval($_REQUEST['tree_id']);//树苗ID

        $name = $this->getActionName();
        $tree_info = M($name)->where("id=" . $id)->find();

        $this->assign("vo", $tree_info);
        $this->display();
    }

    //更新数据
    public function update_tree()
    {
        $id = intval($_REQUEST['id']);//树苗ID
        $data['title'] = strim($_REQUEST['title']);//树苗名称
        $data['image'] = strim($_REQUEST['image']);//树苗图片
        $data['description'] = strim($_REQUEST['description']);//树苗简介
        $data['diamonds'] = strim($_REQUEST['diamonds']);//钻石价格
        $data['sort'] = intval($_REQUEST['sort']);//排序
        $now = get_gmtime();
        $data['update_at'] = $now;//更新时间

        if (empty($data['title'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入树苗名称'));
        }

        if (empty($data['image'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请上传树苗图片'));
        }

        if (empty($data['description'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入树苗简介'));
        }

        if (empty($data['diamonds'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入钻石价格'));
        }

        if (empty($data['sort'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请输入排序'));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "qk_tree", $data, 'UPDATE', 'id =' . $id);
        if ($GLOBALS['db']->affected_rows()) {
            $res['status'] = 1;
            $res['error'] = '编辑树苗成功';
        } else {
            $res['status'] = 0;
            $res['error'] = '编辑树苗失败';
        }

        admin_ajax_return($res);
    }

    //订单列表
    public function order_info()
    {
        $now = get_gmtime();
        $parameter = '';
        $sql_w = '';

        //用户ID
        if (trim($_REQUEST['user_id']) != '') {
            $parameter .= " user_id = " . trim($_REQUEST['user_id']) . "&";
            $sql_w .= " u.id = " . trim($_REQUEST['user_id']) . " and ";
        }

        //订单号
        if (trim($_REQUEST['order_id']) != '') {
            $parameter .= " order_id = " . trim($_REQUEST['order_id']) . "&";
            $sql_w .= " o.id = " . trim($_REQUEST['order_id']) . " and ";
        }

        //树苗名称
        if (trim($_REQUEST['title']) != '') {
            $parameter .= " title like " . urlencode('%' . trim($_REQUEST['title']) . '%') . "&";
            $sql_w .= " t.title like '%" . trim($_REQUEST['title']) . "%' and ";
        }

        if (trim($_REQUEST['diamonds']) != '') {
            $parameter .= " diamonds = " . trim($_REQUEST['diamonds']) . "&";
            $sql_w .= " o.pay = " . trim($_REQUEST['diamonds']) . " and ";
        }

        //订单创建时间
        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date($now, 'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;
        if (trim($_REQUEST['create_time_1']) != '') {
            $parameter .= " create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "'&";
            $sql_w .= " o.create_time between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "' and ";
        }

        $sql_str = "SELECT o.*,t.title,u.nick_name " .
            " FROM " . DB_PREFIX . "qk_tree_order o," . DB_PREFIX . "qk_tree t," . DB_PREFIX . "user u WHERE o.user_id =u.id and o.tree_id=t.id and 1=1 ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "qk_tree_order o," . DB_PREFIX . "qk_tree t," . DB_PREFIX . "user u WHERE o.user_id =u.id and o.tree_id=t.id and 1=1 ";

        $sql_str .= " and " . $sql_w . " 1=1";
        $count_sql .= " and " . $sql_w . " 1=1";

        $model = D();
        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'o.id', 0, $count_sql);

        foreach ($voList as $k => $v) {
            $voList[$k]['title'] = emoji_decode($v['title']);
        }

        $this->assign('url_name', get_manage_url_name());
        $this->assign('list', $voList);
        $this->display();
    }

    //树苗成长列表
    public function tree_manage_list()
    {
        $order_id = intval($_REQUEST['order_id']);//订单编号

        if (!$order_id) {
            admin_ajax_return(array('status' => '0', 'error' => '订单信息有误'));
        }

        $now = get_gmtime();
        $parameter = '';
        $sql_w = '';

        //树苗信息创建时间
        $create_time_2 = empty($_REQUEST['create_time_2']) ? to_date($now, 'Y-m-d') : strim($_REQUEST['create_time_2']);
        $create_time_2 = to_timespan($create_time_2) + 24 * 3600;
        if (trim($_REQUEST['create_time_1']) != '') {
            $parameter .= " create_at between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "'&";
            $sql_w .= " i.create_at between '" . to_timespan($_REQUEST['create_time_1']) . "' and '" . $create_time_2 . "' and ";
        }

        $sql_str = "SELECT i.* " .
            " FROM " . DB_PREFIX . "qk_tree_info i," . DB_PREFIX . "qk_tree_order o WHERE i.order_id =o.id and 1=1 ";

        $count_sql = "SELECT count(*)  as tpcount FROM " . DB_PREFIX . "qk_tree_info i," . DB_PREFIX . "qk_tree_order o WHERE i.order_id =o.id and 1=1  ";

        $sql_str .= " and i.order_id =" . $order_id . " and " . $sql_w . " 1=1";
        $count_sql .= " and  i.order_id =" . $order_id . " and " . $sql_w . " 1=1";

        $model = D();
        $voList = $this->_Sql_list($model, $sql_str, "&" . $parameter, 'i.id', 0, $count_sql);

        foreach ($voList as $k => $v) {
            $voList[$k]['image'] = get_spec_image($v['image']);
        }

        //用户昵称
        $user_id = $GLOBALS['db']->getOne("SELECT u.id FROM ".DB_PREFIX."user u WHERE u.id = (SELECT user_id FROM ".DB_PREFIX."qk_tree_order o WHERE o.id =".$order_id.")");

        $this->assign('user_id',$user_id);
        $this->assign('order_id', $order_id);
        $this->assign('url_name', get_manage_url_name());
        $this->assign('list', $voList);
        $this->display();

    }

    //添加树苗成长信息
    public function add_tree_info()
    {
        $order_id = intval($_REQUEST['order_id']);//订单编号

        $this->assign('order_id', $order_id);
        $this->display();
    }

    //插入数据
    public function insert_tree_info()
    {
        $data['image'] = trim($_REQUEST['image']);//树苗成长图片
        $data['order_id'] = intval($_REQUEST['order_id']);//订单编号
        $now = get_gmtime();
        $data['create_at'] = $now;//成长信息创建时间
        $data['shoot_time'] = to_timespan(trim($_REQUEST['shoot_time']));

        if (!$data['order_id']) {
            admin_ajax_return(array('status' => '0', 'error' => '订单信息有误'));
        }

        if (!$data['image']) {
            admin_ajax_return(array('status' => '0', 'error' => '请上传树苗图片'));
        }

        if (!trim($_REQUEST['shoot_time'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请选择拍摄时间'));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "qk_tree_info", $data, 'INSERT');
        if ($GLOBALS['db']->affected_rows()) {
            $res['status'] = 1;
            $res['error'] = '添加成功';
        } else {
            $res['status'] = 0;
            $res['error'] = '添加失败';
        }

        admin_ajax_return($res);
    }

    //编辑树苗成长信息
    public function edit_tree_info()
    {
        $id = intval($_REQUEST['id']);//树苗成长信息ID

        $tree_info = M('QkTreeInfo')->where('id =' . $id)->find();//树苗成长信息
        $tree_info['image'] = get_spec_image($tree_info['image']);
        $tree_info['shoot_time'] = to_date($tree_info['shoot_time']);

        $this->assign('tree_info', $tree_info);
        $this->display();
    }

    //更新数据
    public function update_tree_info()
    {
        $id = intval($_REQUEST['id']);//树苗成长信息ID
        $data['image'] = trim($_REQUEST['image']);//树苗成长图片
        $data['order_id'] = intval($_REQUEST['order_id']);//订单编号
        $now = get_gmtime();
        $data['update_at'] = $now;//更新时间
        $data['shoot_time'] = to_timespan(trim($_REQUEST['shoot_time']));

        if (!$data['order_id']) {
            admin_ajax_return(array('status' => '0', 'error' => '订单信息有误'));
        }

        if (!$data['image']) {
            admin_ajax_return(array('status' => '0', 'error' => '请上传树苗图片'));
        }

        if (!trim($_REQUEST['shoot_time'])) {
            admin_ajax_return(array('status' => '0', 'error' => '请选择拍摄时间'));
        }

        $GLOBALS['db']->autoExecute(DB_PREFIX . "qk_tree_info", $data, 'UPDATE', 'id =' . $id);
        if ($GLOBALS['db']->affected_rows()) {
            $res['status'] = 1;
            $res['error'] = '编辑成功';
        } else {
            $res['status'] = 0;
            $res['error'] = '编辑失败';
        }

        admin_ajax_return($res);
    }

    //检查图片大小

}