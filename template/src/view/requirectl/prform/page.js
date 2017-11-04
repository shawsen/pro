define(function(require){
	/* 新建PR单 */
    var o={};

	o.execute=function(domid){
        var prform = require('form/PRForm').create({
            render: domid,
            formid: 0
        });
        prform.init();
	};

	return o;
});
