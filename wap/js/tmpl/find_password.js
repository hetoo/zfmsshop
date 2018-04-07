var mobile = '';  //手机号
var sec_val = ''; //验证码
var sec_key = ''; //验证码key
var m_captcha = ''; //手机验证码
var send_sms_state = 0;  //手机短信验证码发送状态

$(function(){
    //加载验证码
    loadSeccode();
    $("#refreshcode").bind('click',function(){
        loadSeccode();
    });
    
    $.sValid.init({//注册验证
        rules:{
            usermobile:{
                required:true,
                mobile:true
            },
            captcha:{
                required:true,
                minlength:4,
                maxlength:4
            },
            mobilecode:{
                required:true,
                digits:true,
                minlength:6,
                maxlength:6
            }
        },
        messages:{
            usermobile:{
                required:"请填写手机号！",
                mobile:"手机号码不正确"
            },
            captcha:{
                required:"请输入图片验证码",
                minlength:"图片验证码为4位字符",
                maxlength:"图片验证码为4位字符"
            },
            mobilecode:{
                required:"请输入手机验证码",
                digits:"手机验证码为6位整数",
                minlength:"手机验证码为6位整数",
                maxlength:"手机验证码为6位整数"
            }
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
    
    //获取短信验证码
    $('#again').click(function(){
        sec_val = $('#captcha').val();
        sec_key = $('#codekey').val();
        mobile = $('#usermobile').val();
        if(mobile == '' || ! /^(1{1})+\d{10}$/.test(mobile)){
            errorTipsShow("<p>请输入正确的手机号</p>");
            return false;
        }
        if(sec_val == '' || sec_val.length != 4){
            errorTipsShow("<p>请输入正确的图片验证码</p>");
            return false;
        }
        if(!send_sms_state){
            send_sms_state = 1;
            send_sms(mobile, sec_val, sec_key);
        }        
    });

	$('#find_password_btn').click(function(){
        if (!$(this).parent().hasClass('ok')) {
            return false;
        }
	    if ($.sValid()) {
            mobile = $('#usermobile').val();
            m_captcha = $('#mobilecode').val();
            check_sms_captcha(mobile,m_captcha);
            return false;
	    } else {
	        return false;
	    }
	});
});

// 发送手机验证码
function send_sms(mobile, sec_val, sec_key) {
    $.getJSON(ApiUrl+'/index.php?act=connect&op=get_sms_captcha', {type:3,phone:mobile,sec_val:sec_val,sec_key:sec_key}, function(result){
        if(!result.datas.error){
            $.sDialog({
                skin:"green",
                content:'发送成功',
                okBtn:false,
                cancelBtn:false
            });
            $('.code-again').hide();
            $('.code-countdown').show().find('em').html(result.datas.sms_time);
            var times_Countdown = setInterval(function(){
                var em = $('.code-countdown').find('em');
                var t = parseInt(em.html() - 1);
                if (t == 0) {
                    send_sms_state = 0;
                    $('.code-again').show();
                    $('.code-countdown').hide();
                    clearInterval(times_Countdown);
                } else {
                    em.html(t);
                }
            },1000);
        }else{
            send_sms_state = 0;
            loadSeccode();
            errorTipsShow('<p>' + result.datas.error + '<p>');
        }
    });
}
//验证手机验证码
function check_sms_captcha(mobile, captcha) {
    $.getJSON(ApiUrl + '/index.php?act=connect&op=check_sms_captcha', {type:3,phone:mobile,captcha:captcha }, function(result){
        if (!result.datas.error) {
            window.location.href = 'find_password_password.html?mobile=' + mobile + '&captcha=' + captcha;
        } else {
            loadSeccode();
            errorTipsShow('<p>' + result.datas.error + '<p>');
        }
    });
}