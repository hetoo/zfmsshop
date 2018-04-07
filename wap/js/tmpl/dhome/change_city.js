var get_state = getQueryString("get_state");
var city_id = getCookie('city_id');

function getCityList(){
	$.ajax({
		type: 'get',
	    url: ApiUrl + "/index.php?act=area&op=get_city_list",
	    dataType: 'json',
	    success: function(result) {
	        var data = result.datas;
	        var html = '';
	        city_list = data.city_list;
	        if(city_list.length > 0){
	        	html = '<h2 class="adr-title"><span>已开通城市</span></h2>';
	        	html += '<ul class="adr-ul scroller">';
	        	for (var i = 0; i < city_list.length; i++) {
	        		if(city_list[i].area_id == city_id){
	        			html += '<li areaname="'+city_list[i].area_name+'" cityid="'+city_list[i].area_id+'" tap="" class="curr">'+city_list[i].area_name+'</li>';
	        		}else{
	        			html += '<li areaname="'+city_list[i].area_name+'" cityid="'+city_list[i].area_id+'" tap="">'+city_list[i].area_name+'</li>';
	        		}	        		
	        	}
	        	html += '</ul>';
	        }else{
	        	html = '<div class="adr-notext">暂时没有开启任何城市<br>请联系平台管理员开通</div>';
	        }
	        $('div.choose-adr').html(html);
	    }
	});
}

$(function(){
	if(get_state == 'fail'){
		$.sDialog({
            skin:"red",
            content:"定位失败<br>请选择城市后手动搜索地址",
            okBtn:false,
            cancelBtn:true,
            cancelBtnText: "确定",
        });
	}
	getCityList();
	$('div.choose-adr').on('click','li',function(){
		var obj = $(this);
		delCookie('city_name');
		delCookie('city_id');
		addCookie('city_name',obj.attr('areaname'));
		addCookie('city_id',obj.attr('cityid'));
		location.href = WapSiteUrl + "/tmpl/dhome/change_address.html";
	});
});