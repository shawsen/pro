define(function(require){
	/* 联系方式 */

    var o={};

	o.init=function(domid){
		var code = '<div class="panel">'+
			'<div class="panel-head">'+
				'<i></i> '+lang.contact+
			'</div>'+
			'<div class="panel-body">'+
				'<img src="">'+
				'<div class="content">'+
				  '<table class="listab">'+
					'<tr><th>Tel:</th><td>'+setting.contact_tel+'</td></tr>'+
					'<tr><th>E-mail:</th>'+
						'<td><a href="mailto:'+setting.contact_email+'">'+setting.contact_email+'</a></td></tr>'+
					'<tr><th>Web Site:</th>'+
						'<td><a href="'+setting.contact_siteurl+'" target="_blank">'+setting.contact_siteurl+'</a></td></tr>'+
				  '</table>'+
				'</div>'+
			'</div>'+
		'</div>';
		jQuery('#'+domid).html(code);
	};

	return o;
});
