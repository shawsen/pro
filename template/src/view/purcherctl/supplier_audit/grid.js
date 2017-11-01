define(function(require){
	/* grid.js, (c) 2017 mawentao */
	var store,grid,gridid;


    var o={};

	o.init=function(_gridid){
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('purcherctl&action=supplier_audit_query')
    		})
		});
		grid = new MWT.Grid({
			render   : gridid,
    		store    : store,
			pagebar  : true,  // false 表示不分页
			bordered : false,  // false 不显示列边框
			pageSize : 10,    // 默认每页大小
			multiSelect : true,
			position : 'fixed',
			cm: new MWT.Grid.ColumnModel([
				{head:lang.supplier, dataIndex:"supplier_id",align:'left',render:function(v,item){
					var code = '<div style="color:gray;">'+
						'<a class="grida" href="#/">'+item.company_name+'</a>'+
						' ['+item.company_short_name+']'+
						'<a class="mwt-btn" style="margin-left:3px;padding:0;color:#999" href="http://www.baidu.com/s?wd='+item.company_name+'" target="_blank">'+
							'<i class="icon icon-search" pop-title="百度一下" pop-cls="mwt-popover-secondary"></i></a>'+
					  '<br>税务登记证号: '+item.company_certnum+
						'<a class="mwt-btn" style="margin-left:3px;padding:0;color:#999" href="http://www.gsxt.gov.cn/" target="_blank">'+
							'<i class="icon icon-report" pop-title="查询企业信用" pop-cls="mwt-popover-secondary"></i></a>'+
					  '<br>成立时间: '+item.company_found_time+'&nbsp;&nbsp;&nbsp;注册资金: '+item.company_regist_capital+
					'</div>';
					return code;
				}},
				{head:lang.state,dataIndex:"status",align:'center',width:100,render:function(v,item){
					switch(v) {
						case '0': return '<span style="color:green;">审核通过</span>';
						case '1': return '<span style="color:blue;">待审核</span>';
						case '2': return '<span style="color:red;">审核驳回：'+item.audit_feadback+'</span>';
					}
					return v;
				}},
				{head:lang.operate, dataIndex:"supplier_id",width:120,align:'center',render:function(v,item){
					var passbtn = '<a class="mwt-btn mwt-btn-success mwt-btn-xs" href="javascript:;" '+
							'name="auditbtn" data-id="'+v+'" data-v="0">通过</a>';
                    var rejectbtn = '<a class="mwt-btn mwt-btn-danger mwt-btn-xs" href="javascript:;" '+
							'name="auditbtn" data-id="'+v+'" data-v="2">驳回</a>';
                    var btns = [passbtn,rejectbtn];
					if (item.status==0) btns = [rejectbtn];
					if (item.status==2) btns = [passbtn];
                    return btns.join("&nbsp;&nbsp;&nbsp;");
				}}
			]),
			tbarStyle: 'margin-bottom:10px;',
			tbar: [
				{label:'审核状态：',type:'radiobtn',id:"sel-status",value:'1',cls:'mwt-btn-default',options:[
					{value:1,text:"待审核"},
					{value:0,text:"通过"},
					{value:2,text:"驳回"}
				 ],handler:o.query},
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询供应商',handler:o.query},
				'->',
				{label:"Add",handler:function(){alert("Add");}}
			]
		});
		grid.create();
		store.on('load',function(){
			mwt.popinit();
			jQuery('[name=auditbtn]').unbind('click').click(function(){
				var data = { 
                    supplier_id : jQuery(this).data('id'),
                    status : jQuery(this).data('v'),
                    reason: ''
                };  
                if (data.status==2) {
                    data.reason = window.prompt("请输入驳回理由");
                }
                ajax.post('purcherctl&action=supplier_audit',data,function(res){
                    if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
                    else {
                        o.query();
                    }
                });
			});
		});
		o.query();
	};

	o.query=function() {
		store.baseParams = {
			status: mwt.get_value('sel-status'),
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
	};


	return o;
});
