define(function(require){
	/* jappengine.js, (c) 2016 mawentao */
	var urlmap=require('core/urlmap');

	// 注册controller配置(所有controller必须在此配置)
	var controller_confs = [
		require('controller/index').conf,			//!< 首页
		require('controller/requirectl').conf,		//!< 需求方管理后台
		require('controller/purcherctl').conf,		//!< 采购员管理后台

		require('controller/flow').conf,	//!< 流程
		require('controller/pr').conf,	    //!< PR单
		require('controller/po').conf,	    //!< PO单

		require('controller/help').conf,
		require('controller/login').conf,   //!< 登录模块
		require('controller/uc').conf       //!< 个人中心
	];

	var o={};
	o.start=function(){
		urlmap.start();
		for (var i=0;i<controller_confs.length;++i) {
			var conf=controller_confs[i];
			//1. 添加urlmap
			if (conf.controller) {
				urlmap.addmap("/"+conf.controller+"/index");
			}
			if (conf.path && conf.path.length>0) {
				for (var k=0;k<conf.path.length;++k) {
					urlmap.addmap(conf.path[k]);
				}
			}
			//2. 在frame中添加controller配置
			require('frame').addcontroller(conf);
		}
	};
	return o;
});
