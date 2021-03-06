<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PR单表
 **/
class table_pro_pr extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_pr';
		$this->_pk = 'prid';
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
				$row['prid_code'] = C::m('#pro#pro_authcode')->encodeID($row['prid']);
			}
		}
		///////////////////////////////////////////
        return $return;
	}/*}}}*/

    // 查询审批通过的PR单
    public function queryAuditSucc()
	{/*{{{*/
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
		$key   = pro_validate::getNCParameter('key','key','string'); 
        $sort  = pro_validate::getOPParameter('sort','sort','string',1024,'ctime');
        $dir   = pro_validate::getOPParameter('dir','dir','string',1024,'DESC');
        $start = pro_validate::getNCParameter('start','start','integer',1024,0);
        $limit = pro_validate::getNCParameter('limit','limit','integer',1024,20);
        $status = PRO_AUDIT_SUCC;
        $where = "a.isdel=0 AND a.status='$status'";
		if ($key!="") $where.=" AND (prid='$key' OR prname like '%$key%')";

		$table_pro_pr = DB::table($this->_table);
        $sort = "a.$sort";
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS a.*
FROM $table_pro_pr as a
WHERE $where ORDER BY $sort $dir LIMIT $start,$limit
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
	}/*}}}*/

    // 创建PR单
    public function create()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        //1. 避免创建多个PR单
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE create_uid='$uid' AND status=0";
        $item = DB::fetch_first($sql);
        if (!empty($item)) {
            return C::m('#pro#pro_authcode')->encodeID($item['prid']);
        }
        //2. 创建PR单
        $username = $_G['username'];
        $prname = $username."采购申请单".date('YmdHi');
        $data = array (
            'prname'     => $prname,
            'status'     => 0,
            'create_uid' => $uid,
            'ctime'      => date('Y-m-d H:i:s'),
            'isdel'      => 0,
        );
        // log
        $prid = $this->insert($data,true);
        if (!$prid) {
            throw new Exception("创建PR单失败,请稍候再试");
        }
        $log = $_G['username']." 新建了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return C::m('#pro#pro_authcode')->encodeID($prid);
    }/*}}}*/

    // 获取可编辑的PR单记录
    public function getEditablePR($prid)
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $item = $this->get_by_pk($prid);
        $status = $item['status'];
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PR单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能编辑此PR单");
        }
        $unedits = array(PRO_AUDIT_SUCC,PRO_AUDIT_TODO);
        if (in_array($item['status'],$unedits)) {
            throw new Exception("此PR单已提交");
        }
        ////////////////////////////////////////////////////////////
        // 编辑过的PR单进入编辑状态
        if ($item['status']==0) {
            $this->update($prid,array('status'=>PRO_STATE_EDIT));
        }
        ////////////////////////////////////////////////////////////
        return $item;
    }/*}}}*/

	// 保存PR单
	public function save()
    {/*{{{*/
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        $pr = $this->getEditablePR($prid);
        $status = $pr['status'];
        // log
        $log = $_G['username']." 保存了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/

    // 提交PR单
    public function submit()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        //1. 操作合法性校验
        $pr = $this->getEditablePR($prid);  //!< 处于编辑状态的PR单才可提交
        //2. 统计PR单总价
        $items = C::t('#pro#pro_pr_items')->getAllByPrid($prid);
        if (empty($items)) {
            throw new Exception("此PR单的采购项为空");
        }
        $totalPrice = 0;
        foreach ($items as &$item) {
            $totalPrice += floatval($item['item_unit_price']) * floatval($item['item_num']);
        }
        //3. 创建审批流程
		$title = "PR单[$prid]审批流程_".$_G['username'];
		$pgid = C::m('#pro#pro_progress')->create('#pro#pro_pr',$prid,$title);
		//3. 更改状态
        $status = PRO_AUDIT_TODO;
        $data = array (
			'pgid'        => $pgid,
            'total_price' => $totalPrice,
            'status'      => $status,
            'submit_time' => date('Y-m-d H:i:s'),
        );
        $this->update($prid,$data);
        // log
        $log = $_G['username']." 提交了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/

    // 撤销PR单
    public function submitCancel()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        //1. 操作合法性校验
        $item = $this->get_by_pk($prid);
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
        $this->update($prid,$data);
        //4. log
        $log = $_G['username']." 撤销了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/

    // 删除PR单
    public function remove()
    {/*{{{*/
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        $pr = $this->getEditablePR($prid);  //!< 处于编辑状态的PR单才可删除
        $data = array (
            'isdel' => 1
        );
        $this->update($prid,$data);
        // log
        $log = $_G['username']." 删除了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/


    // 审批PR单
    public function audit($prid,$status,$feedback)
    {/*{{{*/
        $data = array (
            'status' => $status,
        );
        $this->update($prid,$data);
        $log = "$feedback";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
    }/*}}}*/
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
