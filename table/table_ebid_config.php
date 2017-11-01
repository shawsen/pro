<?php
if (!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 采购员系统配置表
 **/
class table_ebid_config extends discuz_table
{
    public function __construct() {
		$this->_table = 'ebid_config';
		$this->_pk = 'k';
		parent::__construct();
	}

	// 获取全部配置数据
	public function get_all_map()
	{
		$sql = "SELECT k,v FROM ".DB::table($this->_table);
		$arr = DB::fetch_all($sql);
		$map = array();
		foreach ($arr as &$row) {
			$map[$row['k']] = $row['v'];
		}
		return $map;
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
