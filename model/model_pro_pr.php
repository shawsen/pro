<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * PR单
 **/
class model_pro_pr
{
    public function getPRDetail($prid)
    {
        global $_G;
        $uid = $_G['uid'];
        //1. PR主表信息 
        $pr = C::t('#pro#pro_pr')->get_by_pk($prid);
        if (empty($pr) || $pr['isdel']!=0) {
            throw new Exception('PR单不存在或已删除');
        }
        //2. PR项目列表
        $pr['items'] = C::t('#pro#pro_pr_items')->getAllByPrid($prid);
        return $pr;
    }
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
