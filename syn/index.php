<?php 

define("FANWE_REQUIRE",true);
require '../mapi/lib/core/mapi_function.php';
require '../public/directory_init.php';
$_REQUEST['ctl'] = filter_ma_request_mapi($_REQUEST['ctl']);
$_REQUEST['act'] = filter_ma_request_mapi($_REQUEST['act']);
$class = strtolower(strim_mapi($_REQUEST['ctl']))?strtolower(strim_mapi($_REQUEST['ctl'])):"syn";
$act = strtolower(strim_mapi($_REQUEST['act']))?strtolower(strim_mapi($_REQUEST['act'])):"index";
$fun_class = $class.'#'.$act;
require '../system/mapi_init.php';

fanwe_require(APP_ROOT_PATH.'mapi/lib/core/common.php');

fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');

filter_injection($_REQUEST);

 fanwe_require("./lib/base.action.php");
 fanwe_require("./lib/".$class.".action.php");
 $class=$class.'Module';

 if(class_exists($class)){
	 $obj = new $class;
	 if(method_exists($obj, $act)){
		 $obj->$act();
	 }
	 else{
		 $error["errcode "] = 10006;
		 $error["errmsg "] = "接口方法不存在";
		 ajax_return($error);
	 }
 }
 else {
	 $error["errcode "] = 10005;
	 $error["errmsg "] = "接口不存在";
	 ajax_return($error);
 }

?>