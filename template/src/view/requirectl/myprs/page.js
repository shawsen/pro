define(function(require){
	/* 我的PR单 */
    var o={};

	o.execute=function(domid,state){
        var tabs = [
            [1,'未提交'],
            [2,'已提交']
        ];
        var tabcode = [];
        for (var i=0; i<tabs.length; ++i) {
            var tab = tabs[i];
            var url = "#/requirectl/myprs~s="+tab[0];
            var cls = tab[0]==state ? ' class="mwt-active"' : '';
            var code = '<li'+cls+'><a href="'+url+'">'+tab[1]+'</a></li>';
            tabcode.push(code);
        }
        var code = '<ul class="mwt-nav mwt-nav-tabs">'+
                tabcode.join('')+
                '<li style="float:right">'+
                    '<button id="newpr-'+domid+'" class="mwt-btn mwt-btn-primary mwt-btn-sm radius">'+
                        '<i class="sicon-plus" style="vertical-align:middle;"></i> 新建PR单'+
                    '</button>'+
                '</li>'+
            '</ul>'+
            '<div id="grid-'+domid+'" class="mwt-layout-fill" style="top:40px;"></div>';
        jQuery('#'+domid).html(code);
        jQuery('#newpr-'+domid).unbind('click').click(newpr);
		require('./grid').init('grid-'+domid,state);
	};

    // 新建PR单
    function newpr() {
        var msgid=mwt.notify('正在创建PR单...',0,'loading');
        setTimeout(function(){
            ajax.post('requirectl&action=prCreate',{},function(res){
                mwt.notify_destroy(msgid);
                if (res.retcode!=0) {
                    require('common/msg').showException(render,res.retmsg);
                } else {
                    window.location = '#/requirectl/pr~prid='+res.data;
                }
            });
        },1000);
    };

	return o;
});
