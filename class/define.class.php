<?php
if(!defined('IN_DISCUZ')) {
    exit('Access Denied');
}

define('PRO_STATE_EDIT',   1);     //!< 编辑中
define('PRO_AUDIT_SUCC',   100);   //!< 审批通过
define('PRO_AUDIT_TODO',   101);   //!< 待审批
define('PRO_AUDIT_SKIP',   102);   //!< 审批跳过
define('PRO_AUDIT_FAIL',   103);   //!< 审批驳回
define('PRO_AUDIT_CANCEL', 110);   //!< 审批撤销

?>
