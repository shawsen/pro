<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 审批流程模块
 **/
class model_pro_progress
{
	// 创建一个流程单
	public function create($module,$module_id,$title)
	{/*{{{*/
		global $_G;
		$uid = $_G['uid'];
		//1. 获取模块的流程节点模板
		$nodes = C::t('#pro#pro_progress_template')->getNodes($module);
		if (empty($nodes)) {
			throw new Exception('创建流程单失败:未定义流程节点');
		}
		//2. 创建流程单号
        C::t('#pro#pro_progress')->removeOldProgress($module,$module_id);  //!< 删除此模块关联的旧流程单
		$pgid = C::t('#pro#pro_progress')->create($module,$module_id,$title);
		if (!$pgid) {
			throw new Exception('创建流程单失败');
		}
		//3. 创建流程节点列表
		foreach ($nodes as &$node) {
			// 上级审批
			if ($node['utype']==1) {
				$userOrganization = C::m('#pro#pro_user_organization')->getByUid($uid);
				$node['uid'] = $userOrganization['supervisor_uid'];
				if (!$node['uid']) {
					throw new Exception('创建流程单失败::组织关系中找不到你的直接上级,请联系IT');
				}
			}
		}
		C::t('#pro#pro_progress_nodes')->batchCreate($pgid,$nodes);
        //4. 激活下一个节点
        $this->activeNextNode($pgid);
		//5. 返回流程单号
		return $pgid;
	}/*}}}*/

    // 激活流程下一个节点
    public function activeNextNode($pgid)
    {/*{{{*/
        $t_pro_progress_nodes = C::t('#pro#pro_progress_nodes');
        $nodes = $t_pro_progress_nodes->getNodesByPgid($pgid);
        foreach ($nodes as &$node) {
            if ($node['status']==PRO_AUDIT_TODO) {
                $pgnodeid = $node['pgnodeid'];
                $data = array (
                    'is_active' => 1,
                    'active_time' => date('Y-m-d H:i:s'),
                );
                $t_pro_progress_nodes->update($pgnodeid,$data);
                //TODO: 通知审批人
                return true;
            }
        }
    }/*}}}*/

    // 流程审批结束
    public function endAudit($pgid,$status,$feedback)
    {/*{{{*/
        //1. 获取pgid详情
        $t_pro_progress = C::t('#pro#pro_progress');
        $record = $t_pro_progress->get_by_pk($pgid);
        //2. 审批通过
        $data = array (
            'status' => $status,
        );
        $t_pro_progress->update($pgid,$data);
        //3. 流程关联的PRPO单
        C::t($record['module'])->audit($record['module_id'],$status,$feedback);
        //4. 通知流程发起人审批结果
        //TODO: ...        
    }/*}}}*/

	// 流程详情
	public function getDetail($pgid) 
	{
		$progressInfo = C::t('#pro#pro_progress')->get_by_pk($pgid);
		if (empty($progressInfo) || $progressInfo['module_id']==0) {
			throw new Exception('流程不存在或已删除');
		}
        // 获取流程节点列表
        $progressInfo['nodes'] = C::t('#pro#pro_progress_nodes')->getNodesByPgid($pgid);
        // 获取流程节点处理人的详细信息
        $uidmap = array();
        foreach ($progressInfo['nodes'] as &$node) {
            $uidmap[$node['uid']] = array();
        }
        $umap = C::t('#pro#pro_user_organization')->getUserMap($uidmap);
        foreach ($progressInfo['nodes'] as &$node) {
            $node['userinfo'] = $umap[$node['uid']];
        }

		return $progressInfo;
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
