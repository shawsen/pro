define(function(require){
/**
 * 需求方控制器
 **/
	var posnav = require('common/posnav');
    var o={};
	var control='requirectl';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/pr',
			'/'+control+'/myflow',
			'/'+control+'/myflowtodo',
			'/'+control+'/myprs',
			'/'+control+'/index'
		],
		// 左部菜单
		menu: [
			{name:'流程', icon:'icon icon-reply', submenu:[
				{name:'我发起的流程',icon:'icon icon-reply',action:'myflow'},
				{name:'待处理的流程',icon:'icon icon-reply',action:'myflowtodo'}
			]},
			{name:'PR单', icon:'icon icon-reply', submenu:[
				{name:'我的PR单',icon:'icon icon-reply',action:'myprs'}
			]}
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.auth!=1) {
			frame.showpage("没有权限");
			throw new Error("没有权限");
		}
	}

	// 默认action
	o.indexAction=function() {
        window.location = '#/'+control+'/myprs';
	};

	// 我发起的流程
	o.myflowAction=function(erurl) {
		before_action();
        var code = '<div id="myflow_div" class="ctlgrid" style="top:10px;">我发起的流程</div>';
		frame.showpage(code);
		require('view/myflow/page').execute('myflow_div');
	};


	// 我的PR单
	o.myprsAction=function(erurl) {
		before_action();
        var query=erurl.getQuery();
        var prstate = query['s'] ? query['s'] : 1;
        var code = '<div id="requirectl_myprs_div" class="ctlgrid" style="top:10px;"></div>';
		frame.showpage(code);
		require('view/requirectl/myprs/page').execute('requirectl_myprs_div',prstate);
	};

	// PR单
	o.prAction=function(erurl) {
		before_action();
        var query=erurl.getQuery();
        var prid=query.prid ? query.prid : '';
        if (prid=='') {
            window.location='#/'+control+'/myprs';
            return;
        }
        require('form/pr/main').create({
            render : 'frame-center',
            formid : prid,
            print  : false
        }).init();
/*
		var code = posnav.get('PR单')+
			'<div id="requirectl_newpr_div" class="ctlgrid"></div>';
		frame.showpage(code);
//		require('view/requirectl/prform/page').execute('requirectl_newpr_div');
*/
	};

	return o;
});
