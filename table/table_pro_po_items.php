<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PO单采购项目表
 **/
class table_pro_po_items extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_po_items';
		$this->_pk = 'item_id';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

    // 导入关联的PR单的采购列表
    public function syncPrItems()
    {/*{{{*/
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        $poItem = C::t('#pro#pro_po')->editCheck($poid);
        $prid = $poItem['prid'];
        $prItems = C::t('#pro#pro_pr_items')->getAllByPrid($prid);
        if (empty($prItems)) {
            throw new Exception("关联的PR单的采购列表为空");
        }
        // 
        $poItems = array();
        $ctime = date('Y-m-d H:i:s');
        foreach ($prItems as &$item) {
            $code = "('$poid',".
                "'".$item['item_id']."',".
                "'".$item['prod_info']."',".
                "'".$item['use_info']."',".
                "'".$item['prod_brand']."',".
                "'".$item['prod_style']."',".
                "'".$item['item_unit']."',".
                "'".$item['item_num']."',".
                "'".$item['item_unit_price']."',".
                "'$ctime',".
                "0".
            ")";
            $poItems[] = $code;
        }
        $table = DB::table($this->_table);
        $sql = "INSERT INTO $table ".
                "(poid,pr_item_id,prod_info,use_info,prod_brand,prod_style,item_unit,item_num,item_unit_price,ctime,isdel) values ".
                implode(",",$poItems)." ON DUPLICATE KEY UPDATE ".
                "isdel=values(isdel)";
        return DB::query($sql);
    }/*}}}*/

    // 获取PO单所有有效项目
    public function queryItems()
    {/*{{{*/
        $return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$poid  = pro_validate::getNCParameter('poid','poid','integer'); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'ctime');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'ASC');
        $where = "a.poid='$poid' AND isdel=0";
		$table_pro_po_items = DB::table($this->_table);
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*
FROM $table_pro_po_items as a
WHERE $where ORDER BY $sort $dir
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
    }/*}}}*/

	// 获取PO单所有有效项目
	public function getAllByPoid($poid)
	{/*{{{*/
        $where = "isdel=0 AND poid='$prid'";
        $table_this = DB::table($this->_table);
        $sql = "SELECT * FROM $table_this WHERE $where";
        return DB::fetch_all($sql);
	}/*}}}*/


    // 保存单个采购项
    public function saveItem()
	{/*{{{*/
        global $_G;
        $item_id = pro_validate::getNCParameter('item_id','item_id','integer');
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        //1. 操作合法性检查
        $poItem = C::t('#pro#pro_po')->editCheck($poid);
        //2. 插入或更新
        $data = array (
            'poid' => $poid,
            'prod_info' => pro_validate::getNCParameter('prod_info','prod_info','string',100),
            'use_info' => pro_validate::getNCParameter('use_info','use_info','string',100),
            'prod_brand' => pro_validate::getNCParameter('prod_brand','prod_brand','string',60),
            'prod_style' => pro_validate::getNCParameter('prod_style','prod_style','string',60),
            'item_unit' => pro_validate::getNCParameter('item_unit','item_unit','string',60),
            'item_num' => pro_validate::getNCParameter('item_num','item_num','number'),
            'item_unit_price' => pro_validate::getNCParameter('item_unit_price','item_unit_price','number'),
        );
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
        $record = $this->get_by_pk($item_id);
        $poItem = C::t('#pro#pro_po')->editCheck($record['poid']);
        $data = array (
            'isdel' => 1,
        );
        return $this->update($item_id,$data);
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
