define(function(require){
/* PR单 */
function PRForm(opts)
{
    var render = '';
    var print = false;
    var prid = 0;
    if (opts) {
        if(opts.render) render=opts.render;
        if(opts.print) print=opts.print;
        if(opts.formid) prid=opts.formid;
    }

    this.init = function() {
        var code = '<div class="formdiv">'+
            '<img class="logo" src="'+dz.logo+'">'+
            '<h1>采购申请单 (Purchasing Requisition Form)</h1>'+
            '<div>'+
              '<span>申请人: aaa</span>'+
              '<span style="float:right">编号 PR No. 1231312321</span>'+
            '</div>'+
            '<table>'+
                '<tr><th>采购产品列表 (Purchasing Product List)</th></tr>'+
            '</table>'
        '</div>';

        if (!print) {
            code += '<a href="'+dz.siteurl+'/plugin.php?id=pro:print&form=pr&formid='+prid+'" '+
                    'class="mwt-btn mwt-btn-default mwt-btn-sm" target="_blank">打印</a>';
        }


        jQuery('#'+render).html(code);
    };
}

return {
    create: function(domid,prid) {
        return new PRForm(domid,prid);
    }
}
});
