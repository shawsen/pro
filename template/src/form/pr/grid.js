/* grid.js, (c) 2017 mawentao */
define(function(require){
    var o={};
	var i = 0;


	var columns = [
		{header:'产品',dataIndex:'prod_info',align:'left',field:'text',defaultValue:''},
		{header:'数量',dataIndex:'item_num',width:80,align:'right',field:'number',defaultValue:'0'},
		{header:'单位',dataIndex:'item_unit',width:80,align:'center',field:'text',defaultValue:'个'},
		{header:'单价',dataIndex:'item_unit_price',width:80,align:'right',field:'number',defaultValue:'0.00'},
		{header:'币种',dataIndex:'item_price_cny',width:80,align:'center',field:'text',defaultValue:'元'},
		{header:'总价',dataIndex:'item_num',width:80,align:'center',render:function(v,item){
			return item.item_num * item.item_unit_price;
		}}
	];

	function getTdStyle(col) {
		var styles = [];
		if (col.width) styles.push('width:'+col.width+'px');
		if (col.align) styles.push('text-align:'+col.align);
		var style = styles.length ? ' style="'+styles.join(';')+'"' : '';
		return style;
	}


	function getField(data,key,readonly) {
		if (readonly) return data[key];
		var code = '<input type="text" name="fm-'+key+'" class="mwt-field" value="'+data[key]+'">';
		return code;
	}

	function addRow(tbodyid,data,readonly) {
		++i;
		if (!data) {
			data = {
				prod_info: '',
				item_num: 1,
				item_unit: '个',
				item_unit_price: '0.00',
				item_price_cny: '元'
			};
		}

		var cols = [];
		for (var i=0;i<columns.length;++i) {
			var cim = columns[i];
			var style = getTdStyle(cim);
//			var v = data[cim.dataIndex] ? data[cim.dataIndex] : cim.defaultValue;
			var td = '<td'+style+'>'+getField(data,cim.dataIndex,readonly)+'</td>';
			cols.push(td);
		}


		var opbtn = '';
		if (!readonly) {
			opbtn = '<td><a name="delbtn-'+tbodyid+'" data-row="'+i+'" '+
				'class="grida" href="javascript:;">删除</a></td>';
		}
		var row = '<tr id="row-'+tbodyid+i+'">'+
			cols.join('')+
			//'<td>'+data.item_unit*data.item_unit_price+'</td>'+
			opbtn+
		'</tr>';
		jQuery('#'+tbodyid).append(row);
		jQuery('[name=delbtn-'+tbodyid+']').unbind('click').click(function(){
			var rowid = jQuery(this).data('row');
			jQuery('#row-'+tbodyid+rowid).remove();
		});
	}

	o.show=function(domid,data,readonly){
		var theads = [];
		for (var i=0;i<columns.length;++i) {
			var col = columns[i];
			var style = getTdStyle(col);
			theads.push('<th'+style+'>'+col.header+'</th>');
		}
		var opbtn = '';
		if (!readonly) {
			theads.push('<th></th>');
			opbtn = '<button class="mwt-btn mwt-btn-primary mwt-btn-xs round" style="float:right;"'+
				'id="addbtn-'+domid+'" >'+
				'<i class="sicon-plus" style="vertical-align:middle;"></i> 添加项</button>';
		}
		var code = '<thead><tr><th colspan="'+theads.length+'">采购项列表(Purchasing Product List)'+
			opbtn+'</th></tr>'+
			theads.join('')+'</thead>'+
			'<tbody id="imb-'+domid+'"></tbody>';
		jQuery('#'+domid).html(code);
		for (var i=0;i<data.items.length; ++i) {
			addRow('imb-'+domid,data.items[i],readonly);
		}
		jQuery('#addbtn-'+domid).unbind('click').click(function(){
			addRow('imb-'+domid,null,readonly);
		});

	};

	o.getItems=function(domid) {
		var n = jQuery('[name=fm-prod_info]').length;
		var keys = ['prod_info','item_num','item_unit','item_unit_price','item_price_cny'];
		var items = [];
		for (var i=0;i<n;++i) {
			var item = {};
			for (var k=0;k<keys.length;++k) {
				var key = keys[k];
				var jfd = jQuery('[name=fm-'+key+']').eq(i);				
				item[key] = jfd.val().trim();
			}
			items.push(item);
		}
		return items;
	};

	return o;
});
