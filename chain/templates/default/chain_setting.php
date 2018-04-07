<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>"/>
<div class="ncsc-form-default">
  <form id="add_form" method="post" action="<?php echo CHAIN_SITE_URL?>/index.php?act=setting&op=setting" enctype="multipart/form-data">
  	<input type="hidden" id="form_submit" name="form_submit" value="ok"/>
    <h3>门店基础信息填写</h3>
    <dl class="row">
      <dt class="tit">
        <label>店主账号</label>
      </dt>
      <dd class="opt"><?php echo $output['chain_info']['chain_user'];?><span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_name"><i class="required">*</i>门店名称</label>
      </dt>
      <dd class="opt">
        <input type="text" value="<?php echo $output['chain_info']['chain_name'];?>" id="chain_name" name="chain_name" class="text w300">
        <span class="err"></span>
        <p class="notic"> </p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="area_id_1">所在地区</label>
      </dt>
      <dd class="opt">
        <input id="chain_region" name="area_info" type="hidden" value="<?php echo $output['chain_info']['area_info'];?>" >
        <input id="_area_1" name="area_id_1" type="hidden" value="<?php echo $output['chain_info']['area_id_1'];?>" >
        <input id="_area_2" name="area_id_2" type="hidden" value="<?php echo $output['chain_info']['area_id_2'];?>" >
        <input id="_area_3" name="area_id_3" type="hidden" value="<?php echo $output['chain_info']['area_id_3'];?>" >
        <input id="_area_4" name="area_id_4" type="hidden" value="<?php echo $output['chain_info']['area_id_4'];?>" >
        <input id="_area" name="area_id" type="hidden" value="<?php echo $output['chain_info']['area_id'];?>" >
        <span class="err"></span>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_address"><i class="required">*</i>门店地址</label>
      </dt>
      <dd class="opt">
        <input type="text" value="<?php echo $output['chain_info']['chain_address'];?>" id="chain_address" name="chain_address" class="text w300" />
        <span class="err"></span>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_address"><i class="required">*</i>门店电话</label>
      </dt>
      <dd class="opt">
        <input type="text" value="<?php echo $output['chain_info']['chain_phone'];?>" id="chain_phone" name="chain_phone" class="text w300" />
        <span class="err"></span>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
         <label for="chain_address"><i class="required">*</i>营业时间</label>
      </dt>
      <dd class="opt">
        <textarea  rows="3" class="textarea w300" id="chain_opening_hours" name="chain_opening_hours"><?php echo $output['chain_info']['chain_opening_hours'];?></textarea>
        <span class="err"></span>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_address">交通线路</label>
      </dt>
      <dd class="opt">
        <textarea  rows="3" class="textarea w400 h600" id="chain_traffic_line" name="chain_traffic_line"><?php echo $output['chain_info']['chain_traffic_line'];?></textarea>
        <span class="err"></span>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_img">门店图片</label>
      </dt>
      <dd class="opt">
        <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
          <input class="w200" type="file" name="chain_img">
        <span class="err"></span>
        <p class="notic"> </p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_logo">门店LOGO</label>
      </dt>
      <dd class="opt">
        <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
          <input class="w200" type="file" name="chain_logo">
        <span class="err"></span>
        <p class="notic"> </p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="chain_banner">门店横幅</label>
      </dt>
      <dd class="opt">
        <a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>" alt="" style="max-height: 30px;" /> </a>
          <input class="w200" type="file" name="chain_banner">
        <span class="err"></span>
        <p class="notic"> </p>
      </dd>
    </dl>
    <h3>门店运营信息填写</h3>
    <dl class="row">
      <dt class="tit">
        <label for="is_self_take">支持自提</label>
      </dt>
      <dd class="opt">
        <div class="onoff">
          <label for="is_self_take1" class="cb-enable <?php if($output['chain_info']['is_self_take'] == '1'){ ?>selected<?php } ?>" >是</label>
          <label for="is_self_take0" class="cb-disable <?php if($output['chain_info']['is_self_take'] == '0'){ ?>selected<?php } ?>" >否</label>
          <input id="is_self_take1" name="is_self_take" <?php if($output['chain_info']['is_self_take'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
          <input id="is_self_take0" name="is_self_take" <?php if($output['chain_info']['is_self_take'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
        </div>
        <span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row">
      <dt class="tit">
        <label for="is_transport">支持配送</label>
      </dt>
      <dd class="opt">
        <div class="onoff">
          <label for="is_transport1" class="cb-enable <?php if($output['chain_info']['is_transport'] == '1'){ ?>selected<?php } ?>" >是</label>
          <label for="is_transport0" class="cb-disable <?php if($output['chain_info']['is_transport'] == '0'){ ?>selected<?php } ?>" >否</label>
          <input id="is_transport1" name="is_transport" <?php if($output['chain_info']['is_transport'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_transport_info').show();" value="1" type="radio">
          <input id="is_transport0" name="is_transport" <?php if($output['chain_info']['is_transport'] == '0'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_transport_info').hide();" value="0" type="radio">
        </div>
        <span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <div id="tr_chain_transport_info">
      <dl class="row">
        <dt class="tit">
          <label for="start_amount_price">起送金额</label>
        </dt>
        <dd class="opt">
          <input type="text" name="start_amount_price" id="start_amount_price" value="<?php echo $output['chain_info']['start_amount_price'];?>" class="w70" > &nbsp;元
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="transport_freight">配送费</label>
        </dt>
        <dd class="opt">
          <input type="text" name="transport_freight" id="transport_freight" value="<?php echo $output['chain_info']['transport_freight'];?>" class="w70" > &nbsp;元
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="transport_rule">配送方式</label>
        </dt>
        <dd class="opt">
          <input name="transport_rule" <?php if($output['chain_info']['transport_rule'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_transport_area_info').hide();$('#tr_transport_distance_info').show();" value="1" type="radio"> 配送半径 &nbsp;&nbsp;
          <input name="transport_rule" <?php if($output['chain_info']['transport_rule'] == '2'){ ?>checked="checked"<?php } ?> onclick="$('#tr_transport_distance_info').hide();$('#tr_transport_area_info').show();" value="2" type="radio"> 配送区域
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
      <dl class="row" id="tr_transport_distance_info">
        <dt class="tit">
          <label for="transport_distance">配送半径</label>
        </dt>
        <dd class="opt">
          <input type="text" name="transport_distance" id="transport_distance" value="<?php echo $output['chain_info']['transport_distance'];?>" class="w70" > &nbsp;千米
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
      <dl class="row" id="tr_transport_area_info">
        <dt class="tit">
          <label for="transport_areas">配送区域</label>
        </dt>
        <dd class="opt">              
          <?php foreach((array)$output['chain_info']['transport_area_arr'] as $value){?>
            <label style="white-space: nowrap">
              <input type="checkbox" name="transport_areas[]" value="<?php echo $value['area_id'];?>" <?php if(in_array($value['area_id'], (array)$output['chain_info']['transport_areas'])){?> checked="checked" <?php }?> >&nbsp;<?php echo $value['area_name'];?>
            </label>
          <?php }?>
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
    </div>
    <dl class="row">
       <dt class="tit">
         <label for="is_forward_order">转接订单</label>
       </dt>
       <dd class="opt">
         <div class="onoff">
           <label for="is_forward_order1" class="cb-enable <?php if($output['chain_info']['is_forward_order'] == '1'){ ?>selected<?php } ?>" >是</label>
           <label for="is_forward_order0" class="cb-disable <?php if($output['chain_info']['is_forward_order'] == '0'){ ?>selected<?php } ?>" >否</label>
          <input id="is_forward_order1" name="is_forward_order" <?php if($output['chain_info']['is_forward_order'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_forward_order_info').show();" value="1" type="radio">
          <input id="is_forward_order0" name="is_forward_order" <?php if($output['chain_info']['is_forward_order'] == '0'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_forward_order_info').hide();" value="0" type="radio">
        </div>
        <span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <div id="tr_chain_forward_order_info">
      <dl class="row">
        <dt class="tit">
          <label for="is_auto_forward">自动转接订单</label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="is_auto_forward1" class="cb-enable <?php if($output['chain_info']['is_auto_forward'] == '1'){ ?>selected<?php } ?>" >是</label>
            <label for="is_auto_forward0" class="cb-disable <?php if($output['chain_info']['is_auto_forward'] == '0'){ ?>selected<?php } ?>" >否</label>
            <input id="is_auto_forward1" name="is_auto_forward" <?php if($output['chain_info']['is_auto_forward'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="is_auto_forward0" name="is_auto_forward" <?php if($output['chain_info']['is_auto_forward'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="express_city">配送城市</label>
        </dt>
        <dd class="opt">              
          <input type="hidden" name="express_city" value="<?php echo $output['chain_info']['express_city'];?>">
          <input type="hidden" name="express_city_name" value="<?php echo trim($output['chain_info']['express_city_name'],',');?>">
          <span class="area-group"><p style="display:inline-block">
          <?php if(empty($output['chain_info']['express_city'])){ ?>
          未添加地区
          <?php } else { ?>
          <?php echo trim($output['chain_info']['express_city_name'],',');?>
          <?php } ?></p></span>
          <input id="chain_express_city" class="input-btn" value="编辑" type="button">
          <span class="err"></span>
          <p class="notic"> </p>
        </dd>
      </dl>
    </div>
    <dl class="row">
      <dt class="tit">
        <label for="is_collection">支持代收货</label>
      </dt>
      <dd class="opt">
        <div class="onoff">
          <label for="is_collection1" class="cb-enable <?php if($output['chain_info']['is_collection'] == '1'){ ?>selected<?php } ?>" >是</label>
          <label for="is_collection0" class="cb-disable <?php if($output['chain_info']['is_collection'] == '0'){ ?>selected<?php } ?>" >否</label>
          <input id="is_collection1" name="is_collection" <?php if($output['chain_info']['is_collection'] == '1'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_collection_price_info').show();" value="1" type="radio">
          <input id="is_collection0" name="is_collection" <?php if($output['chain_info']['is_collection'] == '0'){ ?>checked="checked"<?php } ?> onclick="$('#tr_chain_collection_price_info').hide();" value="0" type="radio">
        </div>
        <span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <dl class="row" id="tr_chain_collection_price_info">
      <dt class="tit">
        <label for="collection_price"><em>*</em>代收货费用</label>
      </dt>
      <dd class="opt">
        <input type="text" name="collection_price" value="<?php echo $output['chain_info']['collection_price'];?>" class="w70">&nbsp;元
        <span class="err"></span>
        <p class="notic"></p>
      </dd>
    </dl>
    <div class="bottom">
	      <a id='btn_add' class="nc-btn" href="javascript:void(0);" style="display: inline-block ;"><?php echo $lang['nc_submit'];?></a>
	      
    </div>
  </form>
  <div class="ks-ext-mask" style="position: fixed; left: 0px; top: 0px; width: 100%; height: 100%; z-index: 999; display:none"></div>
  <div id="dialog_areas" class="dialog-areas" style="display:none">
    <div class="ks-contentbox">
      <div class="title">选择区域<a class="ks-ext-close" href="javascript:void(0)">X</a></div>
      <form method="post">
        <ul id="J_CityList">
<?php $i = 1; $areas = $output['areas']; foreach ($areas['region'] as $region => $provinceIds) { ?>
<li<?php if ($i % 2 == 0) echo ' class="even"'; ?>>
  <dl class="ncsc-region">
    <dt class="ncsc-region-title">
      <span>
      <input type="checkbox" id="J_Group_<?php echo $i; ?>" class="J_Group" value=""/>
      <label for="J_Group_<?php echo $i; ?>"><?php echo $region; ?></label>
      </span>
    </dt>
    <dd class="ncsc-province-list">
<?php foreach ($provinceIds as $provinceId) { ?>
      <div class="ncsc-province"><span class="ncsc-province-tab">
        <input type="checkbox" class="J_Province" id="J_Province_<?php echo $provinceId; ?>" value="<?php echo $provinceId; ?>"/>
        <label for="J_Province_<?php echo $provinceId; ?>"><?php echo $areas['name'][$provinceId]; ?></label>
        <span class="check_num"/> </span><i class="icon-angle-down trigger"></i>
        <div class="ncsc-citys-sub">
<?php foreach ($areas['children'][$provinceId] as $cityId) { ?>
          <span class="areas">
          <input type="checkbox" class="J_City" id="J_City_<?php echo $cityId; ?>" value="<?php echo $cityId; ?>"/>
          <label for="J_City_<?php echo $cityId; ?>"><?php echo $areas['name'][$cityId]; ?></label>
          </span>
<?php } ?>
          <p class="tr hr8"><a href="javascript:void(0);" class="ncbtn-mini ncbtn-bittersweet close_button">关闭</a></p>
        </div>
        </span>
      </div>
<?php } ?>

    </dd>
  </dl>
</li>
<?php $i++; } ?>
        </ul>
        <div class="bottom"> <a href="javascript:void(0);" class="J_Submit ncbtn ncbtn-mint">确定</a> <a href="javascript:void(0);" class="J_Cancel ncbtn">取消</a> </div>
      </form>
    </div>
  </div>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common_select.js" charset="utf-8"></script> 
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.nyroModal/custom.min.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script>
var SHOP_SITE_URL = '<?php echo SHOP_SITE_URL;?>';
$(function(){
  //自定义radio样式
  $(".cb-enable").click(function(){
        var parent = $(this).parents('.onoff');
        $('.cb-disable',parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox',parent).attr('checked', true);
  });
  $(".cb-disable").click(function(){
        var parent = $(this).parents('.onoff');
        $('.cb-enable',parent).removeClass('selected');
        $(this).addClass('selected');
        $('.checkbox',parent).attr('checked', false);
  });
  $("#chain_region").nc_region();
  $('a[nctype="nyroModal"]').nyroModal();
  $('input[name=is_transport][value=<?php echo $output['chain_info']['is_transport'];?>]').trigger('click');
  $('input[name=transport_rule][value=<?php echo $output['chain_info']['transport_rule'];?>]').trigger('click');
  $('input[name=is_forward_order][value=<?php echo $output['chain_info']['is_forward_order'];?>]').trigger('click');
  $('input[name=is_collection][value=<?php echo $output['chain_info']['is_collection'];?>]').trigger('click');

  $("#btn_add").click(function(){
    if($("#add_form").valid()){
      $("#add_form").submit();
    }
	});

  $('#add_form').validate({
    errorPlacement: function(error, element){
      var error_td = element.parent('dd').children('span.err');
      error_td.append(error);
    },
    submitHandler:function(form){
        ajaxpost('add_form', '', '', 'onerror');
    },
    rules : {
      chain_name: {
        required : true
      },
      area_info : {
        checklast: true
      },
      chain_address : {
        required : true
      },
      chain_phone : {
        required : true
      },
      chain_opening_hours : {
        required : true
      },
      start_amount_price : {
        required : function(){return $('input[name=is_transport]').val() == 1;},
        digits   : true,
        min      : 0,
        max      : 9999
      },
      transport_freight : {
        required : function(){return $('input[name=is_transport]').val() == 1;},
        number   : true,
        min      : 0,
        max      : 9999
      },
      transport_distance : {
        required : function(){return $('input[name=transport_rule]').val() == 1;},
        digits   : true,
        min      : 1,
        max      : 99
      },
      collection_price : {
        required : function(){return $('input[name=is_collection]').val() == 1;},
        number   : true,
        min      : 0,
        max      : 99
      }
    },
    messages : {
      chain_name: {
        required : '<i class="fa fa-exclamation-circle"></i>门店名称不能为空'
      },
      area_info : {
        checklast : '<i class="icon-exclamation-sign"></i>请将地区选择完整'
      },
      chain_address : {
        required : '<i class="icon-exclamation-sign"></i>请填写详细地址'
      },
      chain_phone : {
        required : '<i class="icon-exclamation-sign"></i>请填写联系方式'
      },
      chain_opening_hours : {
        required : '<i class="icon-exclamation-sign"></i>请填写营业时间'
      },
      start_amount_price : {
        required : '<i class="icon-exclamation-sign"></i>请填写起送金额',
        digits   : '<i class="icon-exclamation-sign"></i>起送金额为0-9999的整数',
        min      : '<i class="icon-exclamation-sign"></i>起送金额为0-9999的整数',
        max      : '<i class="icon-exclamation-sign"></i>起送金额为0-9999的整数'
      },
      transport_freight : {
        required : '<i class="icon-exclamation-sign"></i>请填写配送费',
        digits   : '<i class="icon-exclamation-sign"></i>配送费为0-9999的数字',
        min      : '<i class="icon-exclamation-sign"></i>配送费为0-9999的数字',
        max      : '<i class="icon-exclamation-sign"></i>配送费为0-9999的数字'
      },
      transport_distance : {
        required : '<i class="icon-exclamation-sign"></i>请填写配送半径',
        digits   : '<i class="icon-exclamation-sign"></i>配送半径为1-99的正整数',
        min      : '<i class="icon-exclamation-sign"></i>配送半径为1-99的正整数',
        max      : '<i class="icon-exclamation-sign"></i>配送半径为1-99的正整数'
      },
      collection_price : {
        required : '<i class="icon-exclamation-sign"></i>请填写代收货费用',
        digits   : '<i class="icon-exclamation-sign"></i>代收货费用为0-99的数字',
        min      : '<i class="icon-exclamation-sign"></i>代收货费用为0-99的数字',
        max      : '<i class="icon-exclamation-sign"></i>代收货费用为0-99的数字'
      }
    }
  });
});

	$('#dialog_areas').on('click','.ks-ext-close',function(){
	    $("#dialog_areas").css('display','none');
	    $("#dialog_batch").css('display','none');
	    $('.ks-ext-mask').css('display','none');
	    return false;
	});
	$('#dialog_areas').on('click','.J_Cancel',function(){
	    $("#dialog_areas").css('display','none');
	    $("#dialog_batch").css('display','none');
	    $('.ks-ext-mask').css('display','none');
	});
    $("#chain_express_city").click(function(){
        //取消所有已选择的checkbox
		$('#J_CityList').find('input[type="checkbox"]').attr('checked',false).attr('disabled',false);

		//取消显示所有统计数量
		$('#J_CityList').find('.check_num').html('');
		SelectArea = new Array();

		//取得当前行隐藏域内的city值，放入SelectArea数组中
		var expAreas = $('input[name="express_city"]').val();
		expAreas = expAreas.split(',');
		try{
			if(expAreas[0] != ''){
				for(var v in expAreas){
					SelectArea[expAreas[v]] = true;
				}
			}
			//初始化已选中的checkbox
			$('#J_CityList').find('.ncsc-province').each(function(){
				var count = 0;
				$(this).find('input[type="checkbox"]').each(function(){
					if(SelectArea[$(this).val()]==true){
						$(this).attr('checked',true);
						if($(this)[0].className!='J_Province') count++;
					}
				});
				if (count > 0){
					$(this).find('.check_num').html('('+count+')');
				}
	
			});

			//循环每一行，如果一行省都选中，则大区载选中
			$('#J_CityList>li').each(function(){
				$(this).find('.J_Group').attr('checked',true);
				father = this;
				$(this).find('.J_Province').each(function(){
					if (!$(this).attr('checked')){
						$(father).find('.J_Group').attr('checked',false);
						return ;
					}
				});
			});
		}catch(ex){}
		//定位弹出层的坐标
		var pos = $(this).position();
		$("#dialog_areas").css({'position' : 'fixed','display' : 'block', 'z-index' : '9999'});
		$('.ks-ext-mask').css('display','block');
    });
	$('#dialog_areas').on('click','.J_Province',function(){
		if ($(this).attr('checked')){
			//选择所有未被disabled的子地区
			$(this).parent().find('.ncsc-citys-sub').eq(0).find('input[type="checkbox"]').each(function(){
				if (!$(this).attr('disabled')){
					$(this).attr('checked',true);
				}else{
					$(this).attr('checked',false);
				}
			});
			//计算并显示所有被选中的子地区数量
			num = '('+$(this).parent().find('.ncsc-citys-sub').eq(0).find('input:checked').size()+')';
			if (num == '(0)') num = '';
			$(this).parent().parent().find(".check_num").eq(0).html(num);

			//如果该大区域所有省都选中，该区域选中
			input_checked 	= $(this).parent().parent().parent().find('input:checked').size();
			input_all 		= $(this).parent().parent().parent().find('input[type="checkbox"]').size();
			if (input_all == input_checked){
				$(this).parent().parent().parent().parent().find('.J_Group').attr('checked',true);
			}	

		}else{
			//取消全部子地区选择，取消显示数量
			$(this).parent().parent().find(".check_num").eq(0).html('');
			$(this).parent().find('.ncsc-citys-sub').eq(0).find('input[type="checkbox"]').attr('checked',false);
			//取消大区域选择
			$(this).parent().parent().parent().parent().find('.J_Group').attr('checked',false);
		}
	});
	$('#dialog_areas').on('click','.J_Group',function(){
		if ($(this).attr('checked')){
			//区域内所有没有被disabled复选框选中，带disabled说明已经被选择过了，不能再选
			$(this).parent().parent().parent().find('input[type="checkbox"]').each(function(){
				if (!$(this).attr('disabled')){
					$(this).attr('checked',true);
				}else{
					$(this).attr('checked',false);
				}				
			});
			//循环显示每个省下面的市级的数量
			$(this).parent().parent().parent().find('.ncsc-province-list').find('.ncsc-province').each(function(){
				//显示该省下面已选择的市的数量
				num = '('+$(this).find('.ncsc-citys-sub').find('input:checked').size()+')';
				//如果是0，说明没有选择，不显示数量
				if (num != '(0)'){
					$(this).find(".check_num").html(num);
				}
			});
		}else{
			//区域内所有筛选框取消选中
			$(this).parent().parent().parent().find('input[type="checkbox"]').attr('checked',false);
			//循环清空每个省下面显示的市级数量
			$(this).parent().parent().parent().find('.ncsc-province-list').find('.ncsc-province').each(function(){
				$(this).find(".check_num").html('');
			});
		}

	});
	$('#dialog_areas').on('click','.close_button',function(){ 
	    $(this).parent().parent().parent().parent().removeClass('showCityPop');
	});
	$('#dialog_areas').on('click','.J_City',function(){
		//显示选择市级数量，在所属省后面
		num = '('+$(this).parent().parent().find('input:checked').size()+')';
		if (num=='(0)')num='';
		$(this).parent().parent().parent().find(".check_num").eq(0).html(num);
		//如果市级地区全部选中，则父级省份也选中，反之有一个不选中,则省份和大区域也不选中
		if (!$(this).attr('checked')){
			//取消省份选择
			$(this).parent().parent().parent().find('.J_Province').attr('checked',false);
			//取消大区域选择
			$(this).parent().parent().parent().parent().parent().parent().find('.J_Group').attr('checked',false);
		}else{
			//如果该省所有市都选中，该省选中
			input_checked 	= $(this).parent().parent().find('input:checked').size();
			input_all 		= $(this).parent().parent().find('input[type="checkbox"]').size();
			if (input_all == input_checked){
				$(this).parent().parent().parent().find('.J_Province').attr('checked',true);
			}
			//如果该大区域所有省都选中，该区域选中
			input_checked 	= $(this).parent().parent().parent().parent().parent().find('input:checked').size();
			input_all 		= $(this).parent().parent().parent().parent().parent().find('input[type="checkbox"]').size();
			if (input_all == input_checked){
				$(this).parent().parent().parent().parent().parent().parent().find('.J_Group').attr('checked',true);
			}
		}
	});
	$('#dialog_areas').on('click','.trigger',function () {
		objTrigger = this;objHead = $(this).parent();objPanel = $(this).next();
		if ($(this).next().css('display') == 'none'){
			//隐藏所有已弹出的省份下拉层，只显示当前点击的层
			$('.ks-contentbox').find('.ncsc-province').removeClass('showCityPop');
			$(this).parent().parent().addClass('showCityPop');
		}else{
			//隐藏当前的省份下拉层
			$(this).parent().parent().removeClass('showCityPop');
		}
		//点击省，市所在的head与panel层以外的区域均隐藏当前层
        var oHandle = $(this);
		var de = document.documentElement?document.documentElement : document.body;
        de.onclick = function(e){
	        var e = e || window.event;
	        var target = e.target || e.srcElement;
	        var getTar = target.getAttribute("id");
	        while(target){
	        	//循环最外层一个时，会出现异常
				try{
					//jquery 转成DOM对象，比较两个DOM对象
	                if(target==$(objHead)[0])return true;
	                if(target==$(objPanel)[0])return true;
				}catch(ex){};
	            target = target.parentNode;
	        }
	        $(objTrigger).parent().parent().removeClass('showCityPop');
        }
	});
	$('#dialog_areas').on('click','.J_Submit',function (){
		var CityText = '', CityText2 = '', CityValue = '';
		//首先找市被全部选择的省份
		$('#J_CityList').find('.ncsc-province-tab').each(function(){
			var a = $(this).find('input[type="checkbox"]').size();
			var b = $(this).find('input:checked').size();
			//市被全选的情况
			if (a == b){
				CityText += ($(this).find('.J_Province').next().html())+',';
			}else{
				//市被部分选中的情况
				$(this).find('.J_City').each(function(){
						//计算并准备传输选择的区域值（具体到市级ID），以，隔开
							if ($(this).attr('checked')){
								CityText2 += ($(this).next().html())+',';
							}
				});
			}
		});
		CityText += CityText2;

		//记录弹出层内所有已被选择的checkbox的值(省、市均记录)，记录到CityValue，SelectArea中
		$('#J_CityList').find('.ncsc-province-list').find('input[type="checkbox"]').each(function(){
			if ($(this).attr('checked')){
				CityValue += $(this).val()+',';
			}
		});

		//去掉尾部的逗号
		CityText = CityText.replace(/(,*$)/g,'');
		CityValue = CityValue.replace(/(,*$)/g,'');

		//返回选择的文本内容
		if (CityText == '')CityText = '未添加地区';
		$('.area-group>p').html(CityText);
		//返回选择的值到隐藏域
		$('input[name="express_city"]').val(CityValue);
		$('input[name="express_city_name"]').val(CityText);
		//关闭弹出层与遮罩层
	    $("#dialog_areas").css('display','none');
	    $('.ks-ext-mask').css('display','none');
	    //清空check_num显示的数量
		$(".check_num").html('');
		$('#J_CityList').find('input[type="checkbox"]').attr('checked',false);
	});
</script>