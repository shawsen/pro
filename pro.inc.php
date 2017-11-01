<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
require_once dirname(__FILE__)."/class/env.class.php";

include_once dirname(__FILE__)."/lang/lang.inc.php";

// 设置
$setting = C::m('#pro#pro_setting')->get();
$page_style = $setting['page_style'];
$pluginPath = pro_env::get_plugin_path();
$ajaxapi = $pluginPath."/index.php?version=4&module=";
$setting['introduction'] = $setting['introduction_zh'];
if ($lang['code']=='en') {
	$setting['page_title'] = "E-Bid System";
	$setting['introduction'] = $setting['introduction_en'];
}

// 导航列表
$navlist = C::m('#pro#pro_nav_setting')->getenablelist();
$navlist = json_encode($navlist);

$filename = basename(__FILE__);
list($controller) = explode('.',$filename);
include template("pro:".strtolower($controller));
