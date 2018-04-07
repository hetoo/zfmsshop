$('.nav-r-box').click(function(){
	$('.nav-r').toggleClass('show-s');
});

$(document).scroll(function(){
    $('.nav-r').removeClass('show-s');
});

//回到顶部
$(document).scroll(function(){
    set();
});
$('.fix-block-box').click(function(){
    btn = $(this)[0];
    this.timer=setInterval(function(){
        $(window).scrollTop(Math.floor($(window).scrollTop()*0.8));
        if($(window).scrollTop()==0) clearInterval(btn.timer,set);
    },10);
});
function set(){
	$(window).scrollTop()==0 ? $('.fix-block-box').addClass('hide') : $('.fix-block-box').removeClass('hide');
}

