document.write(unescape("%3Cscript src='https://api.map.baidu.com/api?v=2.0&ak="+BaiDu_AK+"' type='text/javascript'%3E%3C/script%3E"));

var city_id = getCookie('city_id');
var city_name = getCookie('city_name');

function G(id) {
	return document.getElementById(id);
}
function searchTips(){
	var ac = new BMap.Autocomplete({"input" : "suggestId" ,"location" : city_name});
	var myValue;
	ac.addEventListener("onconfirm", function(e) {    //鼠标点击下拉列表后的事件
		var _value = e.item.value;
		setCurrAreaInfo(_value);
	});
}

function setCurrAreaInfo(geo_info){
	delCookie('current_area');
	addCookie('current_area',geo_info.business);
	var address = geo_info.province +  geo_info.city +  geo_info.district +  geo_info.street +  geo_info.business;
	var area_name = geo_info.district;
	$.ajax({
		type: 'get',
	    url: ApiUrl + "/index.php?act=area&op=get_curr_info",
	    data:{address:address,city_id:city_id,area_name:area_name},
	    dataType: 'json',
	    success: function(result) {
	    	var data = result.datas;
	    	delCookie('district_name');
			delCookie('district_id');
			delCookie('curr_lng');
			delCookie('curr_lat');
	    	addCookie('district_name',data.area_name);
			addCookie('district_id',data.area_id);
			addCookie('curr_lng',data.chain_lng);
			addCookie('curr_lat',data.chain_lat);
			location.href = WapSiteUrl + '/dhome.html';
	    }
	});
}

$(function(){
	$('.adr-h2').html(city_name);
	$('.adr-h2').click(function(){
		location.href = WapSiteUrl + "/tmpl/dhome/change_city.html";
	});
	searchTips();
});

