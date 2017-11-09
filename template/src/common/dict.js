define(function(require){
    var o={};
    // 审批状态
    var audit_dict = { 
        '100': '<span style="color:green;">通过</span>',
        '101': '<span style="color:blue;">待审批</span>',
        '103': '<span style="color:red">审批驳回</span>'
    };  
   
    o.get_audit=function(id) {
        return audit_dict[id] ? audit_dict[id] : id; 
    };  

    o.get_audit_options=function(firstoption) {
        return tooptions(audit_dict,firstoption);
    };  


    // dict转成options
    function tooptions(dictionary,firstoption) {
        var options = []; 
        if (firstoption) options.push(firstoption);
        for(var id in dictionary) {
            options.push({text:dictionary[id],value:id});
        }   
        return options;
    }   
    return o;
});
