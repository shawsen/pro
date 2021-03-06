define(function(require){
	/* grid.js, (c) 2017 mawentao */
    var poproc = require('./poproc');
	var store,grid,gridid;
    var state;

    var o={};

	o.init=function(_gridid,_state){
        state = _state;
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('po&action=queryMine')
    		})
		});
		grid = new MWT.Grid({
			render   : gridid,
    		store    : store,
			pagebar  : true,  // false 表示不分页
			bordered : false,  // false 不显示列边框
			pageSize : 50,    // 默认每页大小
			multiSelect : false,
            striped  : true,
			tbarStyle: 'margin-bottom:-1px;',
			tbar: [
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询PR单',handler:o.query}
			],
            bodyStyle: 'min-height:300px;',
			cm: new MWT.Grid.ColumnModel([
				{head:"PO单编号", dataIndex:"poid",width:100,align:'left',sort:true,render:function(v,item){
					return v;
				}},
				{head:"Title",dataIndex:"poname",align:'left',render:function(v,item){
                    var url = '#/po/form~poid='+item.poid_code;
					return '<a href="'+url+'">'+v+'</a>';
				}},
				{head:"关联流程",dataIndex:"progress_title",width:200,align:'left',render:function(v,item){
                    if (!v) return '';
                    return '<a href="#/flow~f='+item.pgid+'">'+v+'</a>';
                }},
				{head:"创建时间",dataIndex:"ctime",width:120,align:'center',sort:true,render:timeRender},
				{head:"状态",dataIndex:"status",width:100,align:'center',render:prStateRender},
				{head:"操作", dataIndex:"poid",width:150,align:'right',render:function(v,item){
					var editbtn = '<a class="grida" href="#/requirectl/pr~prid='+item.prid_code+'" '+
							'name="editbtn" data-id="'+v+'">编辑</a>';
                    var submitbtn = '<a class="mwt-btn mwt-btn-default mwt-btn-xs" href="javascript:;" '+
							'name="submitbtn" data-id="'+v+'">提交</a>';
                    var flowbtn = '<a class="grida" href="#/flow~f='+item.pgid+'" '+
							'name="flowbtn" data-id="'+v+'">审批详情</a>';
                    var cancelbtn = '<a class="grida" href="javascript:;"'+
							'name="cancelbtn" data-id="'+v+'">撤销</a>';
                    var btns = [editbtn,submitbtn];
                    switch (parseInt(item.status)) {
                        case dict.PRO_STATE_EDIT: btns=[editbtn,submitbtn]; break;
                        case dict.PRO_AUDIT_SUCC: btns=[flowbtn]; break;
                        case dict.PRO_AUDIT_TODO: btns=[flowbtn,cancelbtn]; break;
                        case dict.PRO_AUDIT_FAIL: btns=[editbtn,submitbtn]; break;
                        case dict.PRO_AUDIT_CANCEL: btns=[editbtn,submitbtn]; break;
                    }
                    return btns.join("&nbsp;&nbsp;&nbsp;");
				}}
			])
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
			// 提交PO单
			jQuery('[name=submitbtn]').unbind('click').click(function(){
                var poid = jQuery(this).data('id');
                mwt.confirm('确定要提交此PO单吗？',function(res){
                    if (res) {
                        poproc.submit(poid,o.query);
                    }
                });
			});
            // 撤销PR单
            jQuery('[name=cancelbtn]').unbind('click').click(function(){
                var prid = jQuery(this).data('id');
                mwt.confirm('确定要撤销此PR单吗？',function(res){
                    if (res) {
                        poproc.cancel(prid,o.query);               
                    }
                });
            });
		});
		o.query();
	};

	o.query=function() {
		store.baseParams = {
			status: state,
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
	};

	return o;
});
