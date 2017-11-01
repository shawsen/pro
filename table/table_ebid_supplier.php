<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 供应商信息主表
 **/
class table_ebid_supplier extends discuz_table
{
    public function __construct() {
		$this->_table = 'ebid_supplier';
		$this->_pk = 'supplier_id';
		parent::__construct();
	}

	// 供应商列表查询
	public function query()
	{
		global $_G;
        $uid = $_G['uid'];
        $groupid = $_G['groupid'];
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        );
        $key    = ebid_validate::getNCParameter('key','key','string');
		$status = ebid_validate::getNCParameter('status','status','integer');
        $sort   = ebid_validate::getOPParameter('sort','sort','string',64,'mtime');
        $dir    = ebid_validate::getOPParameter('dir','dir','string',64,'DESC');
        $start  = ebid_validate::getOPParameter('start','start','integer',1024,0);
        $limit  = ebid_validate::getOPParameter('limit','limit','integer',1024,10);
        $where  = "a.isdel=0";
		if ($status!=-1) $where.=" AND a.status='$status'";
        if ($key != '') {
            $where .= " AND (a.company_name like '%$key%' OR a.company_short_name like '%$key%')";
        }   
		$table_this = DB::table($this->_table);
        $table_common_member = DB::table('common_member');
        $sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*,b.username
FROM $table_this as a
LEFT JOIN $table_common_member as b ON a.uid=b.uid
WHERE $where
ORDER BY $sort $dir
LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $res = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = intval($res['total']);
        return $return;
	}

	// 审核供应商
	function audit()
	{
		global $_G;
		$uid = $_G['uid'];
		$supplier_id = ebid_validate::getNCParameter('supplier_id','supplier_id','integer');
		$status = ebid_validate::getNCParameter('status','status','integer');
		if (!in_array($status,array(0,1,2))) {
			throw new Exception("status参数无效");
		}
		$reason = ebid_validate::getNCParameter('reason','reason','string',256);
		$data = array (
			'status' => $status,
			'audit_feadback' => $reason,
			'atime' => date('Y-m-d H:i:s'),
		);
		return $this->update($supplier_id,$data);
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
