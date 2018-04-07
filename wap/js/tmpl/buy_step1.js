/* 购物车JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var key = getCookie('key');
var pintuan = getQueryString('pintuan');
var log_id = getQueryString('log_id');
var buyer_id = getQueryString('buyer_id');
// buy_stop2使用变量
var ifcart = getQueryString('ifcart');
if(ifcart==1){
    var cart_id = getQueryString('cart_id');
}else{
    var cart_id = getQueryString("goods_id")+'|'+getQueryString("buynum");
}
var ifchain = getQueryString('ifchain');
var pay_name = 'online';
var invoice_id = 0;
var address_id,vat_hash,offpay_hash,offpay_hash_batch,voucher,pd_pay,password,fcode='',rcb_pay,rpt,payment_code;
var message = {};
// change_address 使用变量
var freight_hash,city_id,area_id
// 其他变量
var area_info;
var goods_id;
var ifshow_offpay = 0;
var ifshow_inv = 0;
var chain_id = getQueryString("chain_id");
var chain_buyer_name = '';
var chain_mob_phone = '';
var chain_store_id = 0;
var shopnc_init = 0;
var chain_goods_id = getQueryString("goods_id");
var chain_list = new Array();
var shopnc_voucher = '';
var shopnc_voucher_price = 0;
var shopnc_totals_price = 0;
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
    
    // 地址选择
    $('#list-address-add-list-ul').on('click', 'li', function(){
        $(this).addClass('selected').siblings().removeClass('selected');
        eval('address_info = ' + $(this).attr('data-param'));
        _init(address_info.address_id);
        $('#list-address-wrapper').find('.header-l > a').click();
    });
    
    // 地址新增
    $.animationLeft({
        valve : '#new-address-valve',
        wrapper : '#new-address-wrapper',
        scroll : ''
    });
    // 支付方式
    $.animationLeft({
        valve : '#select-payment-valve',
        wrapper : '#select-payment-wrapper',
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
    // 门店地区选择
    $.animationLeft({
        valve : '#list-chain-valve',
        wrapper : '#new-chain-wrapper',
        scroll : ''
    });
    $('#chain-list').on('click', 'li', function(){
        $(this).addClass('selected').siblings().removeClass('selected');
    });
    $('#new-chain-wrapper').on('click', '#chain_area_info', function(){
        $.areaSelected({
            success : function(data){
                $('#chain_area_info').val(data.area_info);
                var chain_area_id = data.area_id;
                $.ajax({
                    type:'post',
                    url:ApiUrl+"/index.php?act=member_buy&op=load_chain", 
                    data:{key:key,area_id:chain_area_id,goods_id:chain_goods_id},
                    dataType:'json',
                    async:false,
                    success:function(result){
                        checkLogin(result.login);
                        if(result.datas.chain_list==null){
                            return false;
                        }
                        var html = template.render('list-chain-script', result.datas);
                        $("#chain-list").html(html);
                        chain_list = result.datas.chain_list;
                        if(chain_list.length == 0) $("#chain-list").html('<li><dt style="padding:0.65rem 0 0 0.65rem">该地区没有门店</dt></li>');
                    }
                });
                
            }
        });
    });
    $('#new-chain-wrapper .btn-l').click(function(){
        var errorHtml = "";
        
        chain_buyer_name = $('#chain_input_name').val();
        chain_mob_phone = $('#chain_input_phone').val();
        if(chain_buyer_name == '') errorHtml += "<p>请填写收货人</p>";
        if(chain_mob_phone == '' || !(/^(1{1})+\d{10}$/.test(chain_mob_phone))) errorHtml += "<p>请填写正确的手机号</p>";
        
        var obj = $('#chain-list').find('.selected');
        if(obj.size() == 0) errorHtml += "<p>请选择门店</p>";
        if(errorHtml) {
            errorTipsShow(errorHtml);
            return ;
        }
        chain_id = obj.attr("chain_id");
        $("#chain_buyer_name").html(chain_buyer_name);
        $("#chain_mob_phone").html(chain_mob_phone);
        $("#chain_address").html('[门店]'+obj.find("dt span").html()+' '+obj.find("dd span").html());
        $('#new-chain-wrapper').find('.header-l > a').click();
        $('#receive-chain').attr("chain_id",chain_id);
        _init(0);
    });

    // 发票
    $.animationLeft({
        valve : '#invoice-valve',
        wrapper : '#invoice-wrapper',
        scroll : ''
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

    var _init = function (address_id) {
        var totals = 0;
        shopnc_totals_price = 0;
        shopnc_init = 0;
        // 购买第一步 提交
        $.ajax({//提交订单信息
            type:'post',
            url:ApiUrl+'/index.php?act=member_buy&op=buy_step1',
            dataType:'json',
            data:{key:key,cart_id:cart_id,ifcart:ifcart,ifchain:ifchain,chain_id:chain_id,address_id:address_id,pintuan:pintuan},
            success:function(result){
                checkLogin(result.login);
                if (result.datas.error) {
                    $.sDialog({
                        skin:"red",
                        content:result.datas.error,
                        okBtn:false,
                        cancelBtn:false
                    });
                    return false;
                }
                // 商品数据
                result.datas.WapSiteUrl = WapSiteUrl;
                shopnc_init = 1;
                shopnc_voucher_price = 0;
                var html = template.render('goods_list', result.datas);
                $("#deposit").html(html);
                if (typeof(result.datas.ifshow_offpay) != 'undefined') {
                    ifshow_offpay = result.datas.ifshow_offpay;
                }
                if (fcode == '') {
                    // F码商品
                    for (var k in result.datas.store_cart_list) {
                        if (result.datas.store_cart_list[k].goods_list[0].is_fcode == '1') {
                            $('#container-fcode').removeClass('hide');
                            goods_id = result.datas.store_cart_list[k].goods_list[0].goods_id;
                        }
                        break;
                    }
                }
                // 验证F码
                $('#container-fcode').find('.submit').click(function(){
                    fcode = $('#fcode').val();
                    if (fcode == '') {
                        $.sDialog({
                            skin:"red",
                            content:'请填写F码',
                            okBtn:false,
                            cancelBtn:false
                        });
                        return false;
                    }
                    $.ajax({//提交订单信息
                        type:'post',
                        url:ApiUrl+'/index.php?act=member_buy&op=check_fcode',
                        dataType:'json',
                        data:{key:key,goods_id:goods_id,fcode:fcode},
                        success:function(result){
                            if (result.datas.error) {
                                $.sDialog({
                                    skin:"red",
                                    content:result.datas.error,
                                    okBtn:false,
                                    cancelBtn:false
                                });
                                return false;
                            }

                            $.sDialog({
                                autoTime:'500',
                                skin:"green",
                                content:'验证成功',
                                okBtn:false,
                                cancelBtn:false
                            });
                            $('#container-fcode').addClass('hide');
                        }
                    });
                });
                // 默认地区相关
                if ($.isEmptyObject(result.datas.address_info) && ifchain == '') {
                    $.sDialog({
                        skin:"block",
                        content:'请添加地址',
                        okFn: function() {
                            $('#new-address-valve').click();
                        },
                        cancelFn: function() {
                            history.go(-1);
                        }
                    });
                    return false;
                }
                
                if (typeof(result.datas.ifshow_inv) != 'undefined') {
                    ifshow_inv = result.datas.ifshow_inv;
                }
                if (ifshow_inv == '1') {
                    $('#invoice-valve').parent().removeClass('hide');
                    if (typeof(result.datas.inv_info.inv_id) != 'undefined') {
                    invoice_id = result.datas.inv_info.inv_id;
                    }
                    // 发票
                    $('#invContent').html(result.datas.inv_info.content);
                }
                vat_hash = result.datas.vat_hash;
                
                freight_hash = result.datas.freight_hash;
                // 输入地址数据
                insertHtmlAddress(result.datas.address_info, result.datas.address_api);
                
                if (result.datas.ifchain == '1') {//商品支持门店自提
                    $('#receive-valve').parent().removeClass('hide');
                    chain_store_id = result.datas.chain_store_id;
                    var c_id = getQueryString('chain_id');
                    if (ifchain == '1' && c_id > 0 && typeof(result.datas.chain_info) != 'undefined') {//初始化门店地址
                        $('#storeFreight' + chain_store_id).html('0.00');
                        if(chain_buyer_name == '') $('#receive-chain').click();
                        var chain_info = result.datas.chain_info;
                        $('#chain_area_info').val(chain_info.area_info);
                        chain_list[0] = chain_info;
                        result.datas.chain_list = chain_list;
                        var html = template.render('list-chain-script', result.datas);
                        $("#chain-list").html(html);
                    }
                }
                // 代金券
                voucher = '';
                voucher_temp = [];
                for (var k in result.datas.store_cart_list) {
                    voucher_temp.push([result.datas.store_cart_list[k].store_voucher_info.voucher_t_id + '|' + k + '|' + result.datas.store_cart_list[k].store_voucher_info.voucher_price]);
                    if (result.datas.ifchain == '1') {
                        if (result.datas.store_cart_list[k].store_voucher_info.voucher_price) shopnc_voucher_price = result.datas.store_cart_list[k].store_voucher_info.voucher_price;
                        shopnc_totals_price = parseFloat(result.datas.store_cart_list[k].store_goods_total) +parseFloat(shopnc_voucher_price);
                    }
                }
                voucher = voucher_temp.join(',');
                shopnc_voucher = voucher;

                for (var k in result.datas.store_final_total_list) {
                    // 总价
                    $('#storeTotal' + k).html(result.datas.store_final_total_list[k]);
                    totals += parseFloat(result.datas.store_final_total_list[k]);
                    // 留言
                    message[k] = '';
                    $('#storeMessage' + k).on('change', function(){
                        message[k] = $(this).val();
                    });
                }

                // 红包
                rcb_pay = 0;
                rpt = '';
                var rptPrice = 0;
                if (!$.isEmptyObject(result.datas.rpt_info)) {
                    $('#rptVessel').show();
                    var rpt_info = ((parseFloat(result.datas.rpt_info.rpacket_limit) > 0) ? '满' + parseFloat(result.datas.rpt_info.rpacket_limit).toFixed(2) + 
                            '元，': '') + '优惠' + parseFloat(result.datas.rpt_info.rpacket_price).toFixed(2) + '元'
                    $('#rptInfo').html(rpt_info);
                    rcb_pay = 1;
                    if ($('#useRPT').prop('checked')) {
                        rpt = result.datas.rpt_info.rpacket_t_id+ '|' +parseFloat(result.datas.rpt_info.rpacket_price);
                        rptPrice = parseFloat(result.datas.rpt_info.rpacket_price);
                    }
                } else {
                    $('#rptVessel').hide();
                }
                

                
                password = '';

                $('#useRPT').click(function(){
                    if ($(this).prop('checked')) {
                        rpt = result.datas.rpt_info.rpacket_t_id+ '|' +parseFloat(result.datas.rpt_info.rpacket_price);
                        rptPrice = parseFloat(result.datas.rpt_info.rpacket_price);
                        var total_price = totals - rptPrice;
                    } else {
                        rpt = '';
                        var total_price = totals;
                    }
                    if (total_price <= 0) {
                        total_price = 0;
                    }
                    $('#totalPrice,#onlineTotal').html(total_price.toFixed(2));
                });

                // 计算总价
                var total_price = totals - rptPrice;
                if (total_price <= 0) {
                    total_price = 0;
                }
                $('#totalPrice,#onlineTotal').html(total_price.toFixed(2));
                shopnc_init = 0;
            }
        });
    }
    
    rcb_pay = 0;
    pd_pay = 0;
    // 初始化
    _init();

    // 插入地址数据到html
    var insertHtmlAddress = function (address_info, address_api) {
        address_id = address_info.address_id;
        if (address_id) {
            $('#true_name').html(address_info.true_name);
            $('#mob_phone').html(address_info.mob_phone);
            $('#address').html(address_info.area_info + address_info.address);
            area_id = address_info.area_id;
            city_id = address_info.city_id;
        }
        
        if (address_api.content) {
            for (var k in address_api.content) {
                $('#storeFreight' + k).html(parseFloat(address_api.content[k]).toFixed(2));
            }
        }
        offpay_hash = address_api.offpay_hash;
        offpay_hash_batch = address_api.offpay_hash_batch;
        if (address_api.allow_offpay == 1 && ifshow_offpay) {
            $('#payment-offline').show();
        } else {
            $('#payment-offline').hide();
            pay_name = 'online';
            $('#select-payment-valve').find('.current-con').html('在线支付');
        }
        if (!$.isEmptyObject(address_api.no_send_tpl_ids)) {
            $('#ToBuyStep2').parent().removeClass('ok');
            for (var i=0; i<address_api.no_send_tpl_ids.length; i++) {
                $('.transportId' + address_api.no_send_tpl_ids[i]).show();
            }
        } else {
            $('#ToBuyStep2').parent().addClass('ok');
        }
    }
    
    // 支付方式选择
    // 在线支付
    $('#payment-online').click(function(){
        pay_name = 'online';
        $('#select-payment-wrapper').find('.header-l > a').click();
        $('#select-payment-valve').find('.current-con').html('在线支付');
        $(this).addClass('sel').siblings().removeClass('sel');
    })
    // 货到付款
    $('#payment-offline').click(function(){
        pay_name = 'offline';
        $('#select-payment-wrapper').find('.header-l > a').click();
        $('#select-payment-valve').find('.current-con').html('货到付款');
        $(this).addClass('sel').siblings().removeClass('sel');
    })
    // 门店支付
    $('#payment-chain').click(function(){
        pay_name = 'chain';
        $('#select-payment-wrapper').find('.header-l > a').click();
        $('#select-payment-valve').find('.current-con').html('门店支付');
        $(this).addClass('sel').siblings().removeClass('sel');
    })
    
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
                success:function(result){
                    if (!result.datas.error) {
                        param.address_id = result.datas.address_id;
                        _init(param.address_id);
                        $('#new-address-wrapper,#list-address-wrapper').find('.header-l > a').click();
                    }
                }
            });
        }
    });
    //选择配送方式
    $('#receive-wrapper .sel-con a').click(function(){
        $(this).addClass('sel').siblings().removeClass('sel');
    });
    $('.nctouch-receive-list input').click(function(){
        ifchain = $(this).val();
        if (ifchain == '1') {
            $('#cart-address').hide();
            $('#chain-address').show();
            $('#payment-chain').show();
            $('#storeFreight' + chain_store_id).html('0.00');
            if (chain_id == '') {
                $('#list-chain-valve').click();
            }
            if (shopnc_init == 1) {//初始化
                $('#list-chain-valve').click();
                chain_id = '';
            }
            if ($('#receive-chain').attr("chain_id")) {
                chain_id = $('#receive-chain').attr("chain_id");
                _init(0);
            }
        } else {
            $('#chain-address').hide();
            $('#payment-chain').hide();
            $('#cart-address').show();
            if (address_id) {
                _init(address_id);
            } else {
                $('#list-address-valve').click();
            }
            shopnc_init = 0;
        }
    });
    // 发票选择
    $('#invoice-noneed').click(function(){
        $(this).addClass('sel').siblings().removeClass('sel');
        $('#invoice_add,#invoice-list-scroll').hide();
        invoice_id = 0;
    });
    $('#invoice-need').click(function(){
        $(this).addClass('sel').siblings().removeClass('sel');
        $('#invoice-list-scroll').show();
        $.ajax({//获取发票内容
            type:'post',
            url:ApiUrl+'/index.php?act=member_invoice&op=invoice_content_list',
            data:{key:key},
            dataType:'json',
            success:function(result){
                checkLogin(result.login);
                var data = result.datas;
                var html = '';
                $.each(data.invoice_content_list,function(k,v){
                    html+='<option value="'+v+'">'+v+'</option>';
                });
                $('#inc_content').append(html);
            }
        });
        //获取发票列表
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=member_invoice&op=invoice_list',
            data:{key:key},
            dataType:'json',
            success:function(result){
                checkLogin(result.login);
                var html = template.render('invoice-list-script', result.datas);
                $('#invoice-list').html(html);
                var invoice_scroll = new IScroll('#invoice-list-scroll', { mouseWheel: true, click: true });
                if (result.datas.invoice_list.length > 0) {
                    invoice_id = result.datas.invoice_list[0].inv_id;
                }
                $('.del-invoice').click(function(){
                    var $this = $(this);
                    var inv_id = $(this).attr('inv_id');
                    $.ajax({
                        type:'post',
                        url:ApiUrl+'/index.php?act=member_invoice&op=invoice_del',
                        data:{key:key,inv_id:inv_id},
                        success:function(result){
                            if(result){
                                $this.parents('label').remove();
                            }
                            return false;
                        }
                    });
                });
            }
        });
    })
    // 发票类型选择
    $('input[name="inv_title_select"]').click(function(){
        if ($(this).val() == 'person') {
            $('#inv-title-li').hide();
        } else {
            $('#inv-title-li').show();
        }
    });
    $('#invoice-div').on('click', '#invoiceNew', function(){
        invoice_id = 0;
        $('#invoice_add').show();
        $('#invoice-list-scroll').hide();
    });
    $('#invoice-list').on('click', 'label', function(){
        invoice_id = $(this).find('input').val();
        $('#invoice_add').hide();
    });
    // 发票添加
    $('#invoice-div').find('.btn-l').click(function(){
        if ($('#invoice-need').hasClass('sel')) {
            if (invoice_id == 0) {
                var param = {};
                param.key = key;
                param.inv_title_select = $('input[name="inv_title_select"]:checked').val();
                param.inv_title = $("input[name=inv_title]").val();
                param.company_code = $("input[name=company_code]").val();
                param.inv_content = $('select[name=inv_content]').val();
                if (param.inv_title_select == 'person') {
                    param.inv_title = '个人';
                } else {
                    if (param.inv_title=='') {
                        errorTipsShow('<p>请输入单位或企业名称</p>');
                        return false;
                    }
                    if (param.company_code=='') {
                        errorTipsShow('<p>请输入纳税人识别号</p>');
                        return false;
                    }
                }
                $.ajax({
                    type:'post',
                    async:false,
                    url:ApiUrl+'/index.php?act=member_invoice&op=invoice_add',
                    data:param,
                    dataType:'json',
                    success:function(result){
                        if(result.datas.inv_id>0){
                            invoice_id = result.datas.inv_id;
                            $('#invoiceNew').before('<label><i></i><input type="radio" name="invoice" value="'+invoice_id+
                            '"/><span id="inv_'+invoice_id+'">'+param.inv_title+'&nbsp;&nbsp;'+param.inv_content+
                            '</span><a class="del-invoice" href="javascript:void(0);" inv_id="'+invoice_id+'"></a></label>');
                        }
                    }
                });
                $('#invContent').html(param.inv_title + ' ' + param.company_code + ' ' + param.inv_content);
            } else {
                $('#invContent').html($('#inv_'+invoice_id).html());
            }
        } else {
            $('#invContent').html('不需要发票');
        }
        $('#invoice-wrapper').find('.header-l > a').click();
    });

    
    // 支付
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
        if (ifchain == '1') {
            if (chain_id == '') {
                errorTipsShow('<p>请选择门店</p>');//co_py_right  w_ww_.sh_o_pn_c_.net
                return false;
            }
        } else {
            chain_id = '';
            if (!address_id) {
                errorTipsShow('<p>请选择收货地址</p>');
                return false;
            }
        }
        buy_step2 = 1;
        var msg = '';
        for (var k in message) {
            msg += k + '|' + $('#storeMessage' + k).val() + ',';
        }
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=member_buy&op=buy_step2',
            data:{
                key:key,
                ifcart:ifcart,
                cart_id:cart_id,
                address_id:address_id,
                "chain[id]":chain_id,
                "chain[buyer_name]":chain_buyer_name,
                "chain[mob_phone]":chain_mob_phone,
                "chain[tel_phone]":'',
                pintuan:pintuan,
                log_id:log_id,
                buyer_id:buyer_id,
                vat_hash:vat_hash,
                offpay_hash:offpay_hash,
                offpay_hash_batch:offpay_hash_batch,
                pay_name:pay_name,
                invoice_id:invoice_id,
                voucher:voucher,
                pd_pay:pd_pay,
                password:password,
                fcode:fcode,
                rcb_pay:rcb_pay,
                rpt:rpt,
                pay_message:msg,
                reciver_date_msg:$('#reciver_date_msg').val()
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
                if (result.datas.payment_code == 'offline' || result.datas.payment_code == 'chain') {
                    window.location.href = WapSiteUrl + '/tmpl/member/order_list.html';
                } else {
                    delCookie('cart_count');
                    toPay(result.datas.pay_sn,'member_buy','pay');
                }
            }
        });
    });
});