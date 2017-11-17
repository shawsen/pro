define(function(require){
    /* PO单处理 */
    var o={};

    // 提交PO单
    o.submit=function(poid,callback) {  
		var msgid = mwt.notify('正在提交...',0,'loading');
        ajax.post('po&action=submit',{poid:poid},function(res){
			mwt.notify_destroy(msgid);
            if (res.retcode!=0) {
                mwt.notify(res.retmsg,1500,'danger');
                return;
            }
            if (callback) {callback();}
        });
    };

    // 撤销PR单
    o.cancel=function(poid,callback) {
        var params = {
            poid: poid
        };
        print_r(params);
        ajax.post('requirectl&action=prCancel',params,function(res){
            if (res.retcode!=0) {
                mwt.notify(res.retmsg,1500,'danger');
                return;
            }
            if (callback) {callback();}
        });
    };

    return o;
});
