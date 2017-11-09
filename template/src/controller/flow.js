define(function(require){
	/**
     * 流程模块控制器
    **/
	var posnav = require('common/posnav');
    var o={};
	var control='flow';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/index'
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
	o.indexAction=function(erurl) {
		var query=erurl.getQuery();
        var f = query['f'] ? query['f'] : '';
		if (f=='') window.location="#/";
        var code = '<div id="flow_detail_div" class="ctlgrid" style="top:10px;"></div>';
		frame.showpage(code);
		require('view/flow/detail/page').execute('flow_detail_div',f);
	};

	return o;
});
