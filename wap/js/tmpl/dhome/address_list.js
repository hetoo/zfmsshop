var key = getCookie('key');

function changeAddress(obj){
	var address_id = obj.attr('addressid');	
	var city_id = obj.attr('city_id');	
	var district_id = obj.attr('district_id');
	var curr_lng = obj.attr('addresslng');
	var curr_lat = obj.attr('addresslat');
	var current_area = obj.attr('current_area');

	var area_info = obj.attr('area_info');

	var area_arr = area_info.split(' ');

	var district_name = area_arr[2];
	var city_name = area_arr[1];

	addCookie('city_name',city_name);
	addCookie('city_id',city_id);
	addCookie('district_name',district_name);
	addCookie('district_id',district_id);
	addCookie('curr_lng',curr_lng);
	addCookie('curr_lat',curr_lat);
	addCookie('current_area',current_area);
	addCookie('address_id',address_id);
	location.href = WapSiteUrl + '/dhome.html';
}

$(function(){
	$('#get_location').on('click',function(){
		delCookie('city_name');
		delCookie('city_id');
		delCookie('district_name');
		delCookie('district_id');
		delCookie('curr_lng');
		delCookie('curr_lat');
		delCookie('current_area');
		window.location = WapSiteUrl + "/home.html";
	});
	$('.adr-input').on('click',function(){
		window.location = WapSiteUrl + "/tmpl/dhome/change_address.html";
	});
	if(key){
		$.ajax({
			type: 'post',
		    url: ApiUrl + "/index.php?act=dhome_buy&op=address_list",
		    data: {key: key},
		    dataType: 'json',
		    success: function(result) {
		    	if(!result){
		    		result = [];
		    		result.datas = [];
		    		result.datas.address_list = [];
		    	}
		    	data = result.datas;
		    	var count =  data.address_list.length;
		    	if(count > 0){
		    		$('.adr-notext').hide();
		    		var html = template.render('address_list_tpl', data);
		    		$('.adr-list').html(html);
		    		$('.adr-list').show();
		    	}else{
		    		$('.adr-notext').show();
		    	}
		    }
		});
	}
	$('.adr-list').on('click','li',function(){
		changeAddress($(this));
	});
});