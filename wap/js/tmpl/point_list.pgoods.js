var key = getCookie('key');
var myPoints = 0;
var myLevel = 0;

var page = pagesize;
var curpage = 1;
var hasmore = true;
$(function(){
    get_list();
    if(key){
       $.ajax({
            type:'post',
            url:ApiUrl+"/index.php?act=points&op=member_info",
            data:{key:key},
            dataType:'json',
            success:function(result){
                checkLogin(result.login);
                var member_info = result.datas.member_info;
                myPoints = member_info.member_points;
                myLevel = member_info.level;
            }
        }); 
    }
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
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

    $.ajax({
          url: ApiUrl + '/index.php?act=points&op=exchange_pgoods' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          success : function(result){
            if(!result) {
                result = [];
                result.datas = [];                
                result.datas.pointprod_list = [];
            }
            $('.loading').remove();
            curpage++;
            var html = template.render('pgoods_body', result);
            $(".integral-part").append(html);
            hasmore = result.hasmore;
          }
    });
}