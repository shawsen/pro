define(function(require){
	/* 流程详情页 */
    var o={};
	var did;

	o.execute=function(domid,f){
		did = 'main-'+domid;
		var code = '<div class="flowdiv" id="'+did+'"></div>'+
		require('common/copyright').get();
		jQuery('#'+domid).html(code);
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
		var code = '<h1>'+data.progress_title+'</h1>'+
			'<hr>';

		jQuery('#'+did).html(code);
	};

	return o;
});
