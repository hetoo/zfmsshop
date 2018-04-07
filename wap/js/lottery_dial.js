/* 大转盘活动JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
if (getQueryString('key') != '') {
    var key = getQueryString('key');
    var member_name = getQueryString('username');
    addCookie('key', key);
    addCookie('username', member_name);
} else {
    var key = getCookie('key');
    var member_name = getCookie('username');
}
var lot_id = getQueryString('lot_id');
var from = getQueryString('from');


$(function(){
	var lot_info = '';
	var qua_amount = '';
	var dial_err = '';
	var lottery;
	var prize_info = '';
    if(from.toLowerCase() == 'app' || from.toLowerCase() == 'ios' || from.toLowerCase() == 'android'){
        $('#header').remove();
    }
	$.ajax({
		type:'post',
        url:ApiUrl+'/index.php?act=lottery_dial&op=dial_info',
        data:{lot_id:lot_id,key:key},
        dataType:'json',
        success:function(result){
        	if(result.code == 200){
        		lot_info = result.datas.dial_info;
        		qua_amount = parseInt(result.datas.dail_qua);
                $('div.m-ui-dial').css({'background':'url("'+lot_info.lot_dial_bg+'") no-repeat center','background-size':'100%'});
                $('div.m-ui-dial .pointer').css({'background':'url("'+lot_info.lot_dial_pointer+'") no-repeat center','background-size':'100%'});
                var prize_list = result.datas.prize_list;
                var p_html = '';
                if(!$.isEmptyObject(prize_list)){
                    p_html += '<div class="winner-list box" id="winner-list">';
                        p_html += '<ul class="list-wrap" style="display: block; top: 10px; ">';
                        for (var i = 0; i < prize_list.length; i++) {
                            var info = prize_list[i];
                            p_html += '<li class="winner-info"><span class="w-name">'+info.member_name+'</span><span class="w-prize">'+info.prize_info+'</span></li>';
                        }
                        p_html += '</ul>';
                    p_html += '</div>';
                }else{
                    p_html += "<div class='winner-none' style='line-height:100px;'>还没出现中奖者，期待您赢取大奖！</div>";
                }
                $('.winner-name').append(p_html);
                var Length = $("#winner-list").find("li").length;
                 if(Length >= 5){
                        $("#winner-list").textSlider({
                        speed: 50, //数值越大，速度越慢
                        line:5  //触摸翻滚的条数
                    });
                 }
        	}else{
        		dial_err = result.datas.error;
                $.sDialog({
                    skin:"red",
                    content:dial_err,
                    okBtn:false,
                    cancelBtn:false
                });
        	}
        }        
	});
	lottery = new LotteryDial(document.getElementById('js_pointer'), {
        speed: 30, //每帧速度
        areaNumber: lot_info.prize_size?lot_info.prize_size:8 //奖区数量
    });
	var index = -1;
	$('.pointer').on('click','a.btn',function(){
        if(!key){
            window.location.href = WapSiteUrl+'/tmpl/member/login.html';
            return;
        }else{
    		if(dial_err == '' && qua_amount > 0 && lot_info != ''){
                lottery.draw();
    		}else{
                if(dial_err == '')dial_err='您的抽奖机会已用完啦~~~';
                $.sDialog({
                    skin:"red",
                    content:dial_err,
                    okBtn:false,
                    cancelBtn:false
                });
    		}
        }
	});

	lottery.on('start', function () {
        //请求获取中奖结果
        $.ajax({
            type:'post',
            url:ApiUrl+'/index.php?act=lottery_dial&op=dial_prize',
            data:{key:key,lot_id:lot_id},
            dataType:'json',
            success:function(result){
                if(result.code == 200){
                    index = parseInt(result.datas.prize_grade,10);
                    prize_info = result.datas.prize_info;
                    if(index == -1){
                        $.sDialog({
                            skin:"red",
                            content:prize_info.prize_detial,
                            okBtn:false,
                            cancelBtn:true,
                            cancelBtnText: "确定",
                        });
                        lottery.reset();
                        return false;
                    }
                    lottery.setResult(index);
                }else{
                    $.sDialog({
                        skin:"red",
                        content:result.datas.error,
                        okBtn:false,
                        cancelBtn:false
                    });
                    lottery.reset();
                }
            }
        });
    });
    lottery.on('end', function () {
    	if(prize_info != ''){
    		qua_amount--;
            if(qua_amount == 0){
                dial_err = '您的抽奖机会已用完啦~~~';
            }
            if(parseInt(prize_info.prize_type) == 0){
                $.sDialog({
                    skin:"red",
                    content:prize_info.prize_detial,
                    okBtn:false,
                    cancelBtn:true,
                    cancelBtnText: "确定",
                });
            }else{
                var p_html = '';
                if($('.winner-name .winner-list').size() > 0){
                    p_html = '<li class="winner-info"><span class="w-name">'+member_name+'</span><span class="w-prize">'+prize_info.prize_detial+'</span></li>';
                    $('.winner-name .winner-list').find('ul').append(p_html);
                    var Length = $("#winner-list").find("li").length;
                     if(Length == 5){
                            $("#winner-list").textSlider({
                            speed: 50, //数值越大，速度越慢
                            line:5  //触摸翻滚的条数
                        });
                     }
                }else{
                    p_html += '<div class="winner-list">';
                    p_html += '<ul class="list-wrap sd" style="display: block; top: 10px; ">';
                    p_html += '<li class="winner-info"><span class="w-name">'+member_name+'</span><span class="w-prize">'+prize_info.prize_detial+'</span></li>';
                    p_html += '</ul>';
                    p_html += '</div>';
                    $('.winner-name').find('.winner-none').remove();
                    $('.winner-name').append(p_html);
                }
                var p_txt = '恭喜您获得：'+prize_info.rate_name+'【'+prize_info.prize_detial+'】';
                $.sDialog({
                    skin:"red",
                    content:p_txt,
                    okBtn:false,
                    cancelBtn:true,
                    cancelBtnText: "确定",
                });
            }
    		
    	}
    });
})
