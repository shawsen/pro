define(function(require){
	/* 供应商审核 */
    var o={};

	o.execute=function(domid){
		require('./grid').init(domid);
	};

	return o;
});
