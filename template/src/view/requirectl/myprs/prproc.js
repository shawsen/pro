define(function(require){
    /* PR单处理 */
    var o={};


    // 撤销PR单
    o.cancel=function(prid,callback) {
        var params = {
            prid: prid
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
