/* 商品JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var goods_id = getQueryString("goods_id");
var map_list = [];
var map_index_id = '';
var store_id;
var chain_id = 0;
var chain_map_list = new Array();
var _wap_wx = 0;
var ua = navigator.userAgent.toLowerCase();
if (ua.indexOf('micromessenger') > -1) {
    _wap_wx = 1;
    loadJs("https://res.wx.qq.com/open/js/jweixin-1.2.0.js");
}
$(function () {
    var key = getCookie('key');

    var unixTimeToDateString = function (ts, ex) {
        ts = parseFloat(ts) || 0;
        if (ts < 1) {
            return '';
        }
        var d = new Date();
        d.setTime(ts * 1e3);
        var s = '' + d.getFullYear() + '-' + (1 + d.getMonth()) + '-' + d.getDate();
        if (ex) {
            s += ' ' + d.getHours() + ':' + d.getMinutes() + ':' + d.getSeconds();
        }
        return s;
    };

    var buyLimitation = function (a, b) {
        a = parseInt(a) || 0;
        b = parseInt(b) || 0;
        var r = 0;
        if (a > 0) {
            r = a;
        }
        if (b > 0 && r > 0 && b < r) {
            r = b;
        }
        return r;
    };

    template.helper('isEmpty', function (o) {
        for (var i in o) {
            return false;
        }
        return true;
    });

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
    get_detail(goods_id);
    //点击商品规格，获取新的商品
    function arrowClick(self, myData) {
        $(self).addClass("current").siblings().removeClass("current");
        //拼接属性
        var curEle = $(".spec").find("a.current");
        var curSpec = [];
        $.each(curEle, function (i, v) {
            // convert to int type then sort
            curSpec.push(parseInt($(v).attr("specs_value_id")) || 0);
        });
        var spec_string = curSpec.sort(function (a, b) {
            return a - b;
        }).join("|");
        //获取商品ID
        goods_id = myData.spec_list[spec_string];
        get_detail(goods_id);
    }

    function contains(arr, str) {//检测goods_id是否存入
        var i = arr.length;
        while (i--) {
            if (arr[i] === str) {
                return true;
            }
        }
        return false;
    }
    $.sValid.init({
        rules: {
            buynum: "digits"
        },
        messages: {
            buynum: "请输入正确的数字"
        },
        callback: function (eId, eMsg, eRules) {
            if (eId.length > 0) {
                var errorHtml = "";
                $.map(eMsg, function (idx, item) {
                    errorHtml += "<p>" + idx + "</p>";
                });
                $.sDialog({
                    skin: "red",
                    content: errorHtml,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
    //检测商品数目是否为正整数
    function buyNumer() {
        $.sValid();
    }

    function get_detail(goods_id) {
        var dis_id = getQueryString('dis_id');
        //渲染页面
        $.ajax({
            url: ApiUrl + "/index.php?act=goods&op=goods_detail",
            type: "get",
            async:false,
            data: {goods_id: goods_id, key: key, dis_id: dis_id},
            dataType: "json",
            success: function (result) {
                var data = result.datas;
                if (!data.error) {
                    //商品图片格式化数据
                    if (data.goods_image) {
                        var goods_image = data.goods_image.split(",");
                        data.goods_image = goods_image;
                    } else {
                        data.goods_image = [];
                    }
                    //商品规格格式化数据
                    if (data.goods_info.spec_name) {
                        var goods_map_spec = $.map(data.goods_info.spec_name, function (v, i) {
                            var goods_specs = {};
                            goods_specs["goods_spec_id"] = i;
                            goods_specs['goods_spec_name'] = v;
                            if (data.goods_info.spec_value) {
                                $.map(data.goods_info.spec_value, function (vv, vi) {
                                    if (i == vi) {
                                        goods_specs['goods_spec_value'] = $.map(vv, function (vvv, vvi) {
                                            var specs_value = {};
                                            specs_value["specs_value_id"] = vvi;
                                            specs_value["specs_value_name"] = vvv;
                                            return specs_value;
                                        });
                                    }
                                });
                                return goods_specs;
                            } else {
                                data.goods_info.spec_value = [];
                            }
                        });
                        data.goods_map_spec = goods_map_spec;
                    } else {
                        data.goods_map_spec = [];
                    }

                    // 虚拟商品限购时间和数量
                    if (data.goods_info.is_virtual == '1') {
                        data.goods_info.virtual_indate_str = unixTimeToDateString(data.goods_info.virtual_indate, true);
                        data.goods_info.buyLimitation = buyLimitation(data.goods_info.virtual_limit, data.goods_info.upper_limit);
                    }

                    // 预售发货时间
                    if (data.goods_info.is_presell == '1') {
                        data.goods_info.presell_deliverdate_str = unixTimeToDateString(data.goods_info.presell_deliverdate);
                    }

                    //渲染模板
                    template.helper('is_login',function () {
                        if(key){
                            return true;
                        }
                        return false;
                    });
                    var html = template.render('product_detail', data);
					document.title=data.goods_info.goods_name;
                    $("#product_detail_html").html(html);

                    if (data.goods_info.is_virtual == '0') {
                        $('.goods-detail-o2o').remove();
                    }

                    //渲染模板
                    var html = template.render('product_detail_sepc', data);
                    $("#product_detail_spec_html").html(html);
                    
                    if (data.goods_info.pintuan_promotion == '1') {
                        var log_id = getQueryString("log_id");
                        if (log_id) $(".cart_pintuan_promotion").html('参团');
                    }

                    //渲染模板
                    var html = template.render('voucher_script', data);
                    $("#voucher_html").html(html);

                    if (data.goods_info.is_virtual == '1') {
                        store_id = data.store_info.store_id;
                        virtual();
                    }
                    if (data.goods_info.is_chain == '1') {
                        store_id = data.store_info.store_id;
                        chain();
                    } else {
                        $('.goods-detail-chain').remove();
                    }
                    if (data.goods_info.pintuan_promotion == '1') {
                        takeCount();
                    }

                    // 购物车中商品数量
                    if (getCookie('cart_count')) {
                        if (getCookie('cart_count') > 0) {
                            $('#cart_count,#cart_count1').html('<sup>' + getCookie('cart_count') + '</sup>');
                        }
                    }

                    //图片轮播
                    picSwipe();

                    //商品描述
                    $(".pddcp-arrow").click(function () {
                        $(this).parents(".pddcp-one-wp").toggleClass("current");
                    });
                    //规格属性
                    var myData = {};
                    myData["spec_list"] = data.spec_list;
                    $(".spec a").click(function () {
                        var self = this;
                        arrowClick(self, myData);
                    });
                    //购买数量，减
                    $(".minus").click(function () {
                        var buynum = $(".buy-num").val();
                        if (buynum > 1) {
                            $(".buy-num").val(parseInt(buynum - 1));
                        }
                    });
                    //购买数量加
                    $(".add").click(function () {
                        var buynum = parseInt($(".buy-num").val());
                        if (buynum < data.goods_info.goods_storage) {
                            $(".buy-num").val(parseInt(buynum + 1));
                        }
                    });
                    // 一个F码限制只能购买一件商品 所以限制数量为1
                    if (data.goods_info.is_fcode == '1') {
                        $('.minus').hide();
                        $('.add').hide();
                        $(".buy-num").attr('readOnly', true);
                    }
                    //收藏
                    $(".pd-collect").click(function () {
                        if ($(this).hasClass('favorate')) {
                            if (dropFavoriteGoods(goods_id))
                                $(this).removeClass('favorate');
                        } else {
                            if (favoriteGoods(goods_id))
                                $(this).addClass('favorate');
                        }
                    });
                    //加入购物车
                    $("#add-cart").click(function () {
                        var key = getCookie('key');//登录标记
                        var quantity = parseInt($(".buy-num").val());
                        if (!key) {
                            var goods_info = decodeURIComponent(getCookie('goods_cart'));
                            if (goods_info == null) {
                                goods_info = '';
                            }
                            if (goods_id < 1) {
                                show_tip();
                                return false;
                            }
                            var cart_count = 0;
                            if (!goods_info) {
                                goods_info = goods_id + ',' + quantity;
                                cart_count = 1;
                            } else {
                                var goodsarr = goods_info.split('|');
                                for (var i = 0; i < goodsarr.length; i++) {
                                    var arr = goodsarr[i].split(',');
                                    if (contains(arr, goods_id)) {
                                        goodsarr.splice(i,1);
                                        goods_info = goodsarr.join('|');
                                        if (data.goods_info.goods_storage>=quantity+parseInt(arr[1])) {
                                            quantity = quantity+parseInt(arr[1]);
                                        }
                                        break;
                                    }
                                }
                                goods_info += '|' + goods_id + ',' + quantity;
                                cart_count = goodsarr.length;
                            }
                            // 加入cookie
                            addCookie('goods_cart', goods_info);
                            // 更新cookie中商品数量
                            addCookie('cart_count', cart_count);
                            show_tip();
                            getCartCount();
                            $('#cart_count,#cart_count1').html('<sup>' + cart_count + '</sup>');
                            return false;
                        } else {
                            $.ajax({
                                url: ApiUrl + "/index.php?act=member_cart&op=cart_add",
                                data: {key: key, goods_id: goods_id, quantity: quantity},
                                type: "post",
                                success: function (result) {
                                    var rData = $.parseJSON(result);
                                    if (checkLogin(rData.login)) {
                                        if (!rData.datas.error) {
                                            show_tip();
                                            // 更新购物车中商品数量
                                            delCookie('cart_count');
                                            getCartCount();
                                            $('#cart_count,#cart_count1').html('<sup>' + getCookie('cart_count') + '</sup>');
                                        } else {
                                            if (rData.spike) {
                                                $('.buy-handle').addClass('no-buy');
                                            }
                                            $.sDialog({
                                                skin: "red",
                                                content: rData.datas.error,
                                                okBtn: false,
                                                cancelBtn: false
                                            });
                                        }
                                    }
                                }
                            })
                        }
                    });

                    //立即购买
                    if (data.goods_info.is_virtual == '1') {
                        $("#buy-now").click(function () {
                            var key = getCookie('key');//登录标记
                            var buynum = parseInt($('.buy-num').val()) || 0;

                            if (buynum < 1) {
                                $.sDialog({
                                    skin: "red",
                                    content: '参数错误！',
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                return;
                            }
                            if (buynum > data.goods_info.goods_storage) {
                                $.sDialog({
                                    skin: "red",
                                    content: '库存不足！',
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                return;
                            }

                            // 虚拟商品限购数量
                            if (data.goods_info.buyLimitation > 0 && buynum > data.goods_info.buyLimitation) {
                                $.sDialog({
                                    skin: "red",
                                    content: '超过限购数量！',
                                    okBtn: false,
                                    cancelBtn: false
                                });
                                return;
                            }

                            var json = {};
                            json.key = key;
                            json.cart_id = goods_id;
                            json.quantity = buynum;
                            $.ajax({
                                type: 'post',
                                url: ApiUrl + '/index.php?act=member_vr_buy&op=buy_step1',
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
                                        location.href = WapSiteUrl + '/tmpl/order/vr_buy_step1.html?goods_id=' + goods_id + '&quantity=' + buynum;
                                    }
                                }
                            });
                        });
                    } else {
                        var buy_pintuan = 0;
                        var log_id = getQueryString("log_id");
                        var buyer_id = getQueryString("buyer_id");
                        function cart_buy() {
                            var key = getCookie('key');//登录标记
                                var buynum = parseInt($('.buy-num').val()) || 0;

                                if (buynum < 1) {
                                    $.sDialog({
                                        skin: "red",
                                        content: '参数错误！',
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                if (buynum > data.goods_info.goods_storage) {
                                    $.sDialog({
                                        skin: "red",
                                        content: '库存不足！',
                                        okBtn: false,
                                        cancelBtn: false
                                    });
                                    return;
                                }
                                            var u = WapSiteUrl + '/tmpl/order/buy_step1.html?goods_id=' + goods_id + '&buynum=' + buynum;
                                            if (buy_pintuan) u += '&pintuan=1&log_id=' + log_id + '&buyer_id=' + buyer_id;
                                            if (chain_id) u += '&ifchain=1&chain_id=' + chain_id;
                                            location.href = u;
                        }
                        $("#buy-now").click(function () {cart_buy();});
                        if (data.goods_info.pintuan_promotion == '1') {
                            $(".pintuan_promotion .invite-btn").click(function () {cart_buy();});
                            $(".pintuan_promotion .order-btn").click(function () {
                                buy_pintuan = 1;
                                cart_buy();
                            });
                        }
                    }
                	if (_wap_wx) {
                	    var _str = location.href+'@@@'+data.goods_info.goods_name+'@@@'+data.goods_image[0]+'@@@'+data.goods_info.goods_jingle;//'分享链接@@@分享标题@@@分享图标@@@分享描述'
                        $.ajax({
                            url: ApiUrl + "/index.php?act=wx_share&str="+encodeURIComponent(_str),
                            dataType: 'script',
                            success: function (result) {
                            }
                        });
                	}

                } else {

                    $.sDialog({
                        content: data.error + '！<br>请返回上一页继续操作…',
                        okBtn: false,
                        cancelBtnText: '返回',
                        cancelFn: function () {
                            history.back();
                        }
                    });
                }

                //验证购买数量是不是数字
                $("#buynum").blur(buyNumer);


                // 从下到上动态显示隐藏内容
                $.animationUp({
                    valve: '.animation-up,#goods_spec_selected', // 动作触发
                    wrapper: '#product_detail_spec_html', // 动作块
                    scroll: '#product_roll', // 滚动块，为空不触发滚动
                    start: function () {       // 开始动作触发事件
                        $('.goods-detail-foot').addClass('hide').removeClass('block');
                    },
                    close: function () {        // 关闭动作触发事件
                        $('.goods-detail-foot').removeClass('hide').addClass('block');
                    }
                });
                myScrollAnimationUp = new IScroll("#product_roll", { mouseWheel: true, click: true });

                $.animationUp({
                    valve: '#getVoucher', // 动作触发
                    wrapper: '#voucher_html', // 动作块
                    scroll: '#voucher_roll', // 滚动块，为空不触发滚动
                });
                if (data.voucher) voucherScrollAnimationUp = new IScroll("#voucher_roll", { mouseWheel: true, click: true });

                $('#voucher_html').on('click', '.btn', function () {
                    getFreeVoucher($(this).attr('data-tid'));
                });

                // 联系客服
                $('.kefu').click(function () {
                    window.location.href = WapSiteUrl + '/tmpl/member/chat_info.html?goods_id=' + goods_id + '&t_id=' + result.datas.store_info.default_im;
                })
            }
        });
    }

    $.scrollTransparent();
    $('#product_detail_html').on('click', '#get_area_selected', function () {
        $.areaSelected({
            success: function (data) {
                $('#get_area_selected_name').html(data.area_info);
                var area_id = data.area_id_2 == 0 ? data.area_id_1 : data.area_id_2;
                $.getJSON(ApiUrl + '/index.php?act=goods&op=calc', {goods_id: goods_id, area_id: area_id}, function (result) {
                    $('#get_area_selected_whether').html(result.datas.if_store_cn);
                    $('#get_area_selected_content').html(result.datas.content);
                    if (!result.datas.if_store) {
                        $('.buy-handle').addClass('no-buy');
                    } else {
                        $('.buy-handle').removeClass('no-buy');
                    }
                });
            }
        });
    });

    $('body').on('click', '#goodsBody,#goodsBody1', function () {
        window.location.href = WapSiteUrl + '/tmpl/product_info.html?goods_id=' + goods_id;
    });
    $('body').on('click', '#goodsEvaluation,#goodsEvaluation1', function () {
        window.location.href = WapSiteUrl + '/tmpl/product_eval_list.html?goods_id=' + goods_id;
    });

    $('#list-address-scroll').on('click', 'dl > a', map);
    $('#map_all').on('click', map);

});


function show_tip() {
    var flyer = $('.goods-pic > img').clone().css({'z-index': '999', 'height': '3rem', 'width': '3rem'});
    flyer.fly({
        start: {
            left: $('.goods-pic > img').offset().left,
            top: $('.goods-pic > img').offset().top - $(window).scrollTop()
        },
        end: {
            left: $("#cart_count1").offset().left + 40,
            top: $("#cart_count1").offset().top - $(window).scrollTop(),
            width: 0,
            height: 0
        },
        onEnd: function () {
            flyer.remove();
        }
    });
}

function virtual() {
    $('#get_area_selected').parents('.goods-detail-item').remove();
    $.getJSON(ApiUrl + '/index.php?act=goods&op=store_o2o_addr', {store_id: store_id}, function (result) {
        if (!result.datas.error) {
            if (result.datas.addr_list.length > 0) {
                $('#list-address-ul').html(template.render('list-address-script', result.datas));
                map_list = result.datas.addr_list;
                var _html = '';
                _html += '<dl index_id="0">';
                _html += '<dt>' + map_list[0].name_info + '</dt>';
                _html += '<dd>' + map_list[0].address_info + '</dd>';
                _html += '</dl>';
                _html += '<p><a href="tel:' + map_list[0].phone_info + '"></a></p>';
                $('#goods-detail-o2o').html(_html);

                $('#goods-detail-o2o').on('click', 'dl', map);

                if (map_list.length > 1) {
                    $('#store_addr_list').html('查看全部' + map_list.length + '家分店地址');
                } else {
                    $('#store_addr_list').html('查看商家地址');
                }
                $('#map_all > em').html(map_list.length);
            } else {
                $('.goods-detail-o2o').hide();
            }
        }
    });
    $.animationLeft({
        valve: '#store_addr_list',
        wrapper: '#list-address-wrapper',
        scroll: '#list-address-scroll'
    });
}

function chain_buy(_id) {
    chain_id = _id;
    $('#buy-now').click();
}

function chain() {
    $.getJSON(ApiUrl + '/index.php?act=goods&op=store_chain_addr', {goods_id: goods_id}, function (result) {
        if (!result.datas.error) {
            if (result.datas.addr_list.length > 0) {
                $('#list-chain-ul').html(template.render('list-chain-script', result.datas));
                chain_map_list = result.datas.addr_list;
                var _html = '';//copy_right  w_ww_.sh_op_nc_.net
                _html += '<dl area_id="' + chain_map_list[0].area_id + '" area_info="' + chain_map_list[0].area_info + '">';
                _html += '<dt>' + chain_map_list[0].chain_name +  '</dt>';
                _html += '<dd>门店价格：￥' + chain_map_list[0].shopnc_chain_price +'</dd>';
                _html += '<dd>门店地址：' + chain_map_list[0].chain_address +'，电话：' + chain_map_list[0].chain_phone +'</dd>';
                _html += '</dl>';
                _html += '<p><a href="javascript:chain_buy(' + chain_map_list[0].chain_id + ');"></a></p>';
                $('#goods-detail-chain').html(_html);

                if (chain_map_list.length > 1) {
                    $('#store_chain_list').html('查看全部' + chain_map_list.length + '家门店地址');
                } else {
                    $('#store_chain_list').html('查看门店地址');
                }
                $('#chain_all').html(chain_map_list.length);
                $('#chain_area_info').html('<option value="">所有城区</option>');
                $.each(chain_map_list,function(k,v){
                    if($('#chain_area_info').find("[value='"+v.area_id+"']").size() == 0) $('#chain_area_info').append('<option value="'+v.area_id+'">'+v.area_name+'</option>');
                });
                $('#chain_area_info').on('change', function(){
                    var area_id = $("#chain_area_info").val();
                    if (area_id == '') {
                        $('#list-chain-ul li').show();
                    } else {
                        $('#list-chain-ul li').hide();
                        $('#list-chain-ul').find("li[area_id='"+area_id+"']").show();
                    }
                });
            } else {
                $('.goods-detail-chain').hide();
            }
        }
    });
    $.animationLeft({
        valve: '#store_chain_list',
        wrapper: '#list-chain-wrapper',
        scroll: '#list-chain-scroll'
    });
}

function map() {
    $('#map-wrappers').removeClass('hide').removeClass('right').addClass('left');
    $('#map-wrappers').on('click', '.header-l > a', function () {
        $('#map-wrappers').addClass('right').removeClass('left');
    });
    $('#baidu_map').css('width', document.body.clientWidth);
    $('#baidu_map').css('height', document.body.clientHeight);
    map_index_id = $(this).attr('index_id');
    if (typeof map_index_id != 'string') {
        map_index_id = '';
    }
    if (typeof (map_js_flag) == 'undefined') {
        $.ajax({
            url: WapSiteUrl + '/js/map.js',
            dataType: "script",
            async: false
        });
    }
    if (typeof BMap == 'object') {
        baidu_init();
    } else {
        load_script();
    }
}
	function takeCount() {
	    setTimeout("takeCount()", 1000);
	    $(".count-time").each(function(){
	        var obj = $(this);
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
	    });
	}