define(function(require){
/**
 * 首页控制器
 **/
    var o={};
	var control='index';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/index'
		]
	};

    function permision_deny() {
        var code = '<div style="text-align:center;margin:50px auto;color:#FF8660;font-size:13px;">'+
            '<i class="icon icon-report" style="font-size:60px;padding:0;"></i>'+
            '<br><br>'+
            'Sorry! 您没有权限或权限被收回，请联系IT管理员'+
        '</div>'+
        require('common/copyright').get();
		frame.showpage(code);
    }


	// 默认action
	o.indexAction=function() {
        switch (parseInt(dz.auth)) {
            case 1: window.location="#/requirectl"; break;
            case 2: window.location="#/purcherctl"; break;
            default: permision_deny(); break;
        }
	};

	return o;
});
