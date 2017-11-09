<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 审批流程节点表
 **/
class table_pro_progress_nodes extends discuz_table
{
	public function __construct() {
		$this->_table = 'pro_progress_nodes';
		$this->_pk = 'pgnodeid';
		parent::__construct();
	}

	public function get_by_pk($id) 
	{
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE ".$this->_pk."='$id'";
        return DB::fetch_first($sql);
    }

    // 获取某个流程的第n个节点
    public function getNodeByOrder($pgid,$porder) 
    {
        $sql = "SELECT * FROM ".DB::table($this->_table)." WHERE pgid='$pgid' AND porder='$porder'";
        return DB::fetch_first($sql);
    }

    // 获取我的待办事项
    public function getMyActiveNodes()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
		$return = array(
            "totalProperty" => 0,
            "root" => array(),
        ); 
        $table_pro_progress_nodes = DB::table('pro_progress_nodes');
        $table_pro_progress = DB::table('pro_progress');
        $table_pro_user_organization =  DB::table('pro_user_organization');
		$sql = <<<EOF
SELECT SQL_CALC_FOUND_ROWS 
a.pgnodeid,a.node_name,a.can_skip,a.active_time,
b.module,b.module_id,b.origin_uid,b.progress_title,
c.realname,c.group_name
FROM $table_pro_progress_nodes as a 
LEFT JOIN $table_pro_progress as b ON a.pgid=b.pgid
LEFT JOIN $table_pro_user_organization as c ON b.origin_uid=c.uid
WHERE a.uid='$uid' AND a.is_active=1 AND b.status=1
ORDER BY a.active_time DESC
EOF;
        $return["root"] = DB::fetch_all($sql);
        $row = DB::fetch_first("SELECT FOUND_ROWS() AS total");
        $return["totalProperty"] = $row["total"];
        return $return;
    }/*}}}*/

    // 处理我的当前活跃的流程节点
    public function procMyActiveNode()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $pgnodeid = pro_validate::getNCParameter('pgnodeid','pgnodeid','integer');
        //1. 校验
        $record = $this->get_by_pk($pgnodeid);
        if ($record['uid']!=$uid) {
            throw new Exception('你无法处理此流程节点');
        }
        if ($record['is_active']!=1) {
            throw new Exception('该流程节点尚未激活');
        }
        //2. 更新记录
        $status = pro_validate::getNCParameter('status','status','integer');
        $feedback = pro_validate::getNCParameter('feedback','feedback','string',128);
        $data = array (
            'status' => $status,
            'feedback' => $feedback,
            'is_active' => 0,
            'done_time' => date('Y-m-d H:i:s'),
        );
        $this->update($pgnodeid,$data);
        //3. 审批驳回
        if ($status == PRO_AUDIT_FAIL) {
            C::t('#pro#pro_progress')->reject($record['pgid']);
            return;
        }
        //4. 审批通过或跳过
        if ($record['is_final']==1) {
            // 终态:审批流程通过
            C::t('#pro#pro_progress')->pass($record['pgid']);
        } else {
            // 否则:通知下一个审批节点
            $nextOrder = intval($record['porder'])+1;
            $nextNode = $this->activeNodeByOrder($record['pgid'],$nextOrder);
        }
    }/*}}}*/

    // 激活第n个节点
    private function activeNodeByOrder($pgid,$porder) 
    {/*{{{*/
        $pnode = $this->getNodeByOrder($pgid,$porder);
        if (!empty($pnode)) {
            $data = array (
                'is_active' => 1,
                'active_time' => date('Y-m-d H:i:s'),
            );
            $this->update($pnode['pgnodeid'],$data);
        }
    }/*}}}*/

	// 批量创建流程单的节点列表
	public function batchCreate($pgid,&$nodes) 
	{/*{{{*/
		if (empty($nodes)) return;
		$vals = array();
		$porder = 1;
		$n = count($nodes);
		$ctime = date('Y-m-d H:i:s');
		foreach ($nodes as &$node) {
			$node_name = $node['node_name'];
			$is_final = $n==$porder ? 1 : 0;
			$is_active = $porder == 1 ? 1 : 0;
			$can_skip = $node['can_skip'];
			$uid = $node['uid'];
			$status = PRO_AUDIT_TODO;
			$vals[] = "('$node_name','$pgid','$porder','$is_final','$is_active','$can_skip','$uid','$status','$ctime')";
			++$porder;
		}
		$sql = "INSERT INTO ".DB::table($this->_table)." ".
			   "(node_name,pgid,porder,is_final,is_active,can_skip,uid,status,ctime) VALUES ".
			   implode(',',$vals);
		DB::query($sql);
		return $affectRows;
	}/*}}}*/

}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
