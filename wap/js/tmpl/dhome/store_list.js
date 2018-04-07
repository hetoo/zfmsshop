var district_id = getCookie('district_id');
var curr_lng = getCookie('curr_lng');
var curr_lat = getCookie('curr_lat');
var keyword = decodeURIComponent(getQueryString('keyword'));
var cate_id = getQueryString('cate_id');

var page = pagesize;
var curpage = 1;
var hasmore = true;

//获取首页数据
function getHomeChainData(){
    if (!hasmore) {    	
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.chain_lng = curr_lng;
    param.chain_lat = curr_lat;
    param.district_id = district_id;
    if(keyword != ''){
    	param.keyword = keyword;
    }
    if(cate_id > 0){
    	param.cate_id = cate_id;
    }
	$.ajax({
		url:ApiUrl + '/index.php?act=dhome_search',
		type: 'get',
		data:param,
		dataType: 'json',
		success: function(result) {
			if(!result) {
                result = [];
                result.datas = [];                
                result.datas.chain_list = [];
            }
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
	$('input.ss-input').val(keyword);
	$('.ss-input').click(function(){
        var jurl = WapSiteUrl + "/tmpl/dhome/search.html?keyword="+keyword;
        if(cate_id > 0){
            jurl +="&cate_id="+cate_id;
        }
		location.href = jurl;
	});
	getHomeChainData();
	//门店自动加载
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            getHomeChainData();
        }
    });
});