<?php
if (!defined('IN_PRO_API')) {
    exit('Access Denied');
}
/**
 * PR模块API
 **/
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once PRO_PLUGIN_PATH."/class/env.class.php";


////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
    'create' => array(),
    'save' => array(),
    'submit' => array(),
    'submitCancel' => array(),
    'remove' => array(),
    'queryMine' => array(),
    'saveItem' => array(),
    'removeItem' => array(),
    'queryItems' => array(),
    'queryAuditSucc' => array(),
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

function create() { return C::t('#pro#pro_pr')->create(); }
function save() { return C::t('#pro#pro_pr')->save(); }
function submit() { return C::t('#pro#pro_pr')->submit(); }
function submitCancel() { return C::t('#pro#pro_pr')->submitCancel(); }
function remove() { return C::t('#pro#pro_pr')->remove(); }
function queryMine() { return C::t('#pro#pro_pr')->queryByCreator(); }
function saveItem(){ return C::t('#pro#pro_pr_items')->save(); }
function removeItem(){ return C::t('#pro#pro_pr_items')->removeItem(); }
function queryItems(){ return C::t('#pro#pro_pr_items')->queryItems(); }
function queryAuditSucc() {return C::t('#pro#pro_pr')->queryAuditSucc();}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
