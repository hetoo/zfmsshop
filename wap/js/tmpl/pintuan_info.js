/* 拼团JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
$(function() {
    var pintuan_id = getQueryString("pintuan_id");
    var buyer_id = getQueryString("buyer_id");
    var key = getCookie('key');
    loadJs("https://res.wx.qq.com/open/js/jweixin-1.2.0.js");
    $.ajax({
        url: ApiUrl + "/index.php?act=pintuan&op=info",
        data: {key: key, pintuan_id: pintuan_id,buyer_id: buyer_id},
        type: "get",
        dataType: "json",
        success: function(result) {
            var html = template.render('product_detail', result.datas.pintuan_info);
            $("#product_detail_html").html(html);
            html = template.render('puzzle-btn', result.datas.pintuan_info);
            $("#btn_html").html(html);
            var _info = result.datas.pintuan_info;
            document.title=_info.goods_name;
            takeCount();
    	    var _str = location.href+'@@@'+_info.goods_name+'@@@'+_info.goods_image_url+'@@@'+_info.pintuan_name;//'分享链接@@@分享标题@@@分享图标@@@分享描述'
            $.ajax({
                url: ApiUrl + "/index.php?act=wx_share&str="+encodeURIComponent(_str),
                dataType: 'script',
                success: function (result) {
                }
            });
        }
    });
});
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".puzzle-Countdown").each(function(){
	        var obj = $(this);
	        var tms = obj.attr("count_down");
	        if (tms>0) {
	            tms = parseInt(tms)-1;
                var hours = Math.floor(tms / (1 * 60 * 60)) % 24;
                var minutes = Math.floor(tms / (1 * 60)) % 60;
                var seconds = Math.floor(tms / 1) % 60;

                if (hours < 0) hours = 0;
                if (minutes < 0) minutes = 0;
                if (seconds < 0) seconds = 0;
                obj.find("[time_id='h']").html(hours);
                obj.find("[time_id='m']").html(minutes);
                obj.find("[time_id='s']").html(seconds);
                obj.attr("count_down",tms);
	        }
	    });
	}