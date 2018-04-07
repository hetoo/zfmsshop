var cate_id = getQueryString("cate_id");


function getChildClass(){
	$.ajax({
		url:ApiUrl + '/index.php?act=goods_class&op=get_child_all',
		type: 'get',
		data:{gc_id:cate_id},
		dataType: 'json',
		success: function(result) {
			if(!result){
				result = [];
				result.datas = [];
			}
			var data = result.datas;
            data.WapSiteUrl = WapSiteUrl;
            var html = template.render('category-two', data);
            $(".classify-inner").html(html);
		}
	});
}

$(function(){
	$.ajax({
		url:ApiUrl + '/index.php?act=goods_class',
		type: 'get',
		dataType: 'json',
		success: function(result) {
			var data = result.datas;
			var class_list = data.class_list;
			var class_count = class_list.length;
			var html = '';
			if(class_count > 0){
				for (var i = 0; i < class_count; i++) {
					if(i > 0){
						html += '<li class="c1" cate_id="'+class_list[i].gc_id+'">'+class_list[i].gc_name+'</li>';
					}else{
						html += '<li class="c1 current" cate_id="'+class_list[i].gc_id+'">'+class_list[i].gc_name+'</li>';
						cate_id = class_list[i].gc_id;
					}
				}
			}
			$('#goods_class_list').html(html);
			getChildClass();
		}
	});
	$('#goods_class_list').on('click','li',function(){
		var obj = $(this);
		cate_id = obj.attr('cate_id');		
		obj.siblings().removeClass('current');
		obj.addClass('current');
		getChildClass();
	});
	$('input.search-txt').click(function(){
		location.href = WapSiteUrl + "/tmpl/dhome/search.html?cate_id="+cate_id;
	});
});