/* 兑换礼品JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
var pgoods_id = getQueryString("pgoods_id");
var key = getCookie('key');
var myPoints = 0;
var myLevel = 0;
var cartCount = 0;

var page = pagesize;
var curpage = 1;
var hasmore = true;

$(function () {
    $.ajax({
        url: ApiUrl + "/index.php?act=points&op=pgoods_detial",
        type: "get",
        data: {pgoods_id: pgoods_id, key: key},
        dataType: "json",
        success: function (result) {
            var data = result.datas;
            if (!data.error) {
                if(key){
                    myPoints = data.member_info.member_points;
                    myLevel = data.member_info.level;
                    cartCount = data.pointcart_count;
                    addCookie('pgoods_cart_count',cartCount);
                }
                //渲染模板
                var html = template.render('pgoods_body', data);
                $("#pgoods_info").html(html);
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
        }
    });
    if (getCookie('pgoods_cart_count')) {
        if (getCookie('pgoods_cart_count') > 0) {
            $('#pgoods_cart_count').html('<sup>' + getCookie('pgoods_cart_count') + '</sup>');
        }
    }

    get_order_list();

    $('.ncp-exchangeNote').scroll(function(){
        if(($('.ncp-exchangeNote').scrollTop() + $('.ncp-exchangeNote').height() > $('.ncp-exchangeNote').height()-1)){
            get_order_list();
        }
    });
});

//加入兑换车
$("#add-cart").click(function () {
    key = getCookie('key');
    if (!key) {
        $.sDialog({
            skin: "red",
            content: "请先登录",
            okBtn: false,
            cancelBtn: true,
            cancelBtnText: "确定",
            cancelFn:function(){
                window.location.href = WapSiteUrl+'/tmpl/member/login.html';
                return;
            }
        });
    } else {
        $.ajax({
            url: ApiUrl + "/index.php?act=member_points&op=prodexchange",
            data: {key: key, id: pgoods_id},
            type: "post",
            success: function (result) {
                var rData = $.parseJSON(result);
                if (checkLogin(rData.login)) {
                    if (!rData.datas.error) {
                        cartCount++;
                        addCookie('pgoods_cart_count',cartCount);
                        $('#pgoods_cart_count').html('<sup>' + getCookie('pgoods_cart_count') + '</sup>');
                    } else {
                        $.sDialog({
                            skin: "red",
                            content: rData.datas.error,
                            okBtn: false,
                            cancelBtn: false
                        });
                    }
                }
            }
        });
    }
});

//获取兑换记录列表
function get_order_list() {
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    $.ajax({
        url: ApiUrl + "/index.php?act=points&op=pgoods_order" + window.location.search.replace('?','&'),
        type: "get",
        data:{page:page, curpage:curpage, pgoods_id:pgoods_id},
        dataType: "json",
        success: function (result) {
            var data = result.datas;
            if (!data.error) {
                curpage++;
                //渲染模板
                var html = template.render('pgoods_list', data);
                $('#get_list .ncp-exchangeNote').append(html);                
                hasmore = result.hasmore;
            } else {
                $.sDialog({
                    content: data.error,
                    okBtn: false,
                    cancelBtn: false
                });
            }
        }
    });
}
