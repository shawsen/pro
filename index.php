<?php
/**
 * api入口
 **/
define("IN_EBID_API", 1);
define("EBID_PLUGIN_PATH", dirname(__FILE__));
chdir("../../../");

$modules = array (
	'progress',     //!< 审批流程
    'pr',           //!< PR模块API
    'po',           //!< PO模块API
    'supplier',     //!< 供应商模块API
    'address',      //!< 送货地址模块API

    'requirectl',   //!< 普通用户API
	'purcherctl',   //!< 采购员API
    'seccode','uc','admin',
);

if(!in_array($_GET['module'], $modules)) {
    module_not_exists();
}
$module  = $_GET['module'];
$version = !empty($_GET['version']) ? intval($_GET['version']) : 1;
if($version>4) $version=4;
while ($version>=1) {
    $apifile = EBID_PLUGIN_PATH."/api/$version/$module.php";
    if(file_exists($apifile)) {
        require_once $apifile;
        exit(0);
    }
    --$version;    
}
module_not_exists();

function module_not_exists()
{
	header("Content-type: application/json");
    echo json_encode(array('error' => 'module_not_exists'));
    exit;
}
?>
