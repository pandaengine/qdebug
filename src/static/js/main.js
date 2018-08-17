/**
 * 打开SQL解释
 * obj=obj.parentNode.nextSibling;
 * 
 * @param obj
 */
function explain_toggle(obj){
	var parent=$(obj).parent();
	parent.find('.result-box').toggle();
	/*
	obj=obj.nextSibling;
	var d=obj.style.display; 
	if(d=='block'){
		obj.style.display='none';
	}else{
		obj.style.display='block';
	}
	*/
}

function explain(){
	var dom=$('textarea');
	var sql=dom.val();
	if(sql==''){
		return false;
	}
	var exp='explain';
	var idx=sql.indexOf(exp);
	if(idx==-1){
		//#加入explain
		sql=exp+' '+sql;
	}else{
		//#剔除explain
		sql=sql.substr(idx+exp.length);
	}
	dom.val(sql);
}
function table_toggle(id){
	$(id).toggle();
}

var SQL={};
/**
 * 某个表单
 */
SQL.submit=function(form){
	var $form =$(form);
	var action=$form.attr('action');
	var params=$form.serializeArray();
	$.post(action,params,function(data){
		console.dir(data);
	});
};