<?php
define("FANWE_REQUIRE",true);


if (isset($_REQUEST['cstype']) && $_REQUEST['cstype'] == 1) {
    require '../../system/wap_init.php';
} else {
    require '../../system/pc_init.php';
}

fanwe_require(APP_ROOT_PATH.'mapi/lib/redis/BaseRedisService.php');
require  APP_ROOT_PATH.'system/template/template.php';
$tmpl = new AppTemplate;

$GLOBALS['tmpl']->cache_dir      = APP_ROOT_PATH . 'public/runtime/society/tpl_caches';
$GLOBALS['tmpl']->compile_dir    = APP_ROOT_PATH . 'public/runtime/society/tpl_compiled';
$GLOBALS['tmpl']->template_dir   = APP_ROOT_PATH . 'frontEnd/society/view';
$GLOBALS['tmpl']->assign("TMPL_REAL",APP_ROOT_PATH."frontEnd/society");
$tmpl_path = get_domain().APP_ROOT;
$GLOBALS['tmpl']->assign("TMPL",$tmpl_path);

if(defined("OPEN_SOCIETY_MODULE") && OPEN_SOCIETY_MODULE==1){//公会开关
    $m_config =  load_auto_cache("m_config");//初始化手机端配置
    if($m_config['society_pattern'] == 0){//关闭
        $GLOBALS['tmpl']->assign("moshi",0);
        exit();
    }elseif ($m_config['society_pattern'] == 1){//有抽成
        $GLOBALS['tmpl']->assign("moshi",1);
    }elseif ($m_config['society_pattern'] == 2){//无抽成
        $GLOBALS['tmpl']->assign("moshi",2);
    }
}else{//关闭
    $GLOBALS['tmpl']->assign("moshi",0);
    exit();
}



// $jstmpl_path = get_domain().APP_ROOT."/society/";
// $GLOBALS['tmpl']->assign("JSTMPL",$jstmpl_path);

fanwe_require(APP_ROOT_PATH.'frontEnd/society/lib/core/common.php');
fanwe_require(APP_ROOT_PATH.'mapi/lib/core/common.php');

filter_injection($_REQUEST);


//会员自动登录及输出
$cookie_uid = es_cookie::get("user_id")?es_cookie::get("user_id"):'';
$cookie_upwd = es_cookie::get("user_pwd")?es_cookie::get("user_pwd"):'';

if($cookie_uid!=''&&$cookie_upwd!=''&&!es_session::get("user_info"))
{
    //fanwe_require(APP_ROOT_PATH."system/libs/user.php");	
	fanwe_require(APP_ROOT_PATH."mapi/lib/base.action.php");
	fanwe_require(APP_ROOT_PATH."mapi/society_index/login.action.php");	
    $login = new loginCModule();
    //$login->auto_do_login_user($cookie_uid,$cookie_upwd);
}

//用户信息
global $user_info;
$user_info = es_session::get('user_info');
if (!$user_info && isset($_REQUEST['cstype'])){
    $cstype = $_REQUEST['cstype'];
    
        if (intval($cstype) > 0 ){
            $sql = "select * from ".DB_PREFIX."user where id=".intval($cstype);
        }else{
            $sql = "select * from ".DB_PREFIX."user where id=100324";
        }
    //  es_session::set("user_info",$user_info);
    $user_info = $GLOBALS['db']->getRow($sql);
    //print_r($user_info);
    
}else{
    //print_r($user_info);
}

$cstype = $_REQUEST['cstype'];
if(!$user_info&&$cstype !=''){

    if (intval($cstype) > 0){
        $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=".intval($cstype));
    }else{
        $user_info = $GLOBALS['db']->getRow("select * from ".DB_PREFIX."user where id=290");
    }
//  es_session::set("user_info",$user_info);
}

$GLOBALS['tmpl']->assign("user_info", $user_info);
$_REQUEST['ctl'] = filter_ma_request($_REQUEST['ctl']);
$_REQUEST['act'] = filter_ma_request($_REQUEST['act']);

$search = array("../","\n","\r","\t","\r\n","'","<",">","\"","%","\\",".","/");
$itype = str_replace($search,"",$_REQUEST['itype']);

$class = strtolower(strim($_REQUEST['ctl']))?strtolower(strim($_REQUEST['ctl'])):"society";
$class_name = $class;
$lib = $itype?$itype:'society_index';

if($lib=='lib'){

    fanwe_require(APP_ROOT_PATH."mapi/lib/base.action.php");
    @fanwe_require(APP_ROOT_PATH."mapi/lib/".$class.".action.php");
    $class=$class.'Module';
}else{

    fanwe_require(APP_ROOT_PATH."mapi/lib/base.action.php");
    fanwe_require(APP_ROOT_PATH."mapi/".$lib."/base.action.php");
    fanwe_require(APP_ROOT_PATH."mapi/".$lib."/".$class.".action.php");
    $class=$class.'CModule';
}

$act = strtolower(strim($_REQUEST['act']))?strtolower(strim($_REQUEST['act'])):"user_manage";
$GLOBALS['tmpl']->assign("ctl",$class_name);
$GLOBALS['tmpl']->assign("act",$act);


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
else{
    $error["errcode "] = 10005;
    $error["errmsg "] = "接口不存在";
    ajax_return($error);
}



?>