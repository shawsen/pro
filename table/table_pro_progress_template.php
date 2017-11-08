<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 审批流程模板表
 **/
class table_pro_progress_template extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_progress_template';
		$this->_pk = 'tplid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 获取模板的所有流程节点列表
	public function getNodes($module)
	{/*{{{*/
		$table = DB::table($this->_table);
		$sql = <<<EOF
SELECT node_name,can_skip,utype,uid
FROM $table
WHERE module='$module' AND isdel=0 AND isenable=1
ORDER BY porder ASC
EOF;
		return DB::fetch_all($sql);
	}/*}}}*/
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
