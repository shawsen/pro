define(function(require){
    /* 审批信息 */
    var o={};

    function getAuditResult(node) {
        // 待审批
        if (node.status==dict.PRO_AUDIT_TODO) {
            // 当前节点已激活 && 当前用户是审批人
            if (node.uid==dz.uid && node.is_active==1) {
                var passBtn = '<button name="audit-btn" class="mwt-btn mwt-btn-success mwt-btn-sm" '+
                    'data-id="'+node.pgnodeid+'" data-state="'+dict.PRO_AUDIT_SUCC+'">同意</button>';
                var failBtn = '<button name="audit-btn" class="mwt-btn mwt-btn-danger mwt-btn-sm" '+
                    'data-id="'+node.pgnodeid+'" data-state="'+dict.PRO_AUDIT_FAIL+'">驳回</button>';
                var skipBtn = '<button name="audit-btn" class="mwt-btn mwt-btn-default mwt-btn-sm" '+
                    'data-id="'+node.pgnodeid+'" data-state="'+dict.PRO_AUDIT_SKIP+'">跳过</button>';
                var btns = [passBtn,failBtn];
                if (node.can_skip==1) btns.push(skipBtn);
                return btns.join('&nbsp;&nbsp;&nbsp;');
            }
        }
        // 审批通过
        else if (node.status==dict.PRO_AUDIT_SUCC) {
            return '<span style="color:green;">审批通过</span>';
        }
        // 审批跳过
        else if (node.status==dict.PRO_AUDIT_SKIP) {
            return '<span style="color:green;">跳过</span>';
        }
        // 审批驳回
        else if (node.status==dict.PRO_AUDIT_FAIL) {
            return '<span style="color:red;">驳回</span>';
        }
        return '等待审批';
    }

    o.show=function(domid,data) {
        var lis = [];
        for (var i=0;i<data.nodes.length;++i) {
            var im = data.nodes[i];
            var auditUserInfo = im.userinfo;
            var auditGroup = 'aaaa';
            var auditResult = getAuditResult(im);
            var code = '<table class="audit-tab">'+
                '<tr><th colspan="4"><h3>'+im.node_name+'</h3></th></tr>'+
                '<tr>'+
                  '<th width="130">任务处理人:</th>'+
                  '<td width="250"><a href="javascript:uc.showInDialog('+im.uid+')">'+auditUserInfo.realname+'</a></td>'+
                  '<th width="130">审批结果:</th><td>'+auditResult+'</td>'+
                '</tr>'+
                '<tr>'+
                  '<th>部门:</th><td>'+auditUserInfo.group_name+'</td>'+
                  '<th>审批理由:</th><td>'+im.feedback+'</td>'+
                '</tr>'+
                '<tr><td colspan="4"></td></tr>'+
            '</table>';
            lis.push(code);
        }
        var code = lis.join('');
        jQuery('#'+domid).html(code);
        jQuery('[name=audit-btn]').unbind('click').click(audit);
    };

    function audit() {
        var params = {
            pgnodeid: jQuery(this).data('id'),
            status: jQuery(this).data('state')
        }
        if (params.status == dict.PRO_AUDIT_FAIL) {
            mwt.prompt({title:'请输入驳回理由',type:'textarea',top:80},function(res){
                params.feedback = res;
                subAudit(params);
            });
        }
        else if (params.status == dict.PRO_AUDIT_SKIP) {
            params.feedback = '跳过';
            subAudit(params);
        } 
        else {
            params.feedback = '同意';
            subAudit(params);
        }
    }

    function subAudit(params) {
        ajax.post('progress&action=audit',params,function(res){
            if (res.retcode!=0) {
                mwt.alert(res.retmsg);
            } else {
                mwt.alert("已审批",function(){
                    window.location.reload();
                });
            }
        });
    }

    return o;
});
