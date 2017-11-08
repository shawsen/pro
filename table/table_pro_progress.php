<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 审批流程主表
 **/
class table_pro_progress extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_progress';
		$this->_pk = 'pgid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 创建一条流程单记录
	public function create($module,$module_id,$title)
	{/*{{{*/
		global $_G;
		$data = array (
			'module'     => $module,
			'module_id'  => $module_id,
			'progress_title' => $title,
			'origin_uid' => $_G['uid'],
			'status'     => PRO_AUDIT_TODO,
			'ctime'      => date('Y-m-d H:i:s'),
		);
		return $this->insert($data,true);
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
