define(function(require){
	/* 待处理的流程 */
    var o={};

	o.execute=function(domid){
        var pos = [{name:'待处理的流程'}];

        var code = require('common/posnav').get(pos)+
            '<div id="grid-'+domid+'"></div>'+
            require('common/copyright').get();
        jQuery('#'+domid).html(code);

		require('./grid').init('grid-'+domid);
        
	};

	return o;
});
