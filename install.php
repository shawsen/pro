<?php
/*******************************************************
 * 此脚本文件用于插件的安装
 * 提示：可使用runquery() 函数执行SQL语句
 *       表名可以直接写“cdb_”
 * 注意：需在导出的 XML 文件结尾加上此脚本的文件名
 *******************************************************/
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}

require_once "class/env.class.php";


$curpath = dirname(__FILE__);
$addtime = $modtime = date('Y-m-d H:i:s');
$pluginpath = pro_env::get_plugin_path();

/*
$table = DB::table('pro_log');
$sql = "CREATE TABLE IF NOT EXISTS $table ". <<<EOF
(
`logid` bigint unsigned NOT NULL AUTO_INCREMENT COMMENT '日志ID(自增主键)',
`logtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP COMMENT '日志时间',
`uid` mediumint(8) unsigned NOT NULL DEFAULT '0' COMMENT '用户ID',
`client_ip` varchar(32) NOT NULL DEFAULT '' COMMENT '来访IP',
`log_content` varchar(4096) NOT NULL DEFAULT '' COMMENT '日志内容',
PRIMARY KEY (`logid`),
KEY `idx_logtime_uid` (`logtime`,`uid`)
) ENGINE=InnoDB
EOF;
//runquery($sql);
//runquery("ALTER TABLE `$table` ENGINE=INNODB");


// 采购系统配置表
$table = DB::table('pro_config');
$sql = "CREATE TABLE IF NOT EXISTS $table ". <<<EOF
(
`k` varchar(64) NOT NULL DEFAULT '' COMMENT '配置Key',
`v` varchar(4096) NOT NULL DEFAULT '' COMMENT '配置值',
`muid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '操作用户',
`mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更改时间',
PRIMARY KEY (`k`)
) ENGINE=MyISAM COMMENT '采购系统配置表'
EOF;
runquery($sql);
$sql = "INSERT IGNORE INTO $table (k,v,muid) VALUES ".<<<EOF
('introduction_img','$pluginpath/data/imgs/introduction.png',1),
('introduction_zh','公司中文简介',1),
('introduction_en','English Introduction',1),

('contact_siteurl','http://www.yourcompany.com/',1),
('contact_tel','021-12345678',1),
('contact_email','yourcompany@null.com',1)
EOF;
runquery($sql);

// 供应商主表
$table = DB::table('pro_supplier');
$sql = "CREATE TABLE IF NOT EXISTS $table ". <<<EOF
(
`supplier_id` bigint(20) unsigned NOT NULL AUTO_INCREMENT COMMENT '供应商ID',
`company_name` varchar(64) NOT NULL DEFAULT "" COMMENT "公司全称",
`company_short_name` varchar(64) NOT NULL DEFAULT "" COMMENT "公司简称", 
`company_address` varchar(128) NOT NULL DEFAULT "" COMMENT "公司地址",
`company_post_code` varchar(16) NOT NULL DEFAULT "" COMMENT "邮政编码",
`company_tel` varchar(32) NOT NULL DEFAULT "" COMMENT "公司电话",
`company_fax` varchar(32) NOT NULL DEFAULT "" COMMENT "公司传真",
`company_bank_name` varchar(32) NOT NULL DEFAULT "" COMMENT "开户银行",
`company_bank_account` varchar(32) NOT NULL DEFAULT "" COMMENT "银行帐号",
`company_certnum` varchar(64) NOT NULL DEFAULT "" COMMENT "税务登记证号[三证合一]",
`company_regist_capital` varchar(32) NOT NULL DEFAULT "" COMMENT "注册资金", 
`company_real_capital` varchar(32) NOT NULL DEFAULT "" COMMENT "固定资产",
`company_found_time` varchar(32) NOT NULL DEFAULT "" COMMENT "成立时间",
`company_website` varchar(256) NOT NULL DEFAULT "" COMMENT "公司网站",
`company_faren` varchar(64) NOT NULL DEFAULT "" COMMENT "公司法人代表",
`company_faren_tel` varchar(64) NOT NULL DEFAULT "" COMMENT "公司法人联系方式",
`company_introduction` varchar(4096) NOT NULL DEFAULT "" COMMENT "公司介绍",
`company_business` varchar(4096) NOT NULL DEFAULT "" COMMENT "公司营业范围",
`status` tinyint unsigned NOT NULL DEFAULT '1' COMMENT '状态(0:审核通过,1:待审核,2:审核拒绝)',
`uid` mediumint(8) UNSIGNED NOT NULL DEFAULT '0' COMMENT '用户uid',
`audit_feadback` varchar(256) NOT NULL DEFAULT "" COMMENT "审核反馈",
`atime` datetime NOT NULL DEFAULT "0000-00-00 00:00:00" comment '提审or审核日期',
`ctime` datetime NOT NULL DEFAULT "0000-00-00 00:00:00" comment '创建日期',
`mtime` timestamp NOT NULL DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP COMMENT '更改时间',
`isdel` tinyint(1) unsigned NOT NULL DEFAULT '0' COMMENT '删除标志(0:未删,1:已删)',
PRIMARY KEY (`supplier_id`),
KEY `idx_uid` (`uid`),
KEY `idx_status_isdel_company_name` (`status`,`isdel`,`company_name`)
) ENGINE=MyISAM AUTO_INCREMENT=100001 COMMENT '供应商主表'
EOF;
runquery($sql);
$sql = "INSERT IGNORE INTO $table (supplier_id,company_name,company_short_name,company_address,company_post_code,company_tel,company_fax,company_bank_name,company_bank_account,company_certnum,company_regist_capital,company_real_capital,company_found_time,company_website,company_faren,company_faren_tel,company_introduction,company_business,status,uid,audit_feadback,atime,ctime) VALUES ".<<<EOF
(100001,'上海逸森电子技术有限公司','逸森电子',
'上海市九亭镇九新公路339号1幢2楼-059','','','',
'','',
'310227001413156','50万元','','2009年01月20日',
'http://sh.qiyexinyong.org/corp-310227001413156.html','王宝兴','',
'上海逸森电子技术有限公司是一家新兴的信息技术服务公司，我们专注于企业客户的产品销售、解决方案、系统集成、并提供从维护型到顾问型的系列化专业服务。',
'电子技术领域内的技术开发、技术服务；计算机软硬件（除计算机信息系统安全专用产品）、通讯器材、电子产品、办公自动化设备、家用电器、汽摩配件、建筑装潢材料（除危险品）、化工产品（除危险化学品、监控化学品、烟花爆竹、民用爆炸物品、易制毒化学品）批发零售；商务信息咨询；建筑装潢设计；设计、制作、利用自有媒体发布各类广告。 【企业经营涉及行政许可的，凭许可证件经营】',
1,
1,'','$addtime','$addtime'
)
EOF;
runquery($sql);
*/


$finish = TRUE;
?>
