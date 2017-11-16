define(function(require){
    /* 表单信息 */
    var o={};

    o.show=function(domid,data) {
        var code = '<div id="form-'+domid+'" class="audit-tab"></div>';
        jQuery('#'+domid).html(code);       

        if (data.module=='#pro#pro_pr') {
            require('form/pr/main').create({
                render : 'form-'+domid,
                formid : data.module_id,
                print  : false,
                edit   : false
            }).init();
        } 
        else if (data.module=='#pro#pro_po') {
            require('form/po/main').create({
                render : 'form-'+domid,
                formid : data.module_id,
                print  : false,
                edit   : false
            }).init();
        }
    };

    return o;
});
