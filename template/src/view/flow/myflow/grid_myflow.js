define(function(require){
	/* 我发起的流程列表 */
	var dict = require('common/dict');
	var store,grid,gridid;
    var state;

    var o={};

	o.init=function(_gridid,_state){
        state = _state;
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('progress&action=myflow')
    		})
		});
		grid = new MWT.Grid({
			render   : gridid,
    		store    : store,
			pagebar  : true,   // false 表示不分页
			bordered : false,  // false 不显示列边框
			pageSize : 20,     // 默认每页大小
			multiSelect : false,
            striped  : true,
			tbar: [
                {type:"radiobtn",label:"状态",id:"state-sel-"+gridid,value:'-1',cls:'mwt-btn-default',
                 options:[
                    {value:-1,text:"全部"},
                    {value:dict.PRO_AUDIT_TODO,text:"待审批"},
                    {value:dict.PRO_AUDIT_SUCC,text:"通过"},
                    {value:dict.PRO_AUDIT_FAIL,text:"驳回"}
                 ],
                 handler:o.query},
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询我的流程单',handler:o.query}
			],
            bodyStyle: 'min-height:200px;',
			cm: new MWT.Grid.ColumnModel([
				{head:"流程单号", dataIndex:"pgid",width:100,align:'left',sort:true,render:function(v,item){
					return v;
				}},
				{head:"流程",dataIndex:"progress_title",align:'left',render:function(v,item){
					var url = '#/flow~f='+item.pgid;
					return '<a class="grida" href="'+url+'" target="_blank">'+v+'</a>';
				}},
				{head:"创建时间",dataIndex:"ctime",width:120,align:'center',sort:true,render:timeRender},
				{head:"状态",dataIndex:"status",width:100,align:'center',sort:true,render:function(v){
					return dict.get_audit(v);
				}},
				{head:"操作", dataIndex:"pgid",width:120,align:'right',render:function(v,item){
					var viewbtn = '<a class="mwt-btn mwt-btn-primary mwt-btn-xs" href="#/flow~f='+v+'">查看</a>';
                    var btns = [viewbtn];
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
		});
		o.query();
	};

	o.query=function() {
		store.baseParams = {
			status: mwt.get_value('state-sel-'+gridid),
            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
	};

	return o;
});
