<?php
// +----------------------------------------------------------------------
// | FANWE 直播系统
// +----------------------------------------------------------------------
// | Copyright (c) 2011 http://www.fanwe.com All rights reserved.
// +----------------------------------------------------------------------
// | Author: 云淡风轻(88522820@qq.com)
// +----------------------------------------------------------------------

class baseCModule  extends baseModule
{
    public static $lib='';
    var $t_user = array(
        'id' => 101031
    );
    var $image_pay_type = array(
        'red_photo','photo'
    );
	public function __construct()
	{
        parent::__construct();
        require_once APP_ROOT_PATH . 'mapi/lib/core/Model.class.php';
        fanwe_require(APP_ROOT_PATH.'mapi/xr/core/common.php');
        Model::$lib = dirname(__FILE__);
        if($_REQUEST['test']==1){
            if($_REQUEST['test']==1){
                $GLOBALS['user_info'] = $this->t_user;
            }
        }
    }

}