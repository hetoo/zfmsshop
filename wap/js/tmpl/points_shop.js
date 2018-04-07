var key = getCookie('key');
var myPoints = 0;
var myLevel = 0;
$.ajax({
    type:'post',
    url:ApiUrl+"/index.php?act=points",
    data:{key:key},
    dataType:'json',
    success:function(result){
        var html = '';
        if(key){
            var member_info = result.datas.member_info;
            html +='<div class="account-image">';
            html +='<img class="image-shadow" src="'+member_info.avatar+'">';
            html +='<div class="fl">';
            html +='<h1>'+member_info.user_name+'<span class="level image-shadow-s">Lv.<b>'+member_info.level+'</b></span></h1>';
            html +='<h2>经验值:&nbsp;'+member_info.member_exppoints+'</h2>';
            html +='</div>';
            html +='</div>';
            var cart_count = '';
            if(result.datas.pointcart_count > 0){
                cart_count = '<span></span>';
            }
            html +='<a href="member/point_cart.html" class="integral-cart">'+cart_count+'</a>';
            html +='<a href="member/point_order.html" class="record">礼品兑换记录&nbsp;></a>';
            html +='<div class="clear"></div>';
            
            myPoints = member_info.member_points;
            myLevel = member_info.level;
            $('#points_body').find('[data_type="point"]').text(myPoints);
            $('#points_body').find('[data_type="voucher"]').text(result.datas.vouchercount);
            $('#points_body').find('[data_type="redpacket"]').text(result.datas.redpacketcount);
        }else{
            html +='<div class="account-image">';
            html += '<img class="image-shadow" src="../images/member_w.png">';
            html += '<div class="fl">';
            html += '<a href="member/login.html"><h1 style="margin-top:1.3rem">立即登录</h1></a>';
            html += '</div>';
            html += '</div>';
            html += '<div class="clear"></div>';
        }
        var data = {pointsprod:result.datas.pointsprod,redpacket:result.datas.redpacket,voucher:result.datas.voucher};
        var b_html = template.render('home_body', data);
        $('#points_body').append(b_html);
        $('#access_info').html(html);
    }
});

//兑换代金券
$('.get_voucher').live('click',function(){
    if(!key){
        location.href = '../tmpl/member/login.html';
        return false;
    }
    var url = ApiUrl+"/index.php?act=member_points&op=voucherexchange";
    var obj = $(this).parents('li');
    var voucher_id = obj.attr('voucher_id');
    var voucher_t_price = obj.attr('voucher_t_price');
    var voucher_t_limit = obj.attr('voucher_t_limit');
    var voucher_t_end_date = obj.attr('voucher_t_end_date');
    var voucher_t_points = parseInt(obj.attr('voucher_t_points'));
    var voucher_t_giveout = parseInt(obj.attr('voucher_t_giveout'));
    var voucher_t_eachlimit = obj.attr('voucher_t_eachlimit');
    var voucher_t_total = parseInt(obj.attr('voucher_t_total'));
    var voucher_t_mgradelimit = parseInt(obj.attr('voucher_t_mgradelimit'));

    var d_html = '';
    if(voucher_t_giveout < voucher_t_total){
        if(voucher_t_points > myPoints){
            alert('您的积分不足，暂时不能兑换该代金券');return false;
        }
        if(voucher_t_mgradelimit > myLevel){
            alert('您的会员级别不够，暂时不能兑换该代金券');return false;
        }
        d_html += '<dl><dt>您正在使用<span class="ml5 mr5">'+voucher_t_points+'</span>积分&nbsp;兑换&nbsp;1&nbsp;张<br>';
        d_html += '官方自营'+voucher_t_price+'元店铺代金券（<em>满'+voucher_t_limit+'减'+voucher_t_price+'</em>）</dt>';
        d_html += '<dd>店铺代金券有效期至'+voucher_t_end_date+'</dd>';
        var limit_count = '不限量';
        if(voucher_t_eachlimit > 0){
            limit_count = voucher_t_eachlimit+'张';
        }
        d_html += '<dd>每个ID领取'+limit_count+'</dd></dl>';
        exchange(url,voucher_id,d_html);
    }else{
        d_html += '代金券已兑换完';
        alert(d_html);return false;
    }
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
                        $.sDialog({
                            skin: "red",
                            content: "兑换成功",
                            okBtn: false,
                            cancelBtn: false
                        });
                        //alert("兑换成功");
                    }else{
                        //alert(result.datas.error);
                        $.sDialog({
                            skin: "red",
                            content: result.datas.error,
                            okBtn: false,
                            cancelBtn: false
                        });
                    }
                }
            });
        }
    });
}


