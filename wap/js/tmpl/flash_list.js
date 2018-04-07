var page = pagesize;
var curpage = 1;
var hasmore = true;
var footer = false;
var spike_id = getQueryString("spike_id");
var reset = true;

$(function(){
    if (getQueryString('state') != '') {
        $('#filtrate_ul').find('li').has('a[data-state="' + getQueryString('data-state')  + '"]').addClass('selected').siblings().removeClass("selected");
    }

    $('#fixed_nav').waypoint(function() {
        $('#fixed_nav').toggleClass('fixed');
    }, {
        offset: '50'
    });

    $('#filtrate_ul li').click(function(){
        $('#filtrate_ul').find('li').removeClass('selected');
        $(this).addClass('selected').siblings().removeClass("selected");
        reset = true;
        window.scrollTo(0,0);
        get_list();
    });

	get_list();
	
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });

    
});


function get_list() {
    if (reset) {
        curpage = 1;
        hasmore = true;
    }
    $('.loading').remove();
    if (!hasmore) {
        return false;
    }
    hasmore = false;
    param = {};
    param.page = page;
    param.curpage = curpage;
    param.state = $('#filtrate_ul .selected').find('a').attr('data-state');

    $.ajax({
          url: ApiUrl + '/index.php?act=flash&op=flash_list' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          success : function(result){
        	if(!result) {
        		result = [];
        		result.datas = [];
        		result.datas.flash_list = [];
        	}
            $('.loading').remove();
            curpage++;
            var html = template.render('home_body', result);
            if(reset){
                $("#act_list").html(html);
            }else{
                $("#act_list").append(html);
            }            
            hasmore = result.hasmore;
          }
    });
}
