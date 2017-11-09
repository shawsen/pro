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
		//4. 返回流程单号
		return $pgid;
	}/*}}}*/

	// 流程详情
	public function getDetail($pgid) 
	{
		$progressInfo = C::t('#pro#pro_progress')->get_by_pk($pgid);
		if (empty($progressInfo) || $progressInfo['module_id']==0) {
			throw new Exception('流程不存在或已删除');
		}

		return $progressInfo;
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
