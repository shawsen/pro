<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 供应商表
 **/
class table_pro_supplier extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_supplier';
		$this->_pk = 'supplier_id';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

    // 查询有效的供应商列表
    public function queryEffect()
	{/*{{{*/
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$key   = pro_validate::getNCParameter('key','key','string'); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'supplier_id');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'ASC');
        $start = pro_validate::getNCParameter('start','start','integer',1024,0);
        $limit = pro_validate::getNCParameter('limit','limit','integer',1024,20);
        $where = "a.isdel=0 AND a.status=0";
		if ($key!="") $where.=" AND (supplier_id='$supplier_id' OR company_name like '%$key%')";

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
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
