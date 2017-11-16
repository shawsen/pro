define(function(require){
	/* 我的流程 */
    var o={};

	o.execute=function(domid,tabidx){
        var tabs = [
            ['待处理的流程'],
            ['我发起的流程']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/flow/myflow~s="+i;
            var cls = i==tabidx ? ' class="mwt-active"' : '';
            var code = '<li'+cls+'><a href="'+url+'">'+tab[0]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                '<li>&nbsp;</li>'+
                tabcode.join('')+
            '</ul>'+
            '<div id="grid-'+domid+'" style="margin-top:10px;"></div>';
        jQuery('#'+domid).html(code);

        if (tabidx==0) {
		    require('./grid_mytodo').init('grid-'+domid);
        } else {
		    require('./grid_myflow').init('grid-'+domid);
        }
	};

	return o;
});
