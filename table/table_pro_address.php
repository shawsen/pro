<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 地址表
 **/
class table_pro_address extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_address';
		$this->_pk = 'addrid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 管理后台查询接口
	public function query()
	{/*{{{*/
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$key   = pro_validate::getNCParameter('key','key','string'); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'displayorder');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'ASC');
        $start = pro_validate::getOPParameter('start','start','integer',1024,0);
        $limit = pro_validate::getOPParameter('limit','limit','integer',1024,20);
        $where = "isdel=0";
		if ($key!="") $where.=" AND (addrtitle like '%$key%' OR address like '%$key%')";
		$table_pro_address = DB::table($this->_table);
		$table_common_member = DB::table('common_member');
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*,b.username
FROM $table_pro_address as a LEFT JOIN $table_common_member as b ON a.create_uid=b.uid
WHERE $where ORDER BY $sort $dir LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
