<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 用户组织关系详情
 * C::m('#pro#pro_profile')->func()
 **/
class model_pro_user_organization
{
	private $_user_map = array();

	// 获取用户的详情
    public function getByUid($uid) 
    {
		$map = &$this->_user_map;
		if (!isset($map[$uid])) {
			$map[$uid] = C::t('#pro#pro_user_organization')->getUserOrganization($uid);
		}
		return $map[$uid];
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
