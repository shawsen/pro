define(function(require){
	/* grid.js, (c) 2017 mawentao */
	var dict = require('common/dict');
	var store,grid,gridid;
    var state;

    var o={};

	o.init=function(_gridid,_state){
        state = _state;
		gridid = _gridid;
		store = new mwt.Store({
    		proxy: new mwt.HttpProxy({
        		url: ajax.getAjaxUrl('progress&action=mytodo')
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
//			position : 'fixed',
/*			tbarStyle: 'margin-bottom:10px;background:#fff;',
			tbar: [
				{type:"search",id:"so-key-"+gridid,width:300,placeholder:'查询我的流程单',handler:o.query}
			],*/
			cm: new MWT.Grid.ColumnModel([
				{head:"流程发起人", dataIndex:"uid",width:160,align:'left',sort:true,render:function(v,item){
					return item.realname+'（'+item.group_name+'）';
				}},
				{head:"流程",dataIndex:"progress_title",align:'left',render:function(v,item){
					var url = '#/flow~f='+item.pgid;
					return '<a class="grida" href="'+url+'" target="_blank">'+v+'</a>';
				}},
				{head:"到达时间",dataIndex:"active_time",width:120,align:'center',sort:true,render:timeRender},
/*				{head:"状态",dataIndex:"status",width:100,align:'center',render:function(v){
//					return dict.get_audit(v);
				}},*/
				{head:"操作", dataIndex:"pgid",width:120,align:'center',render:function(v,item){
					var viewbtn = '<a class="grida" href="#/flow~f='+v+'">处理</a>';
                    var btns = [viewbtn];
					if (item.status==101) btns.push(cancelbtn);
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
//			status: state,
//            key: mwt.get_value("so-key-"+gridid)
        };  
        grid.load();
	};

	return o;
});
