<?php
if (!defined('IN_PRO_API')) {
    exit('Access Denied');
}
/**
 * 审批流程API
 **/
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once PRO_PLUGIN_PATH."/class/env.class.php";

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
	'myflow' => array(),  //!< 我发起的流程
	'mytodo' => array(),  //!< 我的当前待处理的审批流程单
	'detail' => array(),  //!< 流程详情页
    'audit'  => array(),  //!< 审批
);
////////////////////////////////////
$uid      = $_G['uid'];
$username = $_G['username'];
$groupid  = $_G["groupid"];
$action   = isset($_GET['action']) ? $_GET['action'] : "get";

try {
    //////////////////////////////////////////////////
    $auth = C::t('#pro#pro_auth')->getByUid($uid);
    if ($auth==0) {
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

// 我发起的流程
function myflow() {return C::t('#pro#pro_progress')->queryMine();}

// 我的当前待处理的审批流程单
function mytodo() {return C::t('#pro#pro_progress_nodes')->getMyActiveNodes();}

// 流程详情页
function detail() {
	$pgid = pro_validate::getNCParameter('pgid','pgid','string'); 
	return C::m('#pro#pro_progress')->getDetail($pgid);
}

// 审批
function audit() {return C::t('#pro#pro_progress_nodes')->procMyActiveNode();}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
