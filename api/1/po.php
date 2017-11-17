<?php
if (!defined('IN_PRO_API')) {
    exit('Access Denied');
}
/**
 * PO模块API
 **/
require './source/class/class_core.php';
$discuz = C::app();
$discuz->init();
require_once PRO_PLUGIN_PATH."/class/env.class.php";

////////////////////////////////////
// action的用户组列表（空表示全部用户组）
$actionlist = array(
    'queryMine' => array(),

    'create' => array(),
    'getDetail' => array(),
    'save' => array(),

    'setPrid' => array(),
    'setSupplier' => array(),
    'setAddr' => array(),
    'setArrivalDate' => array(),

    'syncPr' => array(),
    'queryItems' => array(),
    'removeItem' => array(),
    'saveItem' => array(),

    'submit' => array(),
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

function create(){ return C::t('#pro#pro_po')->create(); }
function getDetail() { 
    $poid = pro_validate::getNCParameter('poid','poid','string');
    if (!is_numeric($poid)) {
        $poid = C::m('#pro#pro_authcode')->decodeID($poid);
    }
    return C::m('#pro#pro_po')->getPODetail($poid); 
}
function save(){ return C::t('#pro#pro_po')->save(); }

function setPrid(){ return C::t('#pro#pro_po')->setPrid(); }
function setSupplier() { return C::t('#pro#pro_po')->setSupplier(); }
function setAddr() 
{/*{{{*/
    $poid = pro_validate::getNCParameter('poid','poid','integer');
    $data = array (
        'addrid' => pro_validate::getNCParameter('addrid','addrid','integer')
    );
    return C::t('#pro#pro_po')->save($poid,$data);
}/*}}}*/
function setArrivalDate()
{/*{{{*/
    $poid = pro_validate::getNCParameter('poid','poid','integer');
    $data = array (
        'arrival_date' => pro_validate::getNCParameter('arrival_date','arrival_date','string')
    );
    return C::t('#pro#pro_po')->save($poid,$data);
}/*}}}*/


function syncPr(){ return C::t('#pro#pro_po_items')->syncPrItems(); }
function queryItems() { return C::t('#pro#pro_po_items')->queryItems(); }
function removeItem() { return C::t('#pro#pro_po_items')->removeItem(); }
function saveItem() { return C::t('#pro#pro_po_items')->saveItem(); }

function submit() { return C::m('#pro#pro_po')->submit(); }

function queryMine() { return C::t('#pro#pro_po')->queryByCreator(); }

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
