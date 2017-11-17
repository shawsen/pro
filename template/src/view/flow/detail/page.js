define(function(require){
	/* 流程详情页 */
    var o={};
	var did;

	o.execute=function(domid,f){
		did = 'main-'+domid;
		var code = '<div class="flowdiv" id="'+did+'"></div>'+
		require('common/copyright').get();
		jQuery('#'+domid).html(code).css({'background':'#eee'});
		ajax.post('progress&action=detail',{pgid:f},function(res){
			if (res.retcode!=0) {
				var code = '<div style="color:red;margin:0 0 20px;">'+res.retmsg+'</div>';
				jQuery('#'+did).html(code);
			} else {
				show(res.data);
			}
		});
	};

	function show(data) {
        if (data.status==dict.PRO_AUDIT_CANCEL) {
            var code = '<h1>'+data.progress_title+'</h1>'+
                '<div id="msg-'+did+'"></div>';
            jQuery('#'+did).html(code);
            msg.showException('msg-'+did,'此流程已撤销');
            return;
        }

		var code = '<h1>'+data.progress_title+'</h1>'+
            '<h2>流程步骤</h2>'+
            '<div id="flow-graph-div"></div>'+
            '<h2>基本信息</h2>'+
            '<div id="flow-form-div"></div>'+
            '<h2>审批信息</h2>'+
            '<div id="flow-audit-div"></div>';
		jQuery('#'+did).html(code);
        require('./graph').show('flow-graph-div',data);
        require('./form').show('flow-form-div',data);
        require('./audit').show('flow-audit-div',data);
	};

	return o;
});
