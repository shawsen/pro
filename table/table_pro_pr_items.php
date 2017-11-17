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
		$this->_pk = 'item_id';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

    // 获取PR单所有有效项目
    public function queryItems()
    {/*{{{*/
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$prid  = pro_validate::getNCParameter('prid','prid','integer'); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'ctime');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'ASC');
        $where = "a.prid='$prid' AND isdel=0";
		$table_pro_pr_items = DB::table($this->_table);
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*
FROM $table_pro_pr_items as a
WHERE $where ORDER BY $sort $dir
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
    }/*}}}*/

	// 获取PR单所有有效项目
	public function getAllByPrid($prid)
	{/*{{{*/
        $where = "isdel=0 AND prid='$prid'";
        $table_this = DB::table($this->_table);
        $sql = "SELECT * FROM $table_this WHERE $where";
        return DB::fetch_all($sql);
	}/*}}}*/

	// 批量保存PR单的采购项列表
	public function saveBatch()
	{/*{{{*/
        $prid = pro_validate::getNCParameter('prid','prid','integer');
		//1. 获取采购项列表
		$items = $_POST['items'];
		if (empty($items)) {
			throw new Exception("采购项列表为空");
		}
		//2. 获取所有采购项(包括删除的)
		$table = DB::table($this->_table);
		$sql = "SELECT * FROM $table WHERE prid='$prid'";
		$savedList = DB::fetch_all($sql);
		DB::query("UPDATE $table SET isdel=1 WHERE prid='$prid'");
		//3. 借位保存
		$i=0;
		foreach ($items as &$row) {
			$row['prid'] = $prid;
			$row['isdel'] = 0;
			if (isset($savedList[$i])) {
				$item_id = $savedList[$i]['item_id'];
				$this->update($item_id,$row);
			} else {
				$this->insert($row);
			}
			++$i;
		}
		return count($items);
	}/*}}}*/

    // 保存单个采购项
    public function save()
	{/*{{{*/
        global $_G;
        $item_id = pro_validate::getNCParameter('item_id','item_id','integer');
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        $data = array (
            'prid'       => $prid,
            'prod_info'  => pro_validate::getNCParameter('prod_info','prod_info','string',100),
            'use_info'   => pro_validate::getNCParameter('use_info','use_info','string',100),
            'prod_brand' => pro_validate::getNCParameter('prod_brand','prod_brand','string',60),
            'prod_style' => pro_validate::getNCParameter('prod_style','prod_style','string',60),
            'item_unit'  => pro_validate::getNCParameter('item_unit','item_unit','string',60),
            'item_num'   => pro_validate::getNCParameter('item_num','item_num','number'),
            'item_unit_price' => pro_validate::getNCParameter('item_unit_price','item_unit_price','number'),
        );
        //1. 操作合法性检查
        $pr = C::t('#pro#pro_pr')->getEditablePR($prid);
        //2. 插入或更新
        if ($item_id==0) {
            $data['ctime'] = date('Y-m-d H:i:s');
            return $this->insert($data,true);
        } else {
            return $this->update($item_id,$data);
        }
	}/*}}}*/

    // 删除一个采购项
    public function removeItem()
	{/*{{{*/
        global $_G;
        $item_id = pro_validate::getNCParameter('item_id','item_id','integer');
        $data = array (
            'isdel' => 1,
        );
        return $this->update($item_id,$data);
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
