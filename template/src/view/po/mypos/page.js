define(function(require){
	/* 我的PO单 */
    var idx=0,gridid;
    var o={};

	o.execute=function(domid,_idx){
        idx = _idx;
        gridid = 'grid-'+domid;
        var tabs = [
            ['待提交'],
            ['审核中'],
            ['已审核']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/po/list~s="+i;
            var cls = i==idx ? ' class="mwt-active"' : '';
            var code = '<li'+cls+'><a href="'+url+'">'+tab[0]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                '<li>&nbsp;</li>'+
                tabcode.join('')+
                '<li style="float:right">'+
                    '<button class="mwt-btn mwt-btn-primary mwt-btn-sm radius"'+
                        ' onclick="window.location=\'#/po/form\'">'+
                        '<i class="sicon-plus" style="vertical-align:middle;"></i> 创建PO单'+
                    '</button>'+
                '</li>'+
            '</ul>'+
            '<div id="grid-'+domid+'" style="margin-top:10px;"></div>';
        jQuery('#'+domid).html(code);
		require('./grid').init('grid-'+domid,idx);
	};

	return o;
});
