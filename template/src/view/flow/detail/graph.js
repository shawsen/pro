define(function(require){
    /* 流程图 */
    var o={};

    o.show=function(domid,data) {
        var nodes = [
            {state:'pass',name:'发起流程'}/*,
            {state:'fail',name:'直属领导审批'},
            {state:'todo',name:'采购总监审批'}*/
        ];

        for (var i=0;i<data.nodes.length;++i) {
            var im = data.nodes[i];
            var node = {
                state: 'todo',
                name: im.node_name
            };
            if (im.status==100 || im.status==102) node.state='pass';
            if (im.status==103) node.state='fail';
            nodes.push(node);
        }

        var tr1 = [];
        var tr2 = [];
        var n = nodes.length;
        for (var i=0;i<n;++i) {
            var node = nodes[i];
            var cls = 'mwt-icon-btn xs';
            switch (node.state) {
                case 'pass': cls+=' mwt-success fa-check'; break;
                case 'fail': cls+=' mwt-danger fa-times'; break;
                default: break;
            }
            var line = (i==n-1) ? '' : '<td class="'+node.state+'"><hr></td>';
            tr1.push('<td class="node '+node.state+'"><i class="'+cls+'"></i>'+line+'</td>');
            tr2.push('<td class="'+node.state+'" colspan="2">'+node.name+'</td>');
        }

        var code = '<table class="flowtab">'+
            '<tr>'+tr1.join('')+'</tr>'+
            '<tr>'+tr2.join('')+'</tr>'+
        '</table>';
        jQuery('#'+domid).html(code);
    };

    return o;
});
