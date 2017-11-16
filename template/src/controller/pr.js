define(function(require){
	/**
     * PR单模块控制器
    **/
    var o={};
	var control='pr';

	o.conf = {
		controller: control,
		path: [
			'/'+control+'/form',
			'/'+control+'/myprs',
			'/'+control+'/index'
		]
	};

	function before_action() {
		require('./login').check();
		if (dz.auth==0) {
			frame.showpage("没有权限");
			throw new Error("没有权限");
		}
	}

	// 默认action
	o.indexAction=function(erurl) {
        window.location = '#/'+control+'/form';
/*
		var query=erurl.getQuery();
        var f = query['f'] ? query['f'] : '';
		if (f=='') window.location="#/";
        var code = '<div id="flow_detail_div" class="mwt-layout-fill"></div>';
		frame.showpage(code);
		require('view/flow/detail/page').execute('flow_detail_div',f);
*/
	};

    // PR单详情
    o.formAction=function(erurl) {
        before_action();
        var query=erurl.getQuery();
        var prid=query.prid ? query.prid : '';
        if (prid=='') {
            // 创建PR单
            msg.showLoading('frame-center','正在创建PR单...');
            setTimeout(function(){
                ajax.post('pr&action=create',{},function(res){
                    if (res.retcode!=0) {
                        msg.showException('frame-center',res.retmsg);
                    } else {
                        window.location = '#/pr/form~prid='+res.data;
                    }
                }); 
            },1000);
            return;
        }
        jQuery('#frame-center').css({background:'#fff'});
        require('form/pr/main').create({
            render : 'frame-center',
            formid : prid,
            print  : true,
            edit   : true
        }).init();
    };

    // 我的PR单
	o.myprsAction=function(erurl) {
		before_action();
        var query=erurl.getQuery();
        var s = query.s ? query.s : 0;
        var code = '<div class="wp">'+
            posnav.get([{name:'我的PR单'}])+
            '<div id="myprs_div" class="wall"></div>'+
        '</div>'+
        require('common/copyright').get();
        frame.showpage(code);
		require('view/pr/myprs/page').execute('myprs_div',s);
	};


	return o;
});
