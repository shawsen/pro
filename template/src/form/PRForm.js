define(function(require){
/* PR单 */
function PRForm(opts)
{
    var render = '';
    var print = false;
    var prid = 0;
    var data = {};
    if (opts) {
        if(opts.render) render=opts.render;
        if(opts.print) print=opts.print;
        if(opts.formid) prid=opts.formid;
    }

    function showFrom() {
        var ct = strtotime(data.ctime);
        var cdate = date('Y/m/d',ct);
        var code = '<div class="formdiv">'+
            '<img class="formlogo" src="'+dz.logo+'">'+
            '<h1>采购申请单 (Purchasing Requisition Form)</h1>'+
            '<div class="topbar">'+
//              '<label>申请人: </label><span class="field">'+data.creatorInfo.username+'</span>'+
              '<label>申请日期: </label><span class="field">'+cdate+'</span>'+
              '<label style="float:right">编号(PR No.) <span class="field">'+data.prid+'</span></label>'+
            '</div>'+
            '<table border="1">'+
              '<tr><th>采购产品列表 (Purchasing Product List)</th></tr>'+
            '</table>'+
            '<table border="1" style="margin-top:-1px;">'+
              '<tr height="130">'+
                '<td width="50%">申请人/部门<br>Originator/Dept.<span class="datespan"></span></td>'+
                '<td width="25%">部门经理<br>Department Manager<span class="datespan"></span></td>'+
                '<td width="25%">财务经理<br>Finance Controller<span class="datespan"></span></td>'+
              '</tr>'+
              '<tr height="130">'+
                '<td>总经理批准(PR 金额超过RMB15,000或者$2500)<br>'+
                    'GM Approve (Any PR exceeding RMB15,000 or USD 2,500)<span class="datespan"></span></td>'+
                '<td colspan="2">PR递交采购日期<br>PR received date by Purchasing<span class="datespan"></span></td>'+
              '</tr>'+
              '<tr>'+
                '<td colspan="3">说明/Notes:'+
                  '<br>1. BU Asia Finance manager should sign on the PR form for BU indirect purchasing;'+
                  '<br>2. Purchasing Group: 127 Yannie Luo'+
                  '<br>3. Purchasing Type: YI-general purchasing; YF-framkework purchasing for regular exp.; YT-purchasing for Capex'+
                  '<br>4. Purchasing Type: YI-general purchasing; YF-framkework purchasing for regular exp.; YT-purchasing for Capex'+
                  '<br>5. SAP auto generated PR No. should be filled up in the paper PR No. for filing and checking purpose.'+
                '</td>'+
              '</tr>'+
            '</table>'+
        '</div>';

        if (!print) {
            code += '<a href="'+dz.siteurl+'/plugin.php?id=pro:print&form=pr&formid='+prid+'" '+
                    'class="mwt-btn mwt-btn-default mwt-btn-sm" target="_blank">打印</a>';
        }
        jQuery('#'+render).html(code);
    }

    
    this.init = function() {
        try {
            if (prid==0) throw "此PR单不存在或已删除";
            ajax.post("requirectl&action=prDetail",{prid:prid},function(res){
                if (res.retcode!=0) {
                    require('common/msg').showException(render,res.retmsg);
                } else {
                    data=res.data;
                    showFrom();
                }
            });
        } catch (e) {
            require('common/msg').showException(render,e);
        }
    };
}

return {
    create: function(domid,prid) {
        return new PRForm(domid,prid);
    }
}
});
