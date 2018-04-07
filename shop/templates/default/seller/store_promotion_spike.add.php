<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <?php if(empty($output['spike_info'])) { ?>
    <form id="add_form" action="index.php?act=store_promotion_spike&op=spike_save" method="post" enctype="multipart/form-data">
    <?php } else { ?>
    <form id="add_form" action="index.php?act=store_promotion_spike&op=spike_edit_save" method="post" enctype="multipart/form-data">
        <input type="hidden" name="spike_id" value="<?php echo $output['spike_info']['spike_id'];?>">
    <?php } ?>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['spike_name'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="spike_name" name="spike_name" type="text"  maxlength="25" class="text w400" value="<?php echo empty($output['spike_info'])?'':$output['spike_info']['spike_name'];?>"/>
          <span class="error"></span>
        <p class="hint"><?php echo $lang['spike_name_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt>活动标题<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="spike_title" name="spike_title" type="text"  maxlength="10" class="text w200" value="<?php echo empty($output['spike_info'])?'':$output['spike_info']['spike_title'];?>"/>
          <span class="error"></span>
        <p class="hint"><?php echo $lang['spike_title_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt>活动描述<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="spike_explain" name="spike_explain" type="text"  maxlength="30" class="text w400" value="<?php echo empty($output['spike_info'])?'':$output['spike_info']['spike_explain'];?>"/>
          <span class="error"></span>
        <p class="hint"><?php echo $lang['spike_explain_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo '品牌秒杀推荐背景'.$lang['nc_colon'];?></dt>
      <dd>
        <div class="ncsc-upload-thumb spike-common-bg" nctype="spike_common_bg">
          <p><?php if(empty($output['spike_info']['spike_common_bg'])){ ?>
          <i class="icon-picture"></i>
          <?php } else {?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['spike_info']['spike_common_bg'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="spike_common_bg_c" value="<?php echo $output['spike_info']['spike_common_bg'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['spike_info']['spike_common_bg']:'';?>">
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="spike_common_bg" id="spikeCommonBg" nc_type="change_common_bg"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">图片用于首页品牌秒杀推荐背景,请使用宽度595像素、高度405像素、大小1M内的图片，<br>
支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo '品牌秒杀横幅'.$lang['nc_colon'];?> </dt>
      <dd>       
        <div class="ncsc-upload-thumb spike-banner" nctype="spike_banner">
          <p><?php if(empty($output['spike_info']['spike_banner'])){?>
          <i class="icon-picture"></i>
          <?php }else{?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['spike_info']['spike_banner'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="spike_banner_c" value="<?php echo $output['spike_info']['spike_banner'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['spike_info']['spike_banner']:'';?>">       
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="spike_banner" id="spikeBannerPic" nc_type="change_spike_banner"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">图片用于品牌秒杀专题页,请使用宽度1200像素、高度360像素、大小1M内的图片，<br>
支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>    
    <?php if(empty($output['spike_info'])) { ?>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['start_time'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="start_time" name="start_time" type="text" class="text w130" /><em class="add-on"><i class="icon-calendar"></i></em>
          <span class="error"></span>
        <p class="hint">
<?php if (!$output['isOwnShop'] && $output['current_spike_quota']['start_time'] > 1) { ?>
        <?php echo sprintf($lang['spike_add_start_time_explain'],date('Y-m-d H:i',$output['current_spike_quota']['start_time']));?>
<?php } ?>
        </p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['end_time'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="end_time" name="end_time" type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em>
          <span class="error"></span>
        <p class="hint">
<?php if (!$output['isOwnShop']) { ?>
        <?php echo sprintf($lang['spike_add_end_time_explain'],date('Y-m-d H:i',$output['current_spike_quota']['end_time']));?>
<?php } ?>
        </p>
      </dd>
    </dl>
    <?php } ?>
    <dl>
      <dt><i class="required">*</i>购买上限<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <input id="upper_limit" name="upper_limit" type="text" class="text w130" value="<?php echo empty($output['spike_info'])?'1':$output['spike_info']['upper_limit'];?>"/><span class="error"></span>
        <p class="hint">参加活动的单个订单最大购买数量，0为不限制，默认为1</p>
      </dd>
    </dl>
    <dl>
      <dt>订单上限<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <input id="order_limit" name="order_limit" type="text" class="text w130" value="<?php echo empty($output['spike_info'])?'0':$output['spike_info']['order_limit'];?>"/><span class="error"></span>
        <p class="hint">购买单个商品的订单数量，0为不限制，默认为0</p>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input id="submit_button" type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"></label>
    </div>
  </form>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.js"></script>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui-timepicker-addon/jquery-ui-timepicker-addon.min.css"  />
<script type="text/javascript">
var SITEURL = "<?php echo SHOP_SITE_URL; ?>";

$(document).ready(function(){
    <?php if(empty($output['spike_info'])) { ?>
    $('#start_time').datetimepicker({
        controlType: 'select'
    });

    $('#end_time').datetimepicker({
        controlType: 'select'
    });
    <?php } ?>
    
    $('input[nc_type="change_common_bg"]').change(function(){
      var src = getFullPath($(this)[0]);
      $('div[nctype="spike_common_bg"]').find('p').html('<img src="'+src+'">');
      $('input[name="spike_common_bg_c"]').val(src).change();
    });
    $('input[nc_type="change_spike_banner"]').change(function(){
      var src = getFullPath($(this)[0]);
      $('div[nctype="spike_banner"]').find('p').html('<img src="'+src+'">');
      $('input[name="spike_banner_c"]').val(src).change();
    });

    jQuery.validator.methods.greaterThanDate = function(value, element, param) {
        var date1 = new Date(Date.parse(param.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 < date2;
    };
    jQuery.validator.methods.lessThanDate = function(value, element, param) {
        var date1 = new Date(Date.parse(param.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 > date2;
    };
    jQuery.validator.methods.greaterThanStartDate = function(value, element) {
        var start_date = $("#start_time").val();
        var date1 = new Date(Date.parse(start_date.replace(/-/g, "/")));
        var date2 = new Date(Date.parse(value.replace(/-/g, "/")));
        return date1 < date2;
    };

    //页面输入内容验证
    $("#add_form").validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span.error');
            error_td.append(error);
        },
        onfocusout: false,
    	  submitHandler:function(form){
    		ajaxpost('add_form', '', '', 'onerror');
    	  },
        rules : {
            spike_name : {
                required : true
            },
            start_time : {
                required : true,
                greaterThanDate : '<?php echo date('Y-m-d H:i',$output['current_spike_quota']['start_time']);?>'
            },
            end_time : {
                required : true,
                <?php if (!$output['isOwnShop']) { ?>
                lessThanDate : '<?php echo date('Y-m-d H:i',$output['current_spike_quota']['end_time']);?>',
                <?php } ?>
                greaterThanStartDate : true
            },
            upper_limit: {
                required: true,
                digits: true,
                min: 0
            },
            spike_common_bg_c : {
              required : true
            },
            spike_banner_c : {
              required : true
            }
        },
        messages : {
            spike_name : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['spike_name_error'];?>'
            },
            start_time : {
                required : '<i class="icon-exclamation-sign"></i>开始时间不能为空',
                greaterThanDate : '<i class="icon-exclamation-sign"></i><?php echo sprintf($lang['spike_add_start_time_explain'],date('Y-m-d H:i',TIMESTAMP));?>'
            },
            end_time : {
                required : '<i class="icon-exclamation-sign"></i>结束时间不能为空',
                <?php if (!$output['isOwnShop']) { ?>
                lessThanDate : '<i class="icon-exclamation-sign"></i><?php echo sprintf($lang['spike_add_end_time_explain'],date('Y-m-d H:i',$output['current_spike_quota']['end_time']));?>',
                <?php } ?>
                greaterThanStartDate : '<i class="icon-exclamation-sign"></i><?php echo $lang['greater_than_start_time'];?>'
            },
            upper_limit: {
                required : '<i class="icon-exclamation-sign"></i>购买上限不能为空',
                digits: '<i class="icon-exclamation-sign"></i>购买上限必须为数字',
                min: '<i class="icon-exclamation-sign"></i>购买上限不能小于0'
            },
            spike_common_bg_c : {
              required : '<i class="icon-exclamation-sign"></i>品牌秒杀推荐背景不能为空'
            },
            spike_banner_c : {
              required : '<i class="icon-exclamation-sign"></i>品牌秒杀横幅不能为空'
            }
        }
    });
});
</script>
