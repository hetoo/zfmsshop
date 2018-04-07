/* 门店商品购买JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var shopnc_goods_list = new Array();//购物车所有商品信息
var cart_member_id = getCookie('cart_member_id');
var selected_count = 0;
var selected_total = 0;
var cart_count = 0;
var start_amount_price = 0;
if (cart_member_id && (cart_member_id.indexOf('u') > -1) && key) {
    $.ajax({
        url: ApiUrl + "/index.php?act=dhome_buy&op=merge_cart",
        data: {key: key, cart_member_id: cart_member_id},
        type: "post",
        dataType: "json",
        success: function(result) {
            cart_member_id = result.datas;
            addCookie('cart_member_id',cart_member_id);
        }
    });
}
$(function() {
    get_cart();
});
function get_cart() {
    $.ajax({
        url: ApiUrl + "/index.php?act=dhome_buy&op=index",
        data: {chain_id: chain_id, key: key, cart_member_id: cart_member_id},
        type: "post",
        dataType: "json",
        success: function(result) {
            update_cart(result);
        }
    });
}
function update_cart(result) {
    if(result.datas.cart_count) {
        shopnc_goods_list = result.datas.goods_list;
        var html = template.render('chain_cart_goods', result.datas);
        $("#chain_cart_goods_list").html(html);
        $("#chain_cart_checked").removeClass("checked");
        selected_count = result.datas.selected_count;
        selected_total = result.datas.selected_total;
        cart_count = result.datas.cart_count;
        start_amount_price = result.datas.start_amount_price;
        $("a[cart_goods_show]").html('<i class="sx5">'+cart_count+'</i>');
        if(parseFloat(start_amount_price) > 0) $("#chain_cart_buy").addClass("dib").html(''+start_amount_price+'元起送');
        if(cart_count) {
            $("#chain_cart_total").html('<div>￥'+selected_total+'</div>');
            $("#chain_cart_selected").html('');
            $("a[cart_goods_show='1']").addClass("light");
        }
        if(selected_count) {
            $("#chain_cart_selected").html('(已选'+selected_count+'件)');
            if(selected_count==cart_count) $("#chain_cart_checked").addClass("checked");
            if(parseFloat(selected_total) >= parseFloat(start_amount_price)) {
                $("#chain_cart_buy").removeClass("dib").html('去结算');
            }
        }
        update_goods();
        $('.a3q').height('auto');
        var _h = $(window).height();
        var _cart_h = 180+75*$("li[chain_goods_id]").size();
        if(_cart_h > _h) $('.a3q').height(_h-180);
    }else{
        shopnc_goods_list = new Array();
        cart_count = 0;
        $("#chain_cart_content,#chain_cart_mask").hide();
        $("#chain_cart_buy").addClass("dib");
        $("#chain_cart_total").removeClass("left_total").html('<div class="sx3">购物车是空的</div>');
        $("a[cart_goods_show='1']").removeClass("light").addClass("show").html('');
        $("[chain_goods_id]").find(".reduce").addClass("hide");
        $("[goods_num_id]").addClass("hide").html(0);
    }
}
function update_goods() {
    $.each(shopnc_goods_list,function(k,v){
        var goods_id = v.goods_id;
        var stock = v.stock;
        $("[goods_num_id='"+goods_id+"']").attr("goods_stock",stock);
        if(stock > 0) {
            $("[goods_num_id='"+goods_id+"']").removeClass("hide").html(v.goods_num);
            $("[chain_goods_id='"+goods_id+"']").find(".reduce").removeClass("hide");
        }
    });
}
function cart_buy() {
	if(!key){
		window.location.href = WapSiteUrl+'/tmpl/member/login.html';
		return false;
	}
    if(selected_count > 0 && parseFloat(selected_total) >= parseFloat(start_amount_price)) {
        var cartId = [];
        $.each(shopnc_goods_list,function(k,v){
            var goods_id = v.goods_id;
            var goods_num = v.goods_num;
            if(v.goods_selected > 0) {
                cartId.push(goods_id+"|"+goods_num);
            }
        });
        var cart_id = cartId.toString();
        window.location.href = WapSiteUrl + "/tmpl/dhome/buy_step1.html?chain_id="+chain_id+"&cart_id="+cart_id;
    }
}
function show_cart_checked(goods_id) {
    var cart_check = 0;
    
    if(goods_id) {
        if($("[chain_goods_checked='"+goods_id+"']").hasClass("checked")) {
            cart_check = 0;
            $("[chain_goods_checked='"+goods_id+"']").removeClass("checked");
            $("#chain_cart_checked").removeClass("checked");
        }else{
            cart_check = 1;
            $("[chain_goods_checked='"+goods_id+"']").addClass("checked");
        }
    }else{
        if($("#chain_cart_checked").hasClass("checked")) {
            cart_check = 0;
            $("#chain_cart_checked").removeClass("checked");
            $("[chain_goods_checked]").removeClass("checked");
        }else{
            cart_check = 1;
            $("#chain_cart_checked").addClass("checked");
            $("[chain_goods_checked]").addClass("checked");
        }
    }
    
    $.ajax({
        url: ApiUrl + "/index.php?act=dhome_buy&op=update_cart",
        data: {chain_id: chain_id, key: key, cart_check: cart_check, cart_member_id: cart_member_id, goods_id: goods_id},
        type: "post",
        dataType: "json",
        success: function(result) {
            update_cart(result);
        }
    });
}
function chain_del_cart(goods_id) {
    if(goods_id == 0) {
        $.sDialog({
            content: '确认清空购物车？',
            okFn: function() { _del_cart(goods_id); }
        });
    }else{
        _del_cart(goods_id);
    }
}
function show_cart(n) {
    if(cart_count) {
        $('#chain_cart_content').toggle();
        $('#chain_cart_mask').toggle();
        if(n) {
            $("a[cart_goods_show='1']").removeClass("show");
            $("#chain_cart_total").addClass("left_total");
        }else{
            $("a[cart_goods_show='1']").addClass("show");
            $("#chain_cart_total").removeClass("left_total");
        }
    }
}
function del_cart(goods_id) {
    var obj = $("[chain_goods_id='"+goods_id+"']");
    var obj_num = $("[goods_num_id='"+goods_id+"']").first();
    var _num = parseInt(obj_num.html());
    if(_cart(goods_id,_num-1)) {
        if(_num > 1) {
            obj_num.html(parseInt(_num)-1);
        }else{
            obj_num.html(0).addClass("hide");
            obj.find(".reduce").addClass("hide");
        }
    }
}
function add_cart(goods_id) {
    var obj = $("[chain_goods_id='"+goods_id+"']");
    var obj_num = $("[goods_num_id='"+goods_id+"']").first();/* 版权：天津市网城天创科技有限责任公司	*/
    var _stock = parseInt(obj_num.attr("goods_stock"));
    var _num = parseInt(obj_num.html());
    if(_num < _stock) {
        _cart(goods_id,_num+1);
    }else{
        cart_dialog('商品库存不足');
    }
}
function _cart(goods_id,num) {
    var state = false;
    $.ajax({
        url: ApiUrl + "/index.php?act=dhome_buy&op=add_cart",
        data: {chain_id: chain_id, key: key, cart_member_id: cart_member_id, goods_id: goods_id, num: num},
        type: "post",
        dataType: "json",
        async:false,
        success: function(result) {
            if (result.datas.error) {
                cart_dialog(result.datas.error);
                state = false;
            }else{
                update_cart(result);
                state = true;
                cart_member_id = result.datas.cart_member_id;
                addCookie('cart_member_id',cart_member_id);
            }
        }
    });
    return state;
}
function _del_cart(goods_id) {
    $.ajax({
        url: ApiUrl + "/index.php?act=dhome_buy&op=del_cart",
        data: {chain_id: chain_id, key: key, cart_member_id: cart_member_id, goods_id: goods_id},
        type: "post",
        dataType: "json",
        success: function(result) {
            update_cart(result);
        }
    });
}
function cart_dialog(content) {
    if(content) {
        $.sDialog({
            skin:"red",
            content:content,
            okBtn:false,
            cancelBtn:false
        });
    }
}