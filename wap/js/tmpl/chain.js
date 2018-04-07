var key = getCookie('key');
var chain_id = getQueryString("chain_id");

var page = pagesize;
var curpage = 1;
var hasmore = true;
$(function(){
	//加载店铺详情
	$.ajax({
	    type: 'post',
	    url: ApiUrl + "/index.php?act=chain",
	    data: {chain_id: chain_id},
	    dataType: 'json',
	    success: function(result) {
	        var data = result.datas;
	        //显示页面title
	        var title = data.chain_info.chain_name + ' - 门店首页';
	        document.title = title;
	        var chain_info = data.chain_info;

	        //门店banner
	        $('#chain_banner').find('img').attr('src',chain_info.chain_banner);
	        //门店详情	        
	        var html = '<div class="store-avatar"><img src="'+chain_info.chain_img+'"></div>';
	        html += '<div class="detail-item02">';
	        html += '<dl class="discount"><dt class="discount-text">店铺名称：</dt><dd class="discount-address"><span>'+chain_info.chain_name+'</span></dd></dl>';
	        html += '<dl class="discount"><dt class="discount-text">门店地址：</dt><dd class="discount-address"><span>'+chain_info.area_info + chain_info.chain_address+'</span></dd></dl>';
	        html += '<dl class="discount"><dt class="discount-text">联系电话：</dt><dd class="discount-address"><span>'+chain_info.chain_phone+'</span></dd></dl>';
	        html += '<dl class="discount"><dt class="discount-text">营业时间：</dt><dd class="discount-time"><span>'+chain_info.chain_opening_hours+'</span></dd></dl>';
	        html += '</div>';
	        $('.chain_info').html(html);

	        //门店优惠券
	        voucher_list = data.voucher_list;
	        if(voucher_list.length > 0){
	        	var _sample = '';
	        	for(i=0;i<voucher_list.length;i++){
	        		if(i>1)break;
	        		_sample += '<li><span class="coupon"></span> <span class="coupon-name">满'+voucher_list[i].voucher_t_limit+'减'+voucher_list[i].voucher_t_price+'</span> <span class="coupon coupon-right"></span></li>';
	        	}
	        	$('#show_voucher').find('.detail-coupon').html(_sample);

	        	var v_html = template.render('chain_voucher_tpl', data);
	        	$("#voucher_list").html(v_html);
	        }else{
	        	$('#show_voucher').hide();
	        }
	    }
	});
	//加载门店商品
	get_list();

	//商品自动加载
	$(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });

    //领取代金券
    $('#voucher_list').on('click','span.goto-receive',function(){
    	if(!key){
    		window.location.href = WapSiteUrl+'/tmpl/member/login.html';
        	return;
    	}else{
    		var tid = parseInt($(this).parents('li').attr('t_id'));
    		$.ajax({
			    type: 'post',
			    url: ApiUrl + "/index.php?act=chain&op=get_voucher",
			    data: {key:key, tid:tid},
			    dataType: 'json',
			    success: function(result) {
			    	if(!result.datas.error){
						var p_html = '兑换成功';
		                $.sDialog({
		                    skin:"red",
		                    content:p_html,
		                    okBtn:false,
		                    cancelBtn:true,
		                    cancelBtnText: "确定",
		                });
					}else{
						$.sDialog({
		                    skin:"red",
		                    content:result.datas.error,
		                    okBtn:false,
		                    cancelBtn:true,
		                    cancelBtnText: "确定",
		                });
					}
			    }
			});
    	}
    });

    //立即购买
    $("#product_list").on('click','div.buy-now',function(){
    	var obj = $(this);
    	var goods_storage = parseInt(obj.attr('data-storage')) || 0;
	    var goods_id = parseInt(obj.parents('li').attr('goods_id'));
    	cart_buy(goods_storage,goods_id);
    });
});


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

    $.ajax({
          url: ApiUrl + '/index.php?act=chain&op=goods_list' + window.location.search.replace('?','&'),
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
	        $("#product_list").append(g_html);

            hasmore = result.hasmore;
          }
    });
}

//立即购买
function cart_buy(goods_storage,goods_id) {
    if (!key) {
        window.location.href = WapSiteUrl + '/tmpl/member/login.html';
    } else {
	    if (goods_storage < 1) {
	        $.sDialog({
	            skin: "red",
	            content: '库存不足！',
	            okBtn: false,
	            cancelBtn: false
	        });
	        return;
	    }
	    var json = {};
	    json.key = key;
	    json.cart_id = goods_id + '|1';
	    $.ajax({
	        type: 'post',
	        url: ApiUrl + '/index.php?act=member_buy&op=buy_step1',
	        data: json,
	        dataType: 'json',
	        success: function (result) {
	            if (result.datas.error) {
	                $.sDialog({
	                    skin: "red",
	                    content: result.datas.error,
	                    okBtn: false,
	                    cancelBtn: false
	                });
	            } else {
	                var u = WapSiteUrl + '/tmpl/order/buy_step1.html?goods_id=' + goods_id + '&buynum=1';
	                u += '&ifchain=1&chain_id=' + chain_id;
	                location.href = u;
	            }
	        }
	    });
    }
}