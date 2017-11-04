/* japp.js, (c) 2016 mawentao */
/* 全局变量 */
var ajax,log,frame;
var conf = {
    // 日志级别 0:关闭;>=1:WARN;>=2:INFO;>=3DEBUG;
    loglevel: 3
};
/* JApp定义 */
var JApp=function(baseUrl)
{
    this.init = function() {
		require.config({
			baseUrl: baseUrl,
			packages: [
				{name:'lang', location:'lang', main:'main'},
				{name:'frame', location:'frame', main:'main'}
			]
		});
        require(['jappengine','core/log','core/ajax','frame'], function(jappengine,corelog,coreajax,fm){
			log = corelog;
			ajax = coreajax;
			frame = fm;
			jappengine.start();
        });
    };
};


// 
function timeRender(v) {
    var st = strtotime(v);
    return '<span style="color:gray">'+date('Y/m/d H:i',st)+'</span>';
}

function prStateRender(v) {
    switch(parseInt(v)) {
        case 1: return '<span style="color:#333;">未提交</span>';
        case 2: return '<span style="color:blue;">已提交</span>'
    }
    return v;
}
