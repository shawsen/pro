<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PR单操作日志表
 **/
class table_pro_pr_log extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_pr_log';
		$this->_pk = 'logid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

	// 写日志
	public function write($prid,$status,$log)
	{/*{{{*/
		global $_G;
		$data = array(
            'prid' => $prid,
            'prstatus' => $status,
			'uid' => $_G['uid'],
			'client_ip' => $_G['clientip'],
			'log_content' => $log,
		);
		return $this->insert($data);
	}/*}}}*/
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
