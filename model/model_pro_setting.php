<?php
if(!defined('IN_DISCUZ')) {
	exit('Access Denied');
}
/**
 * 插件设置 
 * C::m('#pro#pro_setting')->get()
 **/
class model_pro_setting
{
	// 获取默认配置
    public function getDefault($admin=false)
    {
		$pluginPath = pro_env::get_plugin_path();

		// 管理后台配置内容
		$setting = array (
			// 屏蔽所有discuz页面
			'disable_discuz' => 1,
			// 页面风格
			'page_style' => 'brass',
			// 版权
			'copyright' => '欧拓采购部',
			// favicon
			'favicon' => $pluginPath."/data/imgs/favicon.ico",
			// LOGO图片url
			'logourl' => $pluginPath."/data/imgs/logo.png",
			// Title
			'page_title' => '欧拓采购PRO系统',
		);

		return $setting;
    }

    // 获取配置
	public function get($admin=false)
	{
		$setting = $this->getDefault($admin);
		global $_G;
		if (isset($_G['setting']['pro_config'])){
			$config = unserialize($_G['setting']['pro_config']);
			foreach ($setting as $key => &$item) {
				if (isset($config[$key])) $item = $config[$key];
			}
		}
		return $setting;
	}
}
// vim600: sw=4 ts=4 fdm=marker syn=php
?>
