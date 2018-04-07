/* 秒杀推荐JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/
  
    var recommend_max = 6;
    var goods_max = 4;//商品数
    var spike_max = 4;//活动数
    var recommend_show = 1;//当前选择的推荐
    obj = $(".ncap-form-default");
    function add_recommend_goods() {
        for (var i = 1; i <= recommend_max; i++) {//防止数组下标重复
            if (obj.find("dl[recommend_id='"+i+"']").size()==0) {//编号不存在时添加
                var add_html = '';
                add_html = '<dl class="row" recommend_id="'+i+'"><dt class="tit">推荐商品<div class="btn"> <a class="btn red" href="JavaScript:del_recommend('+i+
                    ');"><i class="fa fa-trash-o"></i>删除</a></div></dt>'+
        			'<dd class="opt" onclick="select_recommend('+i+',\'goods\');"><input type="hidden" name="spike_list['+i+'][]" value="goods">'+
        			'<ul class="dialog-goodslist-s1 goods-list scrollbar-box"></ul>'+
        			'</dd></dl>';
                $("#append_goods").before(add_html);
                select_recommend(i,"goods");
                break;
            }
        }
    }
    function add_spike_brand() {
        for (var i = 1; i <= recommend_max; i++) {//防止数组下标重复
            if (obj.find("dl[recommend_id='"+i+"']").size()==0) {//编号不存在时添加
                var add_html = '';
                add_html = '<dl class="row" recommend_id="'+i+'"><dt class="tit">推荐活动<div class="btn"> <a class="btn red" href="JavaScript:del_recommend('+i+
                    ');"><i class="fa fa-trash-o"></i>删除</a></div></dt>'+
        			'<dd class="opt" onclick="select_recommend('+i+',\'spike\');"><input type="hidden" name="spike_list['+i+'][]" value="spike">'+
        			'<ul class="dialog-goodslist-s102 goods-list scrollbar-box"></ul>'+
        			'</dd></dl>';
                $("#append_goods").before(add_html);
                select_recommend(i,"spike");
                break;
            }
        }
    }
    function del_recommend(_id) {//删除推荐
        if (_id==recommend_show) obj.find("dl[show]").hide();
        obj.find("dl[recommend_id='"+_id+"']").remove();
    }
    function select_recommend(_id,t) {//选中
        var _obj = obj.find("dl[recommend_id='"+_id+"'] dd");
        obj.find("dd").removeClass("selected");
        _obj.addClass("selected");
        recommend_show = _id;
        obj.find("dl[show]").hide();
        obj.find("dl[show="+t+"]").show();
    }
    function goods_search() {
        var goods_name = $.trim($('#goods_name').val());
        $("#show_recommend_goods_list").load('index.php?act=spike_recommend&op=goods_list&'+$.param({'goods_name':goods_name }));
    }
    function spike_search() {
        var spike_name = $.trim($('#spike_name').val());
        $("#show_recommend_spike_list").load('index.php?act=spike_recommend&op=spike_list&'+$.param({'spike_name':spike_name }));
    }
    function select_recommend_goods(goods_id) {//商品选择
        var _obj = obj.find("dd.selected ul");
        if(_obj.find("img[select_goods_id='"+goods_id+"']").size()>0) return;//避免重复
        if(_obj.find("img[select_goods_id]").size()>=goods_max) return;
    	var goods = $("#show_recommend_goods_list img[goods_id='"+goods_id+"']");
    	var text_append = '';
    	var goods_pic = goods.attr("src");
    	var spike_goods_id = goods.attr("spike_goods_id");
    	var goods_name = goods.attr("goods_name");
    	var _id = recommend_show;
    	text_append += '<div ondblclick="del_recommend_goods('+goods_id+');" class="goods-pic">';
    	text_append += '<span class="ac-ico" onclick="del_recommend_goods('+goods_id+');"></span>';
    	text_append += '<span class="thumb size-72x72">';
    	text_append += '<i></i>';
      	text_append += '<img select_goods_id="'+goods_id+'" title="'+goods_name+'" src="'+goods_pic+'" onload="javascript:DrawImage(this,72,72);" />';
    	text_append += '</span></div>';
    	text_append += '<div class="goods-name">';
    	text_append += '<a href="'+SITEURL+'/index.php?act=goods&goods_id='+goods_id+'" target="_blank">';
      	text_append += goods_name+'</a>';
    	text_append += '</div>';
    	text_append += '<input name="spike_list['+_id+'][]" value="'+spike_goods_id+'" type="hidden">';
    	_obj.append('<li id="select_recommend_'+_id+'_goods_'+goods_id+'">'+text_append+'</li>');
    }
    function select_recommend_spike(spike_id) {//活动选择
        var _obj = obj.find("dd.selected ul");
        if(_obj.find("img[select_spike_id='"+spike_id+"']").size()>0) return;//避免重复
        if(_obj.find("img[select_spike_id]").size()>=spike_max) return;
    	var spike = $("#show_recommend_spike_list img[spike_id='"+spike_id+"']");
    	var text_append = '';
    	var spike_pic = spike.attr("src");
    	var spike_name = spike.attr("spike_name");
    	var _id = recommend_show;
    	text_append += '<div ondblclick="del_recommend_spike('+spike_id+');" class="goods-pic"  style=" width:219px; height:150px;">';
    	text_append += '<span class="ac-ico" onclick="del_recommend_spike('+spike_id+');"></span>';
    	text_append += '<span class="thumb size-72x72">';
    	text_append += '<i></i>';
      	text_append += '<img style=" width:219px; height:150px;" select_spike_id="'+spike_id+'" title="'+spike_name+'" src="'+spike_pic+'" onload="javascript:DrawImage(this,72,72);" />';
    	text_append += '</span></div>';
    	text_append += '<div class="goods-name" style=" width:219px;">';
    	text_append += '<a href="'+SITEURL+'/index.php?act=spike&spike_id='+spike_id+'" target="_blank">';
      	text_append += spike_name+'</a>';
    	text_append += '</div>';
    	text_append += '<input name="spike_list['+_id+'][]" value="'+spike_id+'" type="hidden">';
    	_obj.append('<li id="select_recommend_'+_id+'_spike_'+spike_id+'">'+text_append+'</li>');
    }
    function del_recommend_goods(goods_id) {//删除已选商品
        var _id = recommend_show;
        $('#select_recommend_'+_id+'_goods_'+goods_id).remove();
    }
    function del_recommend_spike(spike_id) {//删除已选活动
        var _id = recommend_show;
        $('#select_recommend_'+_id+'_spike_'+spike_id).remove();
    }