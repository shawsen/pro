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

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
