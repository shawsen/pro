define(function(require){
	/* 版权信息 */
    var o={};

    // 最底部的版权信息(审核要求,必须加上)
    o.get = function() {
		var starty=2017;
		var nowy = parseInt(date('Y'));
		var yp = nowy>starty ? starty+'-'+nowy : starty;

        var code = '<div style="text-align:center;font-size:12px;margin:10px 0;font-family:\'microsoft yahe\';">'+
              '<p style="color:#666;">'+setting.page_copyright+'</p>'+
            '</div>';
        return code;
    };

    return o;
});
