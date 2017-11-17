<?php
if (!defined('IN_PRO_API')) {
    exit('Access Denied');
}
/**
 * 供应商模块API
 **/
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once PRO_PLUGIN_PATH."/class/env.class.php";

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
    'queryEffect' => array(),
    'getDetail' => array(),
);
////////////////////////////////////
$uid      = $_G['uid'];
$username = $_G['username'];
$groupid  = $_G["groupid"];
$action   = isset($_GET['action']) ? $_GET['action'] : "get";

try {
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

function queryEffect(){ return C::t('#pro#pro_supplier')->queryEffect(); }
function getDetail(){ 
    $supplier_id = pro_validate::getNCParameter('supplier_id','supplier_id','integer'); 
    return C::t('#pro#pro_supplier')->get_by_pk($supplier_id);
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
