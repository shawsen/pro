<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 用户组织关系表
 **/
class table_pro_user_organization extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_user_organization';
		$this->_pk = 'prid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 获取用户的组织关系
	public function getUserOrganization($uid)
	{
		$table_common_member = DB::table('common_member');
		$table_pro_user_organization = DB::table('pro_user_organization');
		$sql = <<<EOF
SELECT a.uid,a.username,a.email,a.groupid,a.adminid,
b.realname,b.group_name,b.supervisor_uid
FROM $table_common_member as a LEFT JOIN $table_pro_user_organization as b ON a.uid=b.uid
WHERE a.uid=1
EOF;
		return DB::fetch_first($sql);
	}
        
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
