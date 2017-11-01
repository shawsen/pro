define(function(require){
	/* 公告 */

    var o={};

	o.init=function(domid){
		var code = '<div class="panel">'+
			'<div class="panel-head">'+
				'<i></i> '+lang.announcement+
			'</div>'+
			'<div class="panel-body">'+
				'<img src="">'+
				'<div class="content">aaaa</div>'+
			'</div>'+
		'</div>';
		jQuery('#'+domid).html(code);
	};

	return o;
});
