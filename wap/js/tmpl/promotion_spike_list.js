var page = pagesize;
var curpage = 1;
var hasmore = true;
var footer = false;

$(function(){
	get_list();
    $(window).scroll(function(){
        if(($(window).scrollTop() + $(window).height() > $(document).height()-1)){
            get_list();
        }
    });
    $('.tabClick li').click(function(){
    	var l_uri = $(this).attr('ncuri');
    	if(l_uri != ''){
    		window.location.href = WapSiteUrl+'/tmpl/'+l_uri;
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
    param.state = $('.tabClick').find('.active').attr('nctype');

    $.ajax({
          url: ApiUrl + '/index.php?act=spike&op=spike_list' + window.location.search.replace('?','&'),
          data: param,
          dataType: 'json',
          success : function(result){
        	if(!result) {
        		result = [];
        		result.datas = [];
        		result.datas.spike_list = [];
        	}
            $('.loading').remove();
            curpage++;
            var html = template.render('home_body', result);
            $(".new-skill-wrap").append(html);
            hasmore = result.hasmore;
          }
    });
}