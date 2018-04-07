var goods_id = getQueryString("goods_id");
var chain_id = getQueryString("chain_id");
var key = getCookie('key');

var page = pagesize;
var curpage = 1;
var hasmore = true;

// 图片轮播
function picSwipe() {
    var elem = $("#mySwipe")[0];
    window.mySwipe = Swipe(elem, {
        continuous: false,
        // disableScroll: true,
        stopPropagation: true,
        callback: function (index, element) {
            $('.goods-detail-turn').find('li').eq(index).addClass('cur').siblings().removeClass('cur');
        }
    });
}

//获取商品详细信息
function getGoodsDetail(){
	$.ajax({
		url: ApiUrl + "/index.php?act=dhome_store&op=goods_detail",
        type: "get",
        async:false,
        data: {goods_id: goods_id, chain_id: chain_id},
        dataType: "json",
        success: function (result) {
        	var data = result.datas;
        	if (!data.error) {
        		//详情页轮播图
        		var html = template.render('swipe_list_tpl', data);
				document.title=data.goods_info.goods_name;
            	$(".goods-detail-top").html(html);

            	var dhtml = template.render('goods_detail_tpl', data);
            	$("#goodsEvaluation1").before(dhtml);

            	var eval_info = data.eval_info;
            	$('#goodsEvaluation1 .rate').find('em').html(eval_info.good_percent+"%");
            	$('#goodsEvaluation1 .rate-num').html("（"+eval_info.eval_count+"人评价）");
            	update_goods();
        	}else{
        		$.sDialog({
                    content: data.error + '！<br>请返回上一页继续操作…',
                    okBtn: false,
                    cancelBtnText: '返回',
                    cancelFn: function () {
                        history.back();
                    }
                });
        	}        	
        }
	});
}

//获取商品评论
function getGoodsEvaluate(){
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.goods_id = goods_id;
    param.chain_id = chain_id;

	$.ajax({
		url: ApiUrl + "/index.php?act=dhome_store&op=goods_evaluate",
        type: "get",
        async:false,
        data: param,
        dataType: "json",
        success: function (result) {
        	var data = result.datas;
        	if (!data.error) {
        		curpage++;
            	var dhtml = template.render('goods_evaluate_tpl', data);
            	$(".comment-info").html(dhtml);
            	hasmore = result.hasmore;
        	}
        }
	});
}

$(function(){	
	getGoodsDetail();
	picSwipe();
	getGoodsEvaluate();
    $('.nav-r-box .ss').attr('href',"./store.html?chain_id="+chain_id);
	$(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            getGoodsEvaluate();
        }
    });
});