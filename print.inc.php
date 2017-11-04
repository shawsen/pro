<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once dirname(__FILE__)."/class/env.class.php";
include_once dirname(__FILE__)."/lang/lang.inc.php";


$form = $_GET['form'] ? $_GET['form'] : '';
$formid = $_GET['formid'] ? $_GET['formid'] : '';

// 用户权限
$uid = $_G['uid'];
$auth = $uid==0 ? 0 : C::t('#pro#pro_auth')->getByUid($uid);

// 设置
$setting = C::m('#pro#pro_setting')->get();
$page_style = $setting['page_style'];
$pluginPath = pro_env::get_plugin_path();
$ajaxapi = $pluginPath."/index.php?version=4&module=";

$filename = basename(__FILE__);
list($controller) = explode('.',$filename);
include template("pro:".strtolower($controller));
C::t('#pro#pro_log')->write("visit pro:print");
