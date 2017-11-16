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

	// 我发起的流程
	public function queryMine()
	{/*{{{*/
		global $_G;
        $uid = $_G['uid'];
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$key   = pro_validate::getNCParameter('key','key','string'); 
		$status= pro_validate::getOPParameter('status','status','integer',1024,-1); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'ctime');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start = pro_validate::getOPParameter('start','start','integer',1024,0);
        $limit = pro_validate::getOPParameter('limit','limit','integer',1024,20);
        $where = "origin_uid='$uid'";
		if ($key!="") $where.=" AND (progress_title like '%$key%')";
        if ($status!='-1') $where.=" AND a.status='$status'";
		$table = DB::table($this->_table);
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*
FROM $table as a
WHERE $where ORDER BY $sort $dir LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
	}/*}}}*/

    // 删除模块关联的全部流程单
    public function removeOldProgress($module,$module_id)
    {/*{{{*/
        $sql = "UPDATE ".DB::table($this->_table)." SET isdel=1 ".
               "WHERE module='$module' AND module_id='$module_id' AND isdel=0";
        DB::query($sql);
    }/*}}}*/

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

    // 撤销流程
    public function cancel($pgid)
    {/*{{{*/
        global $_G;
        $data = array (
            'status' => PRO_AUDIT_CANCEL,
        );
        return $this->update($pgid,$data);
    }/*}}}*/

    // 驳回流程
    public function reject($pgid,$feedback)
    {/*{{{*/
        $record = $this->get_by_pk($pgid);
        $data = array (
            'status' => PRO_AUDIT_FAIL,
        );
        $this->update($data);
        C::t($record['module'])->reject($record['module_id'],$feedback);   //!< 调用具体单的驳回接口
    }/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
