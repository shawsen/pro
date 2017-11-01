define(function(require){
	/* 首页 */
    var o={};

	o.execute=function(domid){
		var code = '<div class="wp">'+
			'<table class="wptab"></tr>'+
				'<td style="padding-right:10px;">'+
				  	'<div id="introduction-'+domid+'"></div>'+
				'</td>'+
				'<td width="280">'+
				  	'<div id="announce-'+domid+'"></div>'+
				  	'<div id="contact-'+domid+'" style="margin-top:10px;"></div>'+
				'</td>'+
			'</tr></table>'+
		'</div>'+
		require('common/copyright').get();
		jQuery('#'+domid).css({"background":"#eee"}).html(code);

		require('./introduction').init('introduction-'+domid);
		require('./announce').init('announce-'+domid);
		require('./contact').init('contact-'+domid);
	};

	return o;
});
