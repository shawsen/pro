define(function(require){
	/* grid.js, (c) 2017 mawentao */
    var prproc = require('./prproc');
	var store,grid,gridid;
    var state;

    var o={};

	o.init=function(_gridid,_state){
        state = _state;
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('requirectl&action=prQuery')
    		})
		});
		grid = new MWT.Grid({
			render   : gridid,
    		store    : store,
			pagebar  : true,  // false 表示不分页
			bordered : false,  // false 不显示列边框
			pageSize : 10,    // 默认每页大小
			multiSelect : false,
            striped  : true,
			position : 'fixed',
			cm: new MWT.Grid.ColumnModel([
				{head:"PR单号", dataIndex:"prid",width:100,align:'left',sort:true,render:function(v,item){
					return v;
				}},
				{head:"Title",dataIndex:"prname",align:'left',render:function(v,item){
                    var url = '#/requirectl/pr~prid='+item.prid;
					return '<a href="'+url+'">'+v+'</a>';
				}},
				{head:"创建时间",dataIndex:"ctime",width:120,align:'center',sort:true,render:timeRender},
				{head:"状态",dataIndex:"status",width:100,align:'center',render:prStateRender},
				{head:"审批流程",dataIndex:"progress_title",width:120,align:'center',render:function(v,item){
                    if (!v) return '';
                    return '<a class="grida" href="#/flow~f='+item.pgid+'">'+v+'</a>';
                }},
				{head:"操作", dataIndex:"prid",width:150,align:'center',render:function(v,item){
					var editbtn = '<a class="grida" href="#/requirectl/pr~prid='+item.prid_code+'" '+
							'name="editbtn" data-id="'+v+'">编辑</a>';
                    var submitbtn = '<a class="grida" href="javascript:;" '+
							'name="submitbtn" data-id="'+v+'">提交</a>';
                    var flowbtn = '<a class="grida" href="#/flow~f='+item.pgid+'" '+
							'name="submitbtn" data-id="'+v+'">审批详情</a>';
                    var cancelbtn = '<a class="grida" href="javascript:;"'+
							'name="cancelbtn" data-id="'+v+'">撤销</a>';
                    var btns = [editbtn,submitbtn];
                    switch (parseInt(item.status)) {
                        case dict.PRO_STATE_EDIT: btns=[editbtn,submitbtn]; break;
                        case dict.PRO_AUDIT_SUCC: btns=[flowbtn]; break;
                        case dict.PRO_AUDIT_TODO: btns=[flowbtn,cancelbtn]; break;
                        case dict.PRO_AUDIT_FAIL: btns=[editbtn,submitbtn,flowbtn]; break;
                        case dict.PRO_AUDIT_CANCEL: btns=[editbtn,submitbtn]; break;
                    }
                    return btns.join("&nbsp;&nbsp;&nbsp;");
				}}
			]),
			tbarStyle: 'margin-bottom:-1px;',
			tbar: [
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询PR单',handler:o.query}
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
			// 提交PR单
			jQuery('[name=submitbtn]').unbind('click').click(function(){
				var params = {
					prid: jQuery(this).data('id')
				};
				ajax.post('requirectl&action=prSubmit',params,function(res){
					if (res.retcode!=0) mwt.notify(res.retmsg,1500,'danger');
					else {
						o.query();
					}
				});
			});
            // 撤销PR单
            jQuery('[name=cancelbtn]').unbind('click').click(function(){
                var prid = jQuery(this).data('id');
                mwt.confirm('确定要撤销此PR单吗？',function(res){
                    if (res) {
                        prproc.cancel(prid,o.query);               
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
