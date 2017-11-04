define(function(require){
    /* 消息 */
    var o={};

    o.showException=function(domid,msg) {
        var code = '<div class="mwt-wall mwt-wall-danger">'+msg+'</div>';
        jQuery('#'+domid).html(code);;
    };

    return o;
});
