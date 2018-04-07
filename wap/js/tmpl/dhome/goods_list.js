var key = getCookie('key');
var chain_id = getQueryString("chain_id");
var keyword = decodeURIComponent(getQueryString('keyword'));

var page = pagesize;
var curpage = 1;
var hasmore = true;

//获取列表
function get_list() {
    $('.loading').remove();
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.chain_id = chain_id;
    if(keyword != ''){
    	param.keyword = keyword;
    }

    $.ajax({
          url: ApiUrl + '/index.php?act=dhome_store&op=goods_list' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          success : function(result){
            if(!result) {
                result = [];
                result.datas = [];                
                result.datas.goods_list = [];
            }
            $('.loading').remove();
            curpage++;
            
            //门店商品
	        var g_html = template.render('chain_goods_tpl', result);
	        $("#goods_list").append(g_html);
            hasmore = result.hasmore;
            update_goods();
          }
    });
}

$(function(){
	$('input.ss-input').val(keyword);
	$('.ss-input').click(function(){
		location.href = WapSiteUrl + "/tmpl/dhome/search.html?chain_id="+chain_id+"&keyword="+keyword;
	});

	get_list();

	//商品自动加载
	$(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });
});
