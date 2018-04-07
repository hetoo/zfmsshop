var page = pagesize;
var curpage = 1;
var hasmore = true;
var footer = false;
var flash_id = getQueryString("flash_id");
var info_stat = true;

$(function(){
	get_list();
	
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });
});


function get_list() {
    $('.loading').remove();
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.flash_id = flash_id;

    $.ajax({
          url: ApiUrl + '/index.php?act=flash&op=flash_detial' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          async: false,
          success : function(result){
        	if(!result) {
        		result = [];
        		result.datas = [];
        		result.datas.goods_list = [];
        	}
            $('.loading').remove();
            curpage++;            
            if(info_stat){
            	var flash_info = result.datas.flash_info;
				var t_html = '<img src="'+flash_info.flash_banner_url+'" style="animation: fade 400ms 0s;">';
				var obj = $('.nctouch-banner');
				obj.prepend(t_html);

				var down_obj = $('.time-box').find('.time-inner');
				down_obj.attr('count_down',flash_info.to_time);
				takeCount();
				info_stat = false;
            }
            var html = template.render('home_body', result);
            $(".goods-list").append(html);
            hasmore = result.hasmore;
          }
    });
}

function takeCount() {
	setTimeout("takeCount()", 1000);
	var obj = $('.time-box').find('.time-inner');
	var tms = obj.attr("count_down");
	if (tms>0) {
	    tms = parseInt(tms)-1;
        var days = Math.floor(tms / (1 * 60 * 60 * 24));
        var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
        var minutes = Math.floor(tms / (1 * 60)) % 60;
        var seconds = Math.floor(tms / 1) % 60;
        if (days < 0) days = 0;
        if (hours < 0) hours = 0;
        if (minutes < 0) minutes = 0;
        if (seconds < 0) seconds = 0;
        obj.find("[time_id='d']").html(days);
        obj.find("[time_id='h']").html(hours);
        obj.find("[time_id='m']").html(minutes);
        obj.find("[time_id='s']").html(seconds);
        obj.attr("count_down",tms);
	}
}