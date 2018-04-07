var C = {};
var key = getQueryString('key');
var username = getQueryString('username');
if (key != '') {    
    addCookie('key', key);
} else {
    key = getCookie('key');
}
if (username != '') {    
    addCookie('username', username);
} else {
    username = getCookie('key');
}

C.current_area = getCookie('current_area');
C.city_name = getCookie('city_name');
C.city_id = getCookie('city_id');
C.district_name = getCookie('district_name');
C.district_id = getCookie('district_id');
C.curr_lng = getCookie('curr_lng');
C.curr_lat = getCookie('curr_lat');

var page = pagesize;
var curpage = 1;
var hasmore = true;

//获取首页门店数据
function getHomeChainData(){
	$('.loading').remove();
    if (!hasmore) {    	
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.chain_lng = C.curr_lng;
    param.chain_lat = C.curr_lat;
    param.district_id = C.district_id;
	$.ajax({
		url:ApiUrl + '/index.php?act=dhome&op=chain_list',
		type: 'get',
		data:param,
		dataType: 'json',
		success: function(result) {
			if(!result) {
                result = [];
                result.datas = [];                
                result.datas.chain_list = [];
            }
            $('.loading').remove();
            curpage++;
            
            //门店及商品
	        var html = template.render('chain_list_tpl', result);
	        $("#chain_list").append(html);

	        hasmore = result.hasmore;
	        if(hasmore){
	        	$('#chain_list').after('<div class="dp-more mr"><i></i><span>继续滑动查看更多内容</span></div>');
	        }else{
                $('.dp-more-shop').show();
            }
		}
	});
}


$(function(){
    if(C.current_area == null){
        location.href = WapSiteUrl + "/home.html";
    }

	$('a.location').find('span').html(C.current_area);

	$('span.search-ipt').click(function(){
		location.href = WapSiteUrl + "/tmpl/dhome/search.html";
	});

    $.ajax({
        url:ApiUrl + '/index.php?act=dhome&op=chain_swipe',
        type: 'get',
        dataType: 'json',
        success: function(result) {
            if(!result) {
                result = [];
                result.datas = [];
            }           
            //首页轮播
            var item = result.datas;
            $.each(item,function(k,v){
                $.each(v,function(kk,vv){
                    vv.url = buildDHomeUrl(vv.type, vv.data);
                });                
            });
            var html = template.render('adv_list_tpl', item);
            $("#slider .swipe-wrap").html(html);
            $('#slider').each(function(){
                if ($(this).find('.swiper-slide').length < 2) {
                    return;
                }
                Swipe(this, {
                    startSlide: 2,
                    speed: 400,
                    auto: 3000,
                    continuous: true,
                    disableScroll: false,
                    stopPropagation: false,
                    callback: function(index, elem) {},
                    transitionEnd: function(index, elem) {}
                });
            });
        }
    });

	getHomeChainData();

    //门店自动加载
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            getHomeChainData();
        }
    });

});