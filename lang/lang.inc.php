<?php
if (!defined('IN_DISCUZ')) {
    exit('Access Denied');
}
switch ($_COOKIE["ebid-lang"]) {
	case 'en': include_once('en.inc.php'); break;
	default: include_once('zh_cn.inc.php'); break;
}
