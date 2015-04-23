/**
 * 全选/取消checkbox
 *
 * @copyright JDphp框架
 * @version 1.0.7
 * @author yy
 */
var selCheckbox = (function(){
	var s;
   	var Init = function(tableId, relName, selectType){
        var list = $("#" + tableId).find("input[type='checkbox'][rel='" + relName + "']");
        list.each(function(i){
            $(this).bind("click", function(){
                selCheckbox.CheckHanler(list);
            });
        });
        selCheckbox.CheckControl(list, selectType)
    };
    
    var CheckControl = function(childs, selectType){
        for (var i = 0, len = childs.length; i < len; i++) {
            switch (selectType) {
                case 1: //全选
                    childs[i].checked = true;
                    break;
                case 2: //不选
                    childs[i].checked = false;
                    break;
                case 3: //反选
                    childs[i].checked = !childs[i].checked;
                    break;
            }
        }
        if (selCheckbox.CheckHanler) {
            selCheckbox.CheckHanler(childs);
        }
    };
    
    var CheckHanler = function(list){
        list.each(function(i){
            var input = $(this);
            if (this.checked) {
				//给tr添加选中样式
                input.parent().parent().addClass("checked");
            }
            else {
                input.parent().parent().removeClass("checked");
            }
        });
    }	
	return {
			Init : Init,
			CheckControl : CheckControl,
			CheckHanler : CheckHanler
		};
})();

