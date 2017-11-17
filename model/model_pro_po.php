<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PO单
 **/
class model_pro_po
{
    // 获取PO单详情
    public function getPODetail($poid)
    {
        global $_G;
        $uid = $_G['uid'];
        //1. PO主表信息 
        $po = C::t('#pro#pro_po')->get_by_pk($poid);
        if (empty($po) || $po['isdel']!=0) {
            throw new Exception('PO单不存在或已删除');
        }
        $po['poid_code'] = C::m('#pro#pro_authcode')->encodeID($poid);
        //2. 创建者信息
        $po['creatorInfo'] = C::t('#pro#pro_user_organization')->getUserOrganization($po['create_uid']);
        return $po;
    }

    // 提交PO单
    public function submit()
    {
        global $_G;
        $uid = $_G['uid'];
        $poid = pro_validate::getNCParameter('poid','poid','integer');
        //1. 操作合法性校验
        $t_pro_po = C::t('#pro#pro_po');
        $item = $t_pro_po->get_by_pk($poid);
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PO单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能提交此PO单");
        }
        $canSubStates = array(0,PRO_STATE_EDIT,PRO_AUDIT_FAIL,PRO_AUDIT_FAIL);
        if (!in_array($item['status'],$canSubStates)) {
            throw new Exception("此PO单已提交");
        }
        if ($item['supplier_id']==0) {
            throw new Exception("此PO单未指定供应商");
        }
        if ($item['addrid']==0) {
            throw new Exception("此PO单未指定送货地址");
        }
        if ($item['arrival_date']=='0000-00-00') {
            throw new Exception("此PO单未指定送货日期");
        }
        //2. 检查采购列表
        $items = C::t('#pro#pro_po_items')->queryItems();
        if (empty($items['root'])) {
            throw new Exception("此PO单的采购列表为空");
        }
		$totalPrice = 0;
        foreach ($items as &$item) {
            $totalPrice += floatval($item['item_unit_price']) * floatval($item['item_num']);
        }
        //3. 创建审批流程
		$title = "PO单[$poid]审批流程_".$_G['username'];
		$pgid  = C::m('#pro#pro_progress')->create('#pro#pro_po',$poid,$title);
		//3. 更改状态
        $status = PRO_AUDIT_TODO;
        $data = array (
			'pgid'        => $pgid,
            'status'      => $status,
			'price_total' => $totalPrice,
            'submit_time' => date('Y-m-d H:i:s'),
        );
        $t_pro_po->update($poid,$data);
//        // log
//        $log = $_G['username']." 提交了PR单[$prid]";
//        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $poid;
    }    

}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
