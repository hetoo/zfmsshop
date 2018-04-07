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

//兑换平台红包
$('.get_redpacket').live('click',function(){
    if(!key){
        location.href = '../tmpl/member/login.html';
        return false;
    }
    var url = ApiUrl+"/index.php?act=member_points&op=redpacketexchange";
    var obj = $(this).parents('li');
    var redpackte_id = obj.attr('redpackte_id');
    var rpacket_t_price = obj.attr('rpacket_t_price');
    var rpacket_t_limit = obj.attr('rpacket_t_limit');
    var rpacket_t_end_date = obj.attr('rpacket_t_end_date');
    var rpacket_t_points = parseInt(obj.attr('rpacket_t_points'));
    var rpacket_t_giveout = parseInt(obj.attr('rpacket_t_giveout'));
    var rpacket_t_eachlimit = obj.attr('rpacket_t_eachlimit');
    var rpacket_t_total =  parseInt(obj.attr('rpacket_t_total'));
    var rpacket_t_mgradelimit =  parseInt(obj.attr('rpacket_t_mgradelimit'));

    var d_html = '';
    if(rpacket_t_giveout < rpacket_t_total){
        if(rpacket_t_points > myPoints){
            alert('您的积分不足，暂时不能兑换该红包');return false;
        }
        if(rpacket_t_mgradelimit > myLevel){
            alert('您的会员级别不够，暂时不能兑换该红包');return false;
        }
        d_html += '<dl><dt>您正在使用<span class="ml5 mr5">'+rpacket_t_points+'</span>积分&nbsp;兑换&nbsp;1&nbsp;张<br>';
        d_html += rpacket_t_price+'元红包（<em>满'+rpacket_t_limit+'减'+rpacket_t_price+'</em>）</dt>';
        d_html += '<dd>红包有效期至'+rpacket_t_end_date+'</dd>';
        var limit_count = '不限量';
        if(rpacket_t_eachlimit > 0){
            limit_count = rpacket_t_eachlimit+'张';
        }
        d_html += '<dd>每个ID领取'+limit_count+'</dd></dl>';
        exchange(url,redpackte_id,d_html);
    }else{
        d_html += '红包已兑换完';
        alert(d_html);return false;
    }
});

//兑换操作
function exchange(uri,id,d_html){
    $.sDialog({
        skin: "red",
        content: d_html,
        okBtn: true,
        cancelBtn: true,
        okFn: function(){
            $.ajax({
                type:'post',
                url:uri,
                data:{key:key,id:id},
                dataType:'json',
                success:function(result){
                    if(result.code == 200){
                        if(result.datas == 1){
                            alert('兑换成功');
                        }else{
                            alert(result.datas);
                        }
                    }else{
                        alert(result.datas.error);
                    }
                }
            });
        }
    });
}

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
          url: ApiUrl + '/index.php?act=points&op=exchange_redpacket' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          success : function(result){
            if(!result) {
                result = [];
                result.datas = [];                
                result.datas.rptlist = [];
            }
            $('.loading').remove();
            curpage++;
            var html = template.render('redpacket_body', result);
            $(".integral-part").append(html);
            hasmore = result.hasmore;
          }
    });
}