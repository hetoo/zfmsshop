/* 购物车JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var cartCount = 0;
$(function (){
    template.helper('decodeURIComponent', function(o){
        return decodeURIComponent(o);
    });
    var key = getCookie('key');
    if(!key){
        window.location.href = WapSiteUrl+'/tmpl/member/login.html';
        return;
    }else{
        //初始化页面数据
        function initCartList(){
             $.ajax({
                url:ApiUrl+"/index.php?act=member_points&op=prod_cart",
                type:"post",
                dataType:"json",
                data:{key:key},
                success:function (result){
                    if(checkLogin(result.login)){
                        if(!result.datas.error){
                            cartCount = result.datas.cart_list.length;
                            addCookie('pgoods_cart_count',cartCount);
                            var rData = result.datas;
                            
                            rData.WapSiteUrl = WapSiteUrl;
                            rData.check_out = true;
                            var html = template.render('cart-list', rData);
                            if (rData.cart_list.length == 0) {
                                get_footer();
                            }
                            $("#cart-list-wp").html(html);
                            //删除购物车
                            $(".goods-del").click(function(){
                                var  pc_id = $(this).attr("pc_id");
                                $.sDialog({
                                    skin:"red",
                                    content:'确认删除吗？',
                                    okBtn:true,
                                    cancelBtn:true,
                                    okFn: function() {
                                        delCartList(pc_id);
                                    }
                                });
                            });
                             //购买数量，减
                            $(".minus").click(minusBuyNum);
                            //购买数量加
                            $(".add").click(addBuyNum);
                            $(".buynum").blur(buyNumer);
                            // 从下到上动态显示隐藏内容
                            for (var i=0; i<result.datas.cart_list.length; i++) {
                                $.animationUp({
                                    valve : '.animation-up' + i,          // 动作触发，为空直接触发
                                    wrapper : '.nctouch-bottom-mask' + i,    // 动作块
                                    scroll : '.nctouch-bottom-mask-rolling' + i,     // 滚动块，为空不触发滚动
                                });
                            }
                            // 领店铺代金券
                            $('.nctouch-voucher-list').on('click', '.btn', function(){
                                getFreeVoucher($(this).attr('data-tid'));
                            });
                            $('.store-activity').click(function(){
                                $(this).css('height', 'auto');
                            });
                            calculateTotalPrice();
                        }else{
                           alert(result.datas.error);
                        }
                    }
                }
            });
        }
        initCartList();
        //删除购物车
        function delCartList(pc_id){
            $.ajax({
                url:ApiUrl+"/index.php?act=member_points&op=prod_drop",
                type:"post",
                data:{key:key,pc_id:pc_id},
                dataType:"json",
                success:function (res){
                    if(checkLogin(res.login)){
                        if(!res.datas.error && res.datas == "1"){
                            delCookie('pgoods_cart_count');
                            initCartList();                            
                        }else{
                            alert(res.datas.error);
                        }
                    }
                }
            });
        }
        //购买数量减
        function minusBuyNum(){
            var self = this;
            editQuantity(self,"minus");
        }
        //购买数量加
        function addBuyNum(){
            var self = this;
            editQuantity(self,"add");
        }
        //购买数量增或减，请求获取新的价格
        function editQuantity(self,type){
            var sPrents = $(self).parents(".cart-litemw-cnt");
            var cart_id = sPrents.attr("pc_id");
            var numInput = sPrents.find(".buy-num");
            var goodsPrice = sPrents.find(".goods-price");
            var buynum = parseInt(numInput.val());
            var quantity = 1;
            if(type == "add"){
                quantity = parseInt(buynum+1);
            }else {
                if(buynum >1){
                    quantity = parseInt(buynum-1);
                }else {
                    return false;
                }
            }
            $('.pre-loading').removeClass('hide');
            $.ajax({
                url:ApiUrl+"/index.php?act=member_points&op=update",
                type:"post",
                data:{key:key,cart_id:cart_id,quantity:quantity},
                dataType:"json",
                success:function (res){
                    if(checkLogin(res.login)){
                        if(!res.datas.error){
                            numInput.val(quantity);
                            goodsPrice.html('积分小计<em>'+res.datas.subtotal+'</em>');
                            calculateTotalPrice();
                        }else{
                            $.sDialog({
                                skin:"red",
                                content:res.datas.error,
                                okBtn:false,
                                cancelBtn:false
                            });
                        }
                        $('.pre-loading').addClass('hide');
                    }
                }
            });
        }

        //去结算
        $('#cart-list-wp').on('click', ".check-out > a", function(){
            window.location.href = WapSiteUrl + "/tmpl/member/pointcart_step1.html";
        });

        //验证
        $.sValid.init({
            rules:{
                buynum:"digits"
            },
            messages:{
                buynum:"请输入正确的数字"
            },
            callback:function (eId,eMsg,eRules){
                if(eId.length >0){
                    var errorHtml = "";
                    $.map(eMsg,function (idx,item){
                        errorHtml += "<p>"+idx+"</p>";
                    });
                    $.sDialog({
                        skin:"red",
                        content:errorHtml,
                        okBtn:false,
                        cancelBtn:false
                    });
                }
            }  
        });
        function buyNumer(){
            $.sValid();
        }
    }   
});

function calculateTotalPrice() {
    var totalPrice = parseInt("0");
    $('.cart-litemw-cnt').each(function(){
        totalPrice += parseInt($(this).find('.goods-price').find('em').html());
    });
    $(".total-money").find('em').html(totalPrice);
    check_button();
    return true;
}


function get_footer() {
        footer = true;
        $.ajax({
            url: WapSiteUrl+'/js/tmpl/footer.js',
            dataType: "script"
          });
}

function check_button() {
    var _has = true;
    if (_has) {
        $('.check-out').addClass('ok');
    } else {
        $('.check-out').removeClass('ok');
    }
}