    var key = getCookie('key');
	var address_id = getQueryString('address_id');
	var chain_id = 0;
	var address_list = new Array();
	var op = "address_add";
$(function(){
	if(address_id){
    	$.ajax({
    		type: 'post',
    		url: ApiUrl + '/index.php?act=member_address&op=address_info',
    		data: {
    			key: key,
    			address_id: address_id
    		},
    		dataType: 'json',
    		success: function(result) {
    			checkLogin(result.login);
    			var address_info = result.datas.address_info;
    			$('#true_name').val(address_info.true_name);
    			$('#mob_phone').val(address_info.mob_phone);
    			$('#area_info').val(address_info.area_info).attr({'data-areaid':address_info.area_id, 'data-areaid2':address_info.city_id});
    			$('#address').val(address_info.dlyp_address_name+'，'+address_info.dlyp_address);
    			var _checked = address_info.is_default == '1' ? true : false;
    			$('#is_default').prop('checked',_checked);
    			if (_checked) {
    			    $('#is_default').parents('label').addClass('checked');
    			}
    			chain_id = address_info.dlyp_id;
    			chain_list(address_info.area_id);
    			op = "address_edit";
    		}
    	});
	}
	$.sValid.init({
		rules:{
			true_name:"required",
			mob_phone:{
                required:true,
                mobile:true
            },
			area_info:"required",
			address:"required"
		},
		messages:{
			true_name:"姓名必填！",
			mob_phone:{
                required:"手机号必填！",
                mobile:"手机号不正确"
            },
			area_info:"地区必填！",
			address:"门店必填！"
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
	$('#header-nav').click(function(){
		$('.btn').click();
	});
    $.animationLeft({
        valve : '#address',
        wrapper : '#address-wrapper',
        scroll : '#list-chain-scroll'
    });
    $('#chain-list').on('click', "li", function(){
        var _k = $(this).attr("address_k");
        if (_k) {
            insertHtmlAddress(_k);
            $(this).addClass('selected').siblings().removeClass('selected');
            $('#address-wrapper').find('.header-l > a').click();
        }
    });
	$('.btn').click(function(){
		if($.sValid()){
			var true_name = $('#true_name').val();
			var mob_phone = $('#mob_phone').val();
			var address = $('#address').val();
			var city_id = $('#area_info').attr('data-areaid2');
			var area_id = $('#area_info').attr('data-areaid');
			var area_info = $('#area_info').val();
			var is_default = $('#is_default').attr("checked") ? 1 : 0;
			$.ajax({
				type:'post',
				url:ApiUrl+"/index.php?act=member_address&op="+op,	
				data:{
				    key:key,
				    true_name:true_name,
				    mob_phone:mob_phone,
				    city_id:city_id,
				    area_id:area_id,
				    address:address,
				    area_info:area_info,
				    is_default:is_default,
				    chain_id:chain_id,
					address_id:address_id
				},
				dataType:'json',
				success:function(result){
					if(result){
						location.href = WapSiteUrl+'/tmpl/member/address_list.html';
					}else{
						location.href = WapSiteUrl;
					}
				}
			});
		}
	});

    // 选择地区
    $('#area_info').on('click', function(){
        $.areaSelected({
            success : function(data){
                chain_list(data.area_id);
                chain_id = 0;
                $('#address').val('');
                $('#area_info').val(data.area_info).attr({'data-areaid':data.area_id, 'data-areaid2':(data.area_id_2 == 0 ? data.area_id_1 : data.area_id_2)});
                $('#address').click();
            }
        });
    });
});
function insertHtmlAddress(address_k) {
    var _info = address_list[address_k];
    chain_id = _info.chain_id;
    $('#address').val(_info.chain_name+'，'+_info.chain_address);
}
function chain_list(area_id) {
    $.ajax({
        url: ApiUrl + "/index.php?act=member_address&op=chain_list",
        data: {key: key, area_id: area_id},
        type: "post",
        dataType: "json",
        success: function(result) {
            var html = template.render('list-chain-script', result.datas);
            $("#chain-list").html(html);
            address_list = result.datas.chain_list;
        }
    });
}