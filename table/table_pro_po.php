<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PO单表
 **/
class table_pro_po extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_po';
		$this->_pk = 'poid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }


	// 创建者查询接口
	public function queryByCreator()
	{/*{{{*/
        global $_G;
        $uid = $_G['uid'];
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$key   = pro_validate::getNCParameter('key','key','string'); 
        $status= pro_validate::getNCParameter('status','status','integer');
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'ctime');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start = pro_validate::getNCParameter('start','start','integer',1024,0);
        $limit = pro_validate::getNCParameter('limit','limit','integer',1024,20);
        $where = "a.isdel=0 AND a.create_uid='$uid'";
		if ($key!="") $where.=" AND (prname like '%$key%')";
        $ss = array();
        switch ($status) {
            case 0: $ss=array(0,PRO_STATE_EDIT,PRO_AUDIT_SKIP,PRO_AUDIT_FAIL,PRO_AUDIT_CANCEL); break;
            case 1: $ss=array(PRO_AUDIT_TODO); break;
            case 2: $ss=array(PRO_AUDIT_SUCC); break;
        }
        if (!empty($ss)) {
            $where.=" AND a.status IN (".implode(',',$ss).")";
        }
		$table_pro_pr = DB::table($this->_table);
        $table_pro_progress = DB::table('pro_progress');
        $sort = "a.$sort";
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*,b.progress_title
FROM $table_pro_pr as a
LEFT JOIN $table_pro_progress as b ON a.pgid=b.pgid
WHERE $where ORDER BY $sort $dir LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
		///////////////////////////////////////////
		// id encode
		if (!empty($return["root"])) {
			foreach ($return["root"] as &$row) {
				$row['poid_code'] = C::m('#pro#pro_authcode')->encodeID($row['poid']);
			}
		}
		///////////////////////////////////////////
        return $return;
	}/*}}}*/

    // 创建PO单
    public function create()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        //1. 避免创建多个PO单
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE create_uid='$uid' AND status=0";
        $item = DB::fetch_first($sql);
        if (!empty($item)) {
            return C::m('#pro#pro_authcode')->encodeID($item['poid']);
        }
        //2. 创建PO单
        $username = $_G['username'];
        $poname = $username."采购订单".date('YmdHi');
        $data = array (
            'poname'     => $poname,
            'status'     => 0,
            'create_uid' => $uid,
            'ctime'      => date('Y-m-d H:i:s'),
            'isdel'      => 0,
        );
        $poid = $this->insert($data,true);
        if (!$poid) {
            throw new Exception("创建PO单失败,请稍候再试");
        }
        /*
        $log = $_G['username']." 新建了PR单[$poid]";
        C::t('#pro#pro_po_log')->write($poid,$status,$log); */
        return C::m('#pro#pro_authcode')->encodeID($poid);
    }/*}}}*/

    // 获取可编辑的PO单记录
    public function getEditablePO($poid)
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $item = $this->get_by_pk($poid);
        $status = $item['status'];
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PO单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能保存此PO单");
        }
        $unedits = array(PRO_AUDIT_SUCC,PRO_AUDIT_TODO);
        if (in_array($item['status'],$unedits)) {
            throw new Exception("此PO单已提交，不可编辑");
        }
        return $item;
    }/*}}}*/

    // 编辑检查
    public function editCheck($poid)
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $item = $this->get_by_pk($poid);
        $status = $item['status'];
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PO单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能保存此PO单");
        }
        $unedits = array(PRO_AUDIT_SUCC,PRO_AUDIT_TODO);
        if (in_array($item['status'],$unedits)) {
            throw new Exception("此PO单已提交，不可编辑");
        }
        return $item;
    }/*}}}*/

    // 关联PR单
    public function setPrid()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        // op secure check
        $item = $this->get_by_pk($poid);
        $status = $item['status'];
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PO单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能保存此PR单");
        }
        $unedits = array(PRO_AUDIT_SUCC,PRO_AUDIT_TODO);
        if (in_array($item['status'],$unedits)) {
            throw new Exception("此PO单已提交，不可编辑");
        }
        // op
        if ($status==0) $status = PRO_STATE_EDIT;
        $data = array (
            'prid'   => $prid,
            'status' => $status,
        );
        $this->update($poid,$data);
/*
        // log
        $log = $_G['username']." 保存了PR单[$poid]";
        C::t('#pro#pro_po_log')->write($poid,$status,$log);
*/
        return $poid;
    }/*}}}*/

    // 设置供应商
    public function setSupplier()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        $supplier_id = pro_validate::getNCParameter('supplier_id','supplier_id','integer');
        $item = $this->editCheck($poid);
        $status = $item['status'];
        if ($status==0) $status = PRO_STATE_EDIT;
        $data = array (
            'supplier_id' => $supplier_id,
            'status' => $status,
        );
        return $this->update($poid,$data);
    }/*}}}*/

	// 保存PO单
	public function save($poid,$data)
    {/*{{{*/
        $item = $this->editCheck($poid);
        if ($item['status']==0) {
            $data['status'] = PRO_STATE_EDIT;
        }
        return $this->update($poid,$data);
    }/*}}}*/

    // 提交PO单
    public function submit()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        //1. 操作合法性校验
        $item = $this->getEditablePR($poid);  //!< 处于编辑状态的PO单才可提交
        $items = C::t('#pro#pro_po_items')->getAllByPoid($poid);
        if (empty($items)) {
            throw new Exception("此PR单的采购项为空");
        }
        //2. 创建审批流程
		//$uo = C::m('#pro#pro_user_organization')->getByUid($uid);
		$title = "PR单[$poid]审批流程";
		$pgid = C::m('#pro#pro_progress')->create('#pro#pro_po',$poid,$title);
		//3. 更改状态
        $status = PRO_AUDIT_TODO;
        $data = array (
			'pgid'        => $pgid,
            'status'      => $status,
            'submit_time' => date('Y-m-d H:i:s'),
        );
        $this->update($poid,$data);
        // log
        $log = $_G['username']." 提交了PR单[$poid]";
        C::t('#pro#pro_po_log')->write($poid,$status,$log);
        return $poid;
    }/*}}}*/

    // 撤销PR单
    public function submitCancel()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        //1. 操作合法性校验
        $item = $this->get_by_pk($poid);
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PR单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能撤销此PR单");
        }
        if ($item['status']!=PRO_AUDIT_TODO) {
            throw new Exception("此PR单现在不能撤销");
        }
        //2. 撤销流程
        if ($item['pgid']) {
            C::t('#pro#pro_progress')->cancel($item['pgid']);
        }
        //3. 撤销
        $data = array (
            'status' => PRO_AUDIT_CANCEL,
        );
        $this->update($poid,$data);
        //4. log
        $log = $_G['username']." 撤销了PR单[$poid]";
        C::t('#pro#pro_po_log')->write($poid,$status,$log);
        return $poid;
    }/*}}}*/
        
    // 审批PO单
    public function audit($poid,$status,$feedback)
    {/*{{{*/
        $data = array (
            'status' => $status,
        );
        $this->update($poid,$data);
        //$log = "$feedback";
        //C::t('#pro#pro_po_log')->write($poid,$status,$log);
    }/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
