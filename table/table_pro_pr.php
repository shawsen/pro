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
        $where = "isdel=0 AND create_uid='$uid'";
		if ($key!="") $where.=" AND (prname like '%$key%')";
        if ($status!=-1) $where.=" AND status='$status'";
		$table_pro_pr = DB::table($this->_table);
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
        $status = 1;
        $data = array (
            'prname' => pro_validate::getNCParameter('prname','prname','string',128),
            'status' => $status,
            'create_uid' => $uid,
            'ctime' => date('Y-m-d H:i:s'),
            'isdel' => 0,
        );
        // log
        $prid = $this->insert($data,true);
        if (!$prid) {
            throw new Exception("创建PR单失败,请稍候再试");
        }
        $log = $_G['username']." 新建了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/

    // 提交PR单
    public function submit()
    {/*{{{*/
        global $_G;
        $uid = $_G['uid'];
        $prid = pro_validate::getNCParameter('prid','prid','integer');
        // op secure check
        $item = $this->get_by_pk($prid);
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PR单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能提交此PR单");
        }
        if ($item['status']!=1) {
            throw new Exception("此PR单已提交");
        }
        // op
        $status = 2;
        $data = array (
            'status' => $status,
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
        // op secure check
        $item = $this->get_by_pk($prid);
        if (empty($item) || $item['isdel']!=0) {
            throw new Exception("PR单不存在或已删除");
        }
        if ($item['create_uid']!=$uid) {
            throw new Exception("你不能撤销此PR单");
        }
        if ($item['status']!=2) {
            throw new Exception("此PR单现在不能撤销");
        }
        // op
        $status = 1;
        $data = array (
            'status' => $status,
        );
        $this->update($prid,$data);
        // log
        $log = $_G['username']." 撤销了PR单[$prid]";
        C::t('#pro#pro_pr_log')->write($prid,$status,$log);
        return $prid;
    }/*}}}*/
        
}

// vim600: sw=4 ts=4 fdm=marker syn=php
?>
