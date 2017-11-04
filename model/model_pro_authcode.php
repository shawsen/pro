<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 加减密
 **/
class model_pro_authcode
{
    // 密钥
    private $_dekey = 'PRO2017';

    // 加密
    public function encodeID($id) 
    {
		global $_G;
		$str = $_G['uid']."_".$id."_".time();
		return authcode($str,'ENCODE',$this->_dekey);
    }

    // 解密
    public function decodeID($str) 
    {
        $str = preg_replace('/ /i','+',$str);
		$s = authcode($str,'DECODE',$this->_dekey);
		list($uid,$id,$tm) = explode('_',$s);
		return $id;
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
