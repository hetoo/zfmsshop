var chain_id = getQueryString("chain_id");
var district_id = getCookie('district_id');
var curr_lng = getCookie('curr_lng');
var curr_lat = getCookie('curr_lat');
var cate_id = getQueryString("cate_id");
var search_base_url = WapSiteUrl + '/tmpl/dhome/store_list.html?chain_lng='+curr_lng+'&chain_lat='+curr_lat+'&district_id='+district_id;

//获取热搜词
function getHotSearch(){
    $.ajax({
        url:ApiUrl + '/index.php?act=dhome_search&op=get_hot',
        type: 'get',
        dataType: 'json',
        success: function(result) {
            var data = result.datas;
            data.WapSiteUrl = WapSiteUrl;
            if(data.list.length > 0){
                $('#hot_list_container').html(template.render('hot_list',data));
            }else{
                $('dl.hot-keyword').hide();
            }            
            $('#search_his_list_container').html(template.render('search_his_list',data));
        }
    });
}

function buildSUrl(val){
    var r_url = search_base_url;
    r_url += '&keyword='+val;
    return r_url;
}

$(function(){
    if(chain_id > 0){
        $('#keywords').attr('placeholder','搜索店内商品');
        document.title = '搜索店内商品';
        search_base_url = WapSiteUrl + '/tmpl/dhome/goods_list.html?chain_id='+chain_id;
    }else if(cate_id > 0){
        $('#keywords').attr('placeholder','搜索附近的商品和门店');
        search_base_url = WapSiteUrl + '/tmpl/dhome/store_list.html?chain_lng='+curr_lng+'&chain_lat='+curr_lat+'&district_id='+district_id+'&cate_id='+cate_id;
    }

    getHotSearch();

	var keyword = decodeURIComponent(getQueryString('keyword'));
    if (keyword) {
    	$('#keywords').val(keyword);
        writeClear($('#keywords'));
    }

    $('#keywords').on('input',function(){
    	var value = $.trim($('#keyword').val());
    	if (value == '') {
    		$('#search_tip_list_container').hide();
    	} else {
            $.getJSON(ApiUrl + '/index.php?act=goods&op=auto_complete',{term:$('#keyword').val()}, function(result) {
            	if (!result.datas.error) {
                	var data = result.datas;
                	data.WapSiteUrl = WapSiteUrl;
                	if (data.list.length > 0) {
                		$('#search_tip_list_container').html(template.render('search_tip_list_script',data)).show();
                	} else {
                		$('#search_tip_list_container').hide();
                	}
            	}
            })
    	}
    });

    $('.input-del').click(function(){
        $(this).parent().removeClass('write').find('input').val('');
    });

    template.helper('$buildUrl',buildSUrl);

    $('#form_search').submit(function(){
        $('#header-nav').click();
        return false;
    });
    $('#header-nav').click(function(){
    	if ($('#keywords').val() != '') {
    		window.location.href = buildSUrl($('#keywords').val());
    	}
    });
});