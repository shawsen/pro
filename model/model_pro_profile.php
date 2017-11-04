<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 用户资料模块
 * C::m('#pro#pro_profile')->func()
 **/
class model_pro_profile
{
    public function getByUid($uid) 
    {
        $table_common_member = DB::table('common_member');
        $table_common_member_profile = DB::table('common_member_profile');
        $sql = <<<EOF
SELECT a.uid,a.username,a.email,b.realname,b.telephone,b.mobile
FROM $table_common_member as a LEFT JOIN $table_common_member_profile as b ON a.uid=b.uid
WHERE a.uid='$uid'
EOF;
        return DB::fetch_first($sql);
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
