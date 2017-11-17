<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 邮件模块
 * C::m('#pro#pro_mail')->get()
 **/
class model_pro_mail
{
	private $cfgkey = "pro_mail";

	// 发送邮件
	public function send_email($tomail,$title,$content)
	{
        $title.="<系统自动发送，请勿直接回复>";
        $content = "<style>* {font-size:14px;font-family:'HeiTi';}</style>".
            $content.
            "<br><br><br><系统自动发送，请勿直接回复>";
        $log = "mailto:$tomail [$title]";
        try {
            include_once libfile('function/mail');
            $res = sendmail($tomail,$title,$content);
            if (!$res) throw new Exception('send fail');
            $log.=" [send success]";
            pro_env::getlog()->notice($log);
            return true;
        } catch (Exception $e) {
            $log.=' ['.$e->getMessage().']';
            pro_env::getlog()->warning($log);
            return false;
        }
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
