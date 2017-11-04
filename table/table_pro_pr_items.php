<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PR单项目表
 **/
class table_pro_pr_items extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_pr_items';
		$this->_pk = 'prid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 获取PR单所有有效项目
	public function getAllByPrid($prid)
	{/*{{{*/
        $where = "isdel=0 AND prid='$prid'";
        $table_this = DB::table($this->_table);
        $sql = "SELECT * FROM $table_this WHERE $where";
        return DB::fetch_all($sql);
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
