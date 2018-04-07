<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.ui-timepicker-select{ padding: 2px 0px; }
</style>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
    	<a class="back" href="javascript:history.go(-1);" title="返回">
        	<i class="fa fa-arrow-circle-o-left"></i>
      	</a>
      <div class="subject">
        <h3>大转盘管理</h3>
        <h5>大转盘活动发布及相关设置</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>设置指针距离顶部距离、指针图片、大转盘图片,设置完成之后点击保存可查看转盘图片，确认提交之后生效</li>
      <li>转盘初始位置指针必须与最后一个奖项和第一个奖项的分割线重合</li>
    </ul>
  </div>
    <form id="add_form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="lot_id" value="<?php echo $output['dial_info']['lot_id'];?>" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label for="turntableimage"><em>*</em><?php echo '转盘图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
              <span class="show"> 
                <a class="nyroModal" rel="gal" href="<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_dial.png";}?>"> 
                  <i class="fa fa-picture-o" onMouseOver="toolTip('<img src=\'<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_dial.png";}?>\'>')" onMouseOut="toolTip()"></i>
                </a>
              </span>
              <span class="type-file-box">
                <input name="textfield" id="textfield2" class="type-file-text" type="text" value="<?php echo $output['dial_info']['lot_dial_bg'];?>">
                <input name="button" id="button2" value="选择上传..." class="type-file-button" type="button">
                <input class="type-file-file" id="turntableimage" name="turntableimage" size="30" value="<?php echo $output['dial_info']['lot_dial_bg'];?>" hidefocus="true" nc_type="upload_dial_bg" title="点击按钮选择文件并提交表单后上传生效" type="file">
              </span>
            </div>
            <span class="error"></span>
            <p class="notic">推荐图片尺寸500px*500px</p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="pointerimage"><em>*</em><?php echo '指针图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
            	<span class="show"> 
            		<a class="nyroModal" rel="gal" href="<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_pointer.png";}?>"> 
            			<i class="fa fa-picture-o" onMouseOver="toolTip('<img src=\'<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_pointer.png";}?>\'>')" onMouseOut="toolTip()"></i>
            		</a>
            	</span>
            	<span class="type-file-box">
		           <input name="textfield" id="textfield1" class="type-file-text" type="text" value="<?php echo $output['dial_info']['lot_dial_pointer'];?>">
		           <input name="button" id="button1" value="选择上传..." class="type-file-button" type="button">
		           <input class="type-file-file" id="pointerimage" name="pointerimage" size="30" hidefocus="true" nc_type="upload_dial_pointer" title="点击按钮选择文件并提交表单后上传生效" type="file" value="<?php echo $output['dial_info']['lot_dial_pointer'];?>">
            	</span>
            </div>
            <span class="error"></span>
            <p class="notic">推荐图片尺寸150px*240px</p>
          </dd>
        </dl>        
        <dl class="row">
          <dt class="tit">
            <label for="active_bg_image"><?php echo '活动背景图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
              <span class="show"> 
                <a class="nyroModal" rel="gal" href="<?php echo is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg']) ? UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg'] : "";?>"> 
                  <i class="fa fa-picture-o" onMouseOver="toolTip('<img src=\'<?php echo is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg']) ? UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg'] : "";?>\'>')" onMouseOut="toolTip()"></i>
                </a>
              </span>
              <span class="type-file-box">
               <input name="textfield" id="textfield3" class="type-file-text" type="text" value="<?php echo $output['dial_info']['lot_bg'];?>">
               <input name="button" id="button3" value="选择上传..." class="type-file-button" type="button">
               <input class="type-file-file" id="active_bg_image" name="active_bg_image" size="30" hidefocus="true" nc_type="active_bg_image" title="点击按钮选择文件并提交表单后上传生效" type="file" value="<?php echo $output['dial_info']['lot_bg'];?>">
              </span>
            </div>
            <span class="error"></span>
            <p class="notic">推荐图片尺寸800px*1200px</p>
          </dd>
        </dl>
        <?php if($output['dial_info']['lot_state'] != 1){?>
        <dl class="row">
          <dt class="tit"><em>*</em><?php echo '活动时间';?><?php echo $lang['nc_colon'];?></dt>
          <dd class="opt">
            <input value="<?php echo date('Y-m-d H:i',$output['dial_info']['start_time']?$output['dial_info']['start_time']:TIMESTAMP); ?>" id="start_time" name="start_time" type="text" class="text w110 calendar-time" />
            <em class="add-on" ><i class="icon-calendar"></i></em><span></span>
             到&nbsp;
            <input value="<?php echo date('Y-m-d H:i',$output['dial_info']['end_time']?$output['dial_info']['end_time']:TIMESTAMP+86400); ?>" id="end_time" name="end_time" type="text" class="text w110 calendar-time"/>
            <em class="add-on"><i class="icon-calendar"></i></em><span></span>
            <span class="error"></span>
          </dd>
        </dl>
        <?php }?>
        <dl class="row">
          <dt class="tit">
            <label for="wintips"><em>*</em><?php echo '活动名称';?></label>
          </dt>
          <dd class="opt">
            <input name="wintips" type="text" id="wintips" class="text w200" value="<?php echo $output['dial_info']['lot_name'];?>">
            <span class="error"></span>
            <p class="notic"></p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="acexplain"><?php echo '活动说明';?></label>
          </dt>
          <dd class="opt">
            <textarea name="acexplain" id="acexplain" rows="6" class="tarea"><?php echo $output['dial_info']['lot_discription'];?></textarea>
            <span class="error"></span>
            <p class="notic"></p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="rate_weight"><em>*</em><?php echo '中奖率';?></label>
          </dt>
          <dd class="opt">
            <input name="rate_weight" type="text" id="rate_weight" class="text w80" value="<?php echo $output['dial_info']['lot_weight'];?>">&nbsp;%&nbsp;
            <span class="error"></span>
            <p class="notic"></p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>抽取方式</label>
          </dt>
          <dd class="opt">
            <select name="show_type" id="show_type">
                <option value="0" <?php if($output['dial_info']['lot_type']== 0){?> checked="checked"<?php }?> >按会员抽取</option>
                <option value="1" <?php if($output['dial_info']['lot_type']== 1){?> checked="checked"<?php }?> >按订单抽取</option>
            </select>
            <span class="error"></span>
            <p class="notic mb10">如选择按会员抽取，则商城内所有会员都将拥有一定的大转盘抽奖次数</p>
            <p class="notic">如选择按订单抽取，则在活动时间内每完成一笔订单进行一次抽取</p>
          </dd>
        </dl>
        <dl class="row" id="member_num" <?php if($output['dial_info']['lot_type'] == 1){?> style="display: none"<?php }?>>
          <dt class="tit">
            <label for="member_number"><em>*</em><?php echo '每个会员ID抽取次数';?></label>
          </dt>
          <dd class="opt">
            <input type="text" id="member_number" name="member_number" class="txt w100 mr5" value="<?php echo $output['dial_info']['lot_count']?$output['dial_info']['lot_count']:1;?>">次
            <span class="error"></span>
            <p class="notic">会员抽取次数限定在1到10之间</p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit"><em>*</em>奖项设置</dt>
          <dd class="opt" id="pricerang_table">
            <ul class="ncap-lot-ajax-add">
            <?php if(!empty($output['dial_info']['lot_info'])){ $count_prize = count($output['dial_info']['lot_info']);?>
            <?php foreach($output['dial_info']['lot_info'] as $k => $val){?>
              <li index-data="<?php echo $k;?>">
                <div class="rate_info">
                  <label>奖项名称：</label>
                  <input type="text" class="txt w100 mr5" name="rate_name[<?php echo $k;?>]" value="<?php echo $val['rate_name']?>">
                  <label class="ml20 mr10">奖品类型：</label>
                  <select name="prize_type[<?php echo $k;?>]">
                    <option value="0" <?php if($val['prize_type'] == 0) echo 'selected="selected"';?>>未中奖</option>
                    <option value="1" <?php if($val['prize_type'] == 1) echo 'selected="selected"';?>>积分</option>
                    <option value="2" <?php if($val['prize_type'] == 2) echo 'selected="selected"';?>>平台红包</option>
                    <option value="3" <?php if($val['prize_type'] == 3) echo 'selected="selected"';?>>实物</option>
                  </select>
                </div>
                <div class="prize_info mt20 mb10">
                  <?php switch($val['prize_type']){
                      case 0:
                  ?>
                  <label>未中奖提示语：</label>
                  <input type="text" class="txt w250 mr15" name="prize[<?php echo $k;?>][unprize]" value="<?php echo $val['prize']['unprize']?>"/>     
                  <?php
                        break;
                      case 1:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>"/>
                  <label>奖励积分数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_num]" value="<?php echo $val['prize']['prize_num']?>"/>
                  <?php
                        break;
                      case 2:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>"/>
                  <span class="redpacket">
                    <span style="border:dashed 1px #E0E0E0; padding: 5px; "><i class="fa fa-cc-discover"></i><?php echo $val['prize']['coupon_title']?></span>
                    <input type="hidden" name="prize[<?php echo $k;?>][coupon_title]" value="<?php echo $val['prize']['coupon_title']?>"/>
                    <input type="hidden" name="prize[<?php echo $k;?>][coupon_quota]" value="<?php echo $val['prize']['coupon_quota']?>"/>
                    <input type="hidden" name="prize[<?php echo $k;?>][coupon_id]" value="<?php echo $val['prize']['coupon_id']?>"/>
                  </span>
                  <input type="hidden" name="rpacket_<?php echo $k;?>" value="<?php echo $val['prize']['coupon_id']?>"/>
                  <a href="JavaScript:void(0);" onclick="coupon_list(<?php echo $k;?>)" class="ncap-btn">选择平台红包</a>
                  <?php
                        break;
                      case 3:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>"/>
                  <label>实物名称：</label>
                  <input type="text" class="txt w250 mr5" name="prize[<?php echo $k;?>][prize_name]" value="<?php echo $val['prize']['prize_name']?>"/>
                  <?php 
                        break;
                    }
                  ?>
                </div>
                <?php if($k == $count_prize-1 && $k > 0){?>
                <a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red btn-del"><?php echo $lang['nc_del']; ?></a>
                <?php }?>
              </li>
            <?php }?>
            <?php }else{?>
              <li index-data="0">
                <div class="rate_info">
                  <label>奖项名称：</label>
                  <input type="text" class="txt w100 mr5" name="rate_name[0]">
                  <label class="ml20 mr10">奖品类型：</label>
                  <select name="prize_type[0]">
                    <option value="0" selected>未中奖</option>
                    <option value="1">积分</option>
                    <option value="2">平台红包</option>
                    <option value="3">实物</option>
                  </select>
                </div>
                <div class="prize_info mt20 mb10">
                  <label>未中奖提示语：</label>
                  <input type="text" class="txt w250 mr15" name="prize[0][unprize]" value=""/>
                </div>
              </li>
            <?php }?>  
            </ul>
            <a id="addrow" href="javascript:void(0);" class="ncap-btn mt20"><i class="fa fa-plus"></i>增加一行</a>
            <br/>
            <span class="error"></span>
            <p class="notic">积分、奖品数需为大于0的数字</p>
          </dd>
        </dl>
        <div class="bot"><a id="submitBtn" class="ncap-btn-big ncap-btn-green" href="JavaScript:void(0);"><?php echo $lang['nc_submit'];?></a></div>
      </div>
    </form>
</div>
<script src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/jquery.numberAnimation.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/highcharts.js"></script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL?>/js/statistics.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.css"  />
<script type="text/javascript">
  function coupon_list(index){
      var etime = $('#end_time').val();
      _uri = "index.php?act=lottery_dial&op=get_coupon&index="+index+"&end_time="+etime;
      CUR_DIALOG = ajax_form('cou_lists', '获取平台免费领取红包列表', _uri, 580);
  }
</script>
<script>
  var reSubmit = true;
  //按钮先执行验证再提交表单
  $(function(){
    var li_num = <?php echo count($output['dial_info']['lot_info'])>1?count($output['dial_info']['lot_info']):1;?>;
    $('#start_time').datetimepicker({
      controlType: 'select'
    });
    $('#end_time').datetimepicker({
      controlType: 'select'
    });

    // 模拟默认用户图片上传input type='file'样式
    $("input.type-file-file").change(function(){
      var filepath=$(this).val();
      var extStart=filepath.lastIndexOf(".");
      var ext=filepath.substring(extStart,filepath.length).toUpperCase();
      if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
        alert("<?php echo $lang['default_img_wrong'];?>");
        $(this).attr('value','');
        return false;
      }
      $(this).parent('span').find('input.type-file-text').val(filepath);
      if($(this).parents('div.input-file-show').hasClass('redb')){
        $(this).parents('div.input-file-show').removeClass('redb');
        $(this).parents('dd').find('.error').html('');
      }
    });

    // 抽取用户
    $('#show_type').change(function(){
       if(parseInt($(this).val()) == 0){
          $('#member_num').show();
        }else{
          $('#member_num').hide();        
        }
    });

    // 增加一行
    $('#addrow').click(function(){
      var max_size = 10;
      var cur_count = $('ul.ncap-lot-ajax-add li').size();
      if(cur_count >= max_size){
        alert('最多设置'+max_size+'个奖项');
        return false;
      }
      var html = '<li index-data="'+li_num+'"><div class="rate_info mt20">';
      html += '<label>奖项名称：</label><input type="text" class="txt w100 mr5" name="rate_name['+li_num+']">';
      html += '<label class="ml20 mr10">奖品类型：</label><select name="prize_type['+li_num+']">';
      html += '<option value="0" selected>未中奖</option><option value="1">积分</option><option value="2">平台红包</option>';
      html += '<option value="3">实物</option></select></div>';
      html += '<div class="prize_info mt20 mb10">';
      html += '<label>未中奖提示语：</label><input type="text" class="txt w250 mr15" name="prize['+li_num+'][unprize]"/>';
      html += '</div><a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red btn-del"><?php echo $lang['nc_del']; ?></a></li>';
      if(li_num > 1){
        $('li[index-data="'+(parseInt(li_num)-1)+'"]').find('.btn-del').remove();
      }
      li_num++;
      $('ul.ncap-lot-ajax-add').append(html);
    });

    // 删除一行
    $('ul.ncap-lot-ajax-add').on('click','li > .btn-del',function(){
      var index = parseInt($(this).parents('li').attr('index-data'))-1;
      if(index > 0){
      	var html = '<a href="JavaScript:void(0);" class="ncap-btn ncap-btn-red btn-del"><?php echo $lang['nc_del']; ?></a>';
      	$('li[index-data="'+index+'"]').append(html);
      } 
      li_num--;
      $(this).parent('li').remove();
    });

    // 设置奖品类型
    $('ul.ncap-lot-ajax-add').on('change','li > div.rate_info > select',function(){
      var index = parseInt($(this).parents('li').attr('index-data'));
      var prize_type = parseInt($(this).val());
      var html = '<label>奖品数：</label><input type="text" class="txt w100 mr5" name="prize['+index+'][prize_amount]" value=""/> ';
      if(prize_type == 1){
        html += '<label>奖励积分数：</label><input type="text" class="txt w100 mr5" name="prize['+index+'][prize_num]" value=""/>';
      }else if(prize_type == 2){
        html += '<span class="redpacket"></span>';
        html += '<input type="hidden" name="rpacket_'+index+'" value=""/>';
        html += '&nbsp;<a href="JavaScript:void(0);" onclick="coupon_list('+index+')" class="ncap-btn">选择平台红包</a>';
      }else if(prize_type == 3){
        html += '<label>实物名称：</label><input type="text" class="txt w250 mr5" name="prize['+index+'][prize_name]" value=""/>';
      }else{
        html = '<label>未中奖提示语：</label><input type="text" class="txt w250 mr15" name="prize['+index+'][unprize]" value=""/>';
      }
      $(this).parents('div.rate_info').siblings('div.prize_info').html(html);
      if($('.ncap-lot-ajax-add input.redb').size() == 0){
        $(this).parents('dd').find('.error').html('');
      }
    });

    $('form').on('change','.redb',function(){
      var change_flag = false;
      var err_tag = $(this);
      if(err_tag.val() != '' || !isNaN(err_tag.val())){
        err_tag.removeClass('redb');
        if($('.ncap-lot-ajax-add input.redb').size() == 0){
          $(this).parents('dd').find('.error').html('');
        }
      }
      if(change_flag){
        err_tag.removeClass('redb');
        $(this).parents('dd').find('.error').html('');
      }
    });


    // 表单提交验证
    $("#submitBtn").click(function(){
      if(!reSubmit){
        return;
      }
      var flag = true;
      // 奖项设置验证
      $('.ncap-lot-ajax-add input').each(function(){
        if($(this).val() == ''){
          flag = false;
          $(this).addClass('redb');
          $(this).parents('dd').find('span.error').html('请完善奖项设置');
        }
      });
      if($("#add_form").valid() && flag){
        reSubmit = false;
        $("#add_form").submit();
      }else{
        reSubmit = true;
      }
    });

    $.validator.addMethod('checkData', function(value,element){
        _time_stamp1 = Date.parse(new Date($('#start_time').val()));
        _time_stamp2 = Date.parse(new Date($('#end_time').val()));
        if (_time_stamp2 <= 0) {
            return true;
        }
        if (_time_stamp1 >= _time_stamp2) {
            return false;
        }else {
            return true;
        }
    }, '');

    $('#add_form').validate({
        errorPlacement: function(error, element){
            $(element).parents('dd').find('span.error').append(error);
        },
        rules : {
            turntableimage : {
                required    : function(){
                  return $('#textfield2').val() == '';
                }
            },
            pointerimage : {
                required   : function(){
                  return $('#textfield1').val() == '';
                }
            },
            start_time : {
                required    : true,
                checkData   : true
            },
            end_time : {
                required   : true,
                checkData   : true
            },
            wintips : {
                required   : true
            },
            rate_weight : {
                required   : true,
                number     : true,
                max        : 100,
                min        : 0.01
            },
            member_number : {
                required   : function(){
                  return $('#show_type').val() == 1;
                },
                number     : true,
                max        : 10,
                min        : 1
            }
        },
        messages : {
            turntableimage  : {
                required    : '<i class="fa fa-exclamation-circle"></i>转盘图片不能为空'
            },
            pointerimage : {
                required   : '<i class="fa fa-exclamation-circle"></i>指针图片不能为空'
            },
            start_time : {
                required    : '<i class="fa fa-exclamation-circle"></i>开始时间不能为空',
                checkData   : '<i class="fa fa-exclamation-circle"></i>开始时间必须小于结束时间'
            },
            end_time : {
                required   : '<i class="fa fa-exclamation-circle"></i>结束时间不能为空',
                checkData   : '<i class="fa fa-exclamation-circle"></i>结束时间必须大于开始时间'
            },
            wintips : {
                required   : '<i class="fa fa-exclamation-circle"></i>活动名称不能为空'
            },
            rate_weight : {
                required   : '<i class="fa fa-exclamation-circle"></i>中奖率不能为空',
                number   : '<i class="fa fa-exclamation-circle"></i>中奖率必须为大于0.01小于等于100的数字',
                max   : '<i class="fa fa-exclamation-circle"></i>中奖率必须为大于0.01小于等于100的数字',
                min   : '<i class="fa fa-exclamation-circle"></i>中奖率必须为大于0.01小于等于100的数字'
            },
            member_number : {
                required   : '<i class="fa fa-exclamation-circle"></i>每个会员ID抽取次数不能为空',
                number   : '<i class="fa fa-exclamation-circle"></i>每个会员ID抽取次数必须为1-10之间的整数',
                max   : '<i class="fa fa-exclamation-circle"></i>每个会员ID抽取次数必须为1-10之间的整数',
                min   : '<i class="fa fa-exclamation-circle"></i>每个会员ID抽取次数必须为1-10之间的整数'
            }
        }
    });
});
</script>
