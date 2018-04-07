/* 购物车JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var key = getCookie('key');
var buyer_id = getQueryString('buyer_id');
// buy_stop2使用变量
var ifcart = getQueryString('ifcart');
if(ifcart==1){
    var cart_id = getQueryString('cart_id');
}else{
    var cart_id = getQueryString("goods_id")+'|'+getQueryString("buynum");
}
var pay_name = 'online';
var address_id,offpay_hash,offpay_hash_batch,voucher,pd_pay,password,fcode='',rcb_pay,rpt,payment_code;
var message = {};
// change_address 使用变量
var city_id,area_id
// 其他变量
var area_info;
var goods_id;
$(function() {
    // 地址列表
    $('#list-address-valve').click(function(){
        $.ajax({
            type:'post',
            url:ApiUrl+"/index.php?act=member_address&op=address_list", 
            data:{key:key},
            dataType:'json',
            async:false,
            success:function(result){
                checkLogin(result.login);
                if(result.datas.address_list==null){
                    return false;
                }
                var data = result.datas;
                data.address_id = address_id;
                var html = template.render('list-address-add-list-script', data);
                $("#list-address-add-list-ul").html(html);
            }
        });
    });
    $.animationLeft({
        valve : '#list-address-valve',
        wrapper : '#list-address-wrapper',
        scroll : '#list-address-scroll'
    });
    
    // 地区选择
    $('#list-address-add-list-ul').on('click', 'li', function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        eval('address_info = ' + $(this).attr('data-param'));
        _init(address_info.address_id);
        $('#list-address-wrapper').find('.header-l > a').click();
    });

    // 地区选择
    $('#new-address-wrapper').on('click', '#varea_info', function(){
        $.areaSelected({
            success : function(data){
                city_id = data.area_id_2 == 0 ? data.area_id_1 : data.area_id_2;
                area_id = data.area_id;
                area_info = data.area_info;
                $('#varea_info').val(data.area_info);
            }
        });
    });
    
    
    template.helper('isEmpty', function(o) {
        var b = true;
        $.each(o, function(k, v) {
            b = false;
            return false;
        });
        return b;
    });

    var _init = function (address_id) {
        var totals = 0;
        // 购买第一步 提交
        $.ajax({//提交订单信息
            type:'post',
            url:ApiUrl+'/index.php?act=member_points&op=pointcart_step1',
            dataType:'json',
            data:{key:key,address_id:address_id},
            success:function(result){
                checkLogin(result.login);
                if (result.datas.error) {
                    alert(result.datas.error);
                    window.location.href = WapSiteUrl + '/tmpl/points_shop.html';
                }
                // 商品数据
                result.datas.WapSiteUrl = WapSiteUrl;
                var html = template.render('goods_list', result.datas.pointprod_arr);
                $("#deposit").html(html);

                // 默认地区相关
                if ($.isEmptyObject(result.datas.address_info)) {
                    $.sDialog({
                        skin:"block",
                        content:'请添加地址',
                        okFn: function() {
                            window.location.href = WapSiteUrl + '/tmpl/member/address_opera.html?pointcart_step1=1';
                        },
                        cancelFn: function() {
                            history.go(-1);
                        }
                    });
                    return false;
                }
                // 输入地址数据
                insertHtmlAddress(result.datas.address_info);
                
                //所需总积分
                $('#totalPrice,#onlineTotal').html(result.datas.pointprod_arr.pgoods_pointall);

                $('#ToBuyStep2').parent().addClass('ok');
            }
        });
    }
    
    // 初始化
    _init();

    // 插入地址数据到html
    var insertHtmlAddress = function (address_info) {
        address_id = address_info.address_id;
        $('#true_name').html(address_info.true_name);
        $('#mob_phone').html(address_info.mob_phone);
        $('#address').html(address_info.area_info + address_info.address);
        area_id = address_info.area_id;
        city_id = address_info.city_id;
    }

    
    // 兑换
    var buy_step2 = 0;
    $('#ToBuyStep2').click(function(){
        if (buy_step2) {
            $.sDialog({
                skin:"red",
                content:'订单正在处理中，请勿重复点击！',
                okBtn:false,
                cancelBtn:false
            });
            return false;
        }
        buy_step2 = 1;
        var msg = '';
        msg = $('#storeMessage').val();
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=member_points&op=pointcart_step2',
            data:{
                key:key,
                address_options:address_id,
                pcart_message:msg
                },
            dataType:'json',
            success: function(result){
                checkLogin(result.login);
                if (result.datas.error) {
                    $.sDialog({
                        skin:"red",
                        content:result.datas.error,
                        okBtn:false,
                        cancelBtn:false
                    });
                    buy_step2 = 0;
                    return false;
                }
                alert('您的兑换订单已成功生成 兑换积分：'+result.datas.point_allpoint);
                window.location.href = WapSiteUrl + '/tmpl/member/point_order.html';
            }
        });
    });
});