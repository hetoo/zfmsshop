/* 购物车JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var key = getCookie('key');
var cart_id = getQueryString('cart_id');
var chain_id = getQueryString("chain_id");
var pay_name = 'online';
var address_id,voucher_t_id,rpacket_t_id;
var city_id,area_id;
var area_info;
var address_list = new Array();
    var transport_rule = '0';
    var transport_areas = '';
    var chain_city_id = 0;
    var chain_area_id = 0;
    var chain_area_info = '';
    // 插入地址数据到html
    function insertHtmlAddress(address_k) {
        address_info = address_list[address_k];
        address_id = address_info.address_id;
        if (address_id) {
            $('#true_name').html(address_info.true_name);
            $('#mob_phone').html(address_info.mob_phone);
            $('#address').html(address_info.area_info + address_info.address);
            area_id = address_info.area_id;
            city_id = address_info.city_id;
            addCookie('address_id',address_id);
            $('#ToBuyStep2').parent().addClass('ok');
        }
    }
    function chainAddress() {
        $.ajax({
            type:'post',
            url:ApiUrl+"/index.php?act=dhome_buy&op=address_list", 
            data:{key:key,chain_id: chain_id},
            dataType:'json',
            async:false,
            success:function(result){
                if($.isEmptyObject(result.datas.address_list)){
                    $('#new-address-valve').click();
                    city_id = chain_city_id;
                    area_id = chain_area_id;
                    area_info = chain_area_info;
                    $('#varea_info').val(area_info);
                    if (getCookie('district_name')) $('#vaddress').val(getCookie('district_name'));
                    return false;
                }
                address_list = result.datas.address_list;
                if (!address_id) {
                    address_id = getCookie('address_id');
                }
                result.datas.address_id = address_id;
                var html = template.render('list-address-add-list-script', result.datas);
                $("#list-address-add-list-ul").html(html);
                var _k = $("li.selected").attr("address_k");
                if (!_k) {
                    _k = $("li[chain_valid='1']").first().attr("address_k");
                }
                insertHtmlAddress(_k);
            }
        });
    }
$(function() {
	if(!key){
		window.location.href = WapSiteUrl+'/tmpl/member/login.html';
		return false;
	}

    // 地址列表
    $('#list-address-valve').click(function(){
        chainAddress();
    });
    $.animationLeft({
        valve : '#list-address-valve',
        wrapper : '#list-address-wrapper',
        scroll : '#list-address-scroll'
    });
    
    // 地址选择
    $('#list-address-add-list-ul').on('click', "li[chain_valid='1']", function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        insertHtmlAddress($(this).attr("address_k"));
        $('#list-address-wrapper').find('.header-l > a').click();
    });
    
    // 地址新增
    $.animationLeft({
        valve : '#new-address-valve',
        wrapper : '#new-address-wrapper',
        scroll : ''
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
    
    template.helper('pf', function(o) {
        return parseFloat(o) || 0;
    });

    template.helper('p2f', function(o) {
        return (parseFloat(o) || 0).toFixed(2);
    });
        var totals = 0;
        // 购买第一步
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=dhome_buy&op=buy_step1',
            dataType:'json',
            async:false,
            data:{key:key,cart_id:cart_id,chain_id:chain_id},
            success:function(result){
                if (result.datas.error) {
                    $.sDialog({
                        skin:"red",
                        content:result.datas.error,
                        okBtn:false,
                        cancelBtn:true,
                        cancelBtnText: '返回',
                        okFn: function() {
                            history.go(-1);
                        },
                        cancelFn: function() {
                            history.go(-1);
                        }
                    });
                    return false;
                }
                // 商品数据
                result.datas.WapSiteUrl = WapSiteUrl;
                var html = template.render('goods_list', result.datas);
                $("#deposit").html(html);
                totals = parseFloat(result.datas.chain_buy_amount);
                // 代金券
                voucher_t_id = '';
                if (result.datas.voucher_info) voucher_t_id = result.datas.voucher_info.voucher_t_id;
                // 红包
                rpacket_t_id = '';
                var rptPrice = 0;
                if (!$.isEmptyObject(result.datas.rpt_info)) {
                    $('#rptVessel').show();
                    var rpt_info = ((parseFloat(result.datas.rpt_info.rpacket_limit) > 0) ? '满' + parseFloat(result.datas.rpt_info.rpacket_limit).toFixed(2) + 
                            '元，': '') + '优惠' + parseFloat(result.datas.rpt_info.rpacket_price).toFixed(2) + '元'
                    $('#rptInfo').html(rpt_info);
                }
                $('#useRPT').click(function(){
                    rpacket_t_id = '';
                    var total_price = totals;
                    if ($(this).prop('checked')) {
                        rpacket_t_id = result.datas.rpt_info.rpacket_t_id;
                        rptPrice = parseFloat(result.datas.rpt_info.rpacket_price);
                        total_price = totals - rptPrice;
                    }
                    $('#totalPrice,#onlineTotal').html(total_price.toFixed(2));
                });
                $('#totalPrice,#onlineTotal').html(totals.toFixed(2));
                transport_rule = result.datas.transport_rule;
                transport_areas = result.datas.transport_areas;
                chain_city_id = result.datas.city_id;
                chain_area_id = result.datas.area_id;
                chain_area_info = result.datas.area_info;
                chainAddress();
            }
        });
    
    // 地址保存
    $.sValid.init({
        rules:{
            vtrue_name:"required",
            vmob_phone:{
                required:true,
                mobile:true
            },
            varea_info:"required",
            vaddress:"required"
        },
        messages:{
            vtrue_name:"姓名必填！",
            vmob_phone:{
                required:"手机号必填！",
                mobile:"手机号不正确"
            },
            varea_info:"地区必填！",
            vaddress:"街道必填！"
        },
        callback:function (eId,eMsg,eRules){
            if(eId.length >0){
                var errorHtml = "";
                $.map(eMsg,function (idx,item){
                    errorHtml += "<p>"+idx+"</p>";
                });
                errorTipsShow(errorHtml);
            }else{
                errorTipsHide();
            }
        }  
    });
    $('#add_address_form').find('.btn').click(function(){
        if($.sValid()){
            var chain_valid = 1;
            if (transport_rule == 1) {
                $.ajax({
                    type:'post',
                    url:ApiUrl+"/index.php?act=dhome_buy&op=buy_address",  
                    data:{chain_id: chain_id, area_info: $('#varea_info').val(), address: $('#vaddress').val()},
                    dataType:'json',
                    async:false,
                    success:function(result){
                        if (result.datas == 0) {
                            errorTipsShow('所填写地址不在该门店的配送范围内');
                            chain_valid = 0;
                        }
                    }
                });
            }
            if (transport_rule == 2 && transport_areas.indexOf(','+area_id+',')<0) {
                errorTipsShow('所选择地区不在该门店的配送范围内');
                chain_valid = 0;
            }
            if (chain_valid == 0) return false;
            var param = {};
            param.key = key;
            param.true_name = $('#vtrue_name').val();
            param.mob_phone = $('#vmob_phone').val();
            param.address = $('#vaddress').val();
            param.city_id = city_id;
            param.area_id = area_id;
            param.area_info = $('#varea_info').val();
            param.is_default = 0;

            $.ajax({
                type:'post',
                url:ApiUrl+"/index.php?act=member_address&op=address_add",  
                data:param,
                dataType:'json',
                async:false,
                success:function(result){
                    if (!result.datas.error) {
                        var _k = address_list.length;
                        param.address_id = result.datas.address_id;
                        address_list[_k] = param;
                        insertHtmlAddress(_k);
                        $('#new-address-wrapper,#list-address-wrapper').find('.header-l > a').click();
                    }
                }
            });
        }
    });
    // 支付
    var buy_step2 = 0;
    $('#ToBuyStep2').click(function(){
        if (buy_step2) {
            $.sDialog({
                skin:"red",
                content:'订单正在处理中，请勿重复点击！',//co_py_right  w_ww_.sh_o_pn_c_.net
                okBtn:false,
                cancelBtn:false
            });
            return false;
        }
            if (!address_id) {
                errorTipsShow('<p>请选择收货地址</p>');
                return false;
            }
        buy_step2 = 1;
        var msg = $('#storeMessage').val();
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=dhome_buy&op=buy_step2',
            data:{
                key:key,
                chain_id:chain_id,
                cart_id:cart_id,
                address_id:address_id,
                voucher_t_id:voucher_t_id,
                rpacket_t_id:rpacket_t_id,
                pay_name:pay_name,
                password:'',
                rcb_pay:0,
                pd_pay:0,
                pay_message:msg
                },
            dataType:'json',
            async:false,
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
                    toPay(result.datas.pay_sn,'member_buy','pay');
            }
        });
    });
});