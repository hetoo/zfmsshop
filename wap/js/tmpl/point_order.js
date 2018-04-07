var page = pagesize;
var curpage = 1;
var hasmore = true;
var footer = false;
var key = getQueryString('key');
var memberkey = getCookie('key')?getCookie('key'):'';

$(function(){

    get_list();
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });
});

function get_list() {
    $('.loading').remove();
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    if(memberkey != ''){
        param.key = memberkey;
    }

    $.getJSON(ApiUrl + '/index.php?act=member_points&op=point_order' + window.location.search.replace('?','&'), param, function(result){
        if (result.code != '200') {
            alert('请登录');
            window.location.href = WapSiteUrl+'/tmpl/member/login.html';
        }else{
            if(!result) {
                result = [];
                result.datas = [];
                result.datas.order_list = [];
            }
            $('.loading').remove();
            curpage++;
            var html = template.render('point_order_body', result);
            $("#point_order_list .goods-secrch-list").append(html);
            hasmore = result.hasmore;
        }
    });
}
