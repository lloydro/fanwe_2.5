<?php
// +----------------------------------------------------------------------
// | Fanwe 方维直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 甘味人生(526130@qq.com)
// +----------------------------------------------------------------------

class BmConfigAction extends CommonAction
{
    public function config()
    {
        $vo = M("BmConfig")->findAll();
        $this->assign('vo', $vo);

        $this->display();
    }

    public function update()
    {
        fanwe_require(APP_ROOT_PATH . 'mapi/lib/core/NewModel.class.php');
        NewModel::$lib = APP_ROOT_PATH . 'mapi/lib/';
        // $bm_config=load_auto_cache("bm_config");
        foreach ($_POST as $k => $v) {
            if ($k == 'bm_pid') {
                $pid = M("BmPromoter")->where(['user_id' => intval($_POST['bm_pid'])])->getField('pid');
                if (!$pid) {
                    $this->error("请输入正确的代理商绑定会员ID");
                }
            }
            $res = M("BmConfig")->where("code='" . $k . "'")->setField("val", trim($v));
            if ($k == 'bm_pid' && $res) {
                // $pid = intval($bm_config['bm_pid']);
                $to_pid = intval($v);
                $model = NewModel::build('bm_promoter');
                // $model->changePid($pid, $to_pid);
                $model->changePid(0, $to_pid);
                $model->update_promoter_child($pid);
            }
        }
        clear_auto_cache("bm_config");
        load_auto_cache("bm_config");
        $log_info = "推广设置";
        save_log($log_info . L("UPDATE_SUCCESS"), 1);
        $this->success("保存成功");
    }
}
