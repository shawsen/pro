<?php
if (!defined('IN_EBID_API')) {
    exit('Access Denied');
}
/**
 * 普通用户API
 **/
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once EBID_PLUGIN_PATH."/class/env.class.php";

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
	'prQuery'  => array(),  //!< PR单列表查询
    'prCreate' => array(),  //!< 创建PR单
    'prSubmit' => array(),  //!< 提交PR单
    'prCancel' => array(),  //!< 撤销PR单
    'prDetail' => array(),  //!< PR单详情
);
////////////////////////////////////
$uid      = $_G['uid'];
$username = $_G['username'];
$groupid  = $_G["groupid"];
$action   = isset($_GET['action']) ? $_GET['action'] : "get";

try {
    //////////////////////////////////////////////////
    $auth = C::t('#pro#pro_auth')->getByUid($uid);
    if ($auth!=1) {
        throw new Exception("permission denied");
    }
    //////////////////////////////////////////////////
    if (!isset($actionlist[$action])) {
        throw new Exception('unknow action');
    }
    $groups = $actionlist[$action];
    if (!empty($groups) && !in_array($groupid, $groups)) {
        throw new Exception('illegal request');
    }
    $res = $action();
    pro_env::result(array("data"=>$res));
} catch (Exception $e) {
    pro_env::result(array('retcode'=>100010,'retmsg'=>$e->getMessage()));
}

// PR单
function prQuery(){return C::t('#pro#pro_pr')->queryByCreator();}
function prCreate(){return C::t('#pro#pro_pr')->create();}
function prSubmit(){return C::t('#pro#pro_pr')->submit();}
function prCancel(){return C::t('#pro#pro_pr')->submitCancel();}
function prDetail()
{
    $prid = pro_validate::getNCParameter('prid','prid','integer');
    return C::m('#pro#pro_pr')->getPRDetail($prid);
}


// vim600: sw=4 ts=4 fdm=marker syn=php
?>
