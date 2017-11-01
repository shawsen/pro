define(function(require){
/**
 * 供应商管理后台控制器
 **/
    var o={};
	var control='supplierctl';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/index',
			'/'+control+'/profile',
		],
		// 左部菜单
		menu: [
			{name:'我的资料', icon:'sicon-grid', submenu:[
				{name:'我的资料',icon:'icon icon-log',action:'index'},
				{name:'修改密码',icon:'icon icon-lock',action:'changepass'}
			]},
			{name:'采购项目', icon:'sicon-grid', submenu:[
				{name:'新建项目',icon:'icon icon-log',action:'profile'},
				{name:'项目列表',icon:'icon icon-log',action:'profile'},
				{name:'进度管理',icon:'icon icon-lock',action:'changepass'}
			]},
			{name:'供应商管理', icon:'sicon-grid', submenu:[
				{name:'供应商列表',icon:'icon icon-log',action:'profile'},
				{name:'供应商审核',icon:'icon icon-lock',action:'changepass'}
			]},
			{name:'统计报表', icon:'sicon-grid', submenu:[
				{name:'项目报表',icon:'icon icon-log',action:'profile'},
				{name:'供应商报表',icon:'icon icon-lock',action:'changepass'}
			]}
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.groupid<2) {
			frame.showException("没有权限");
			throw new Error("不是供应商");
		}
	}

	// 默认action
	o.indexAction=function() {
		before_action();
		frame.showpage('welcome');
	};

	return o;
});
