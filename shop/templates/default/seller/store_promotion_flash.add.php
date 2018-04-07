<?php defined('InShopNC') or exit('Access Invalid!');?>
<link rel="stylesheet" type="text/css" href="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/themes/ui-lightness/jquery.ui.css"  />
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
    <?php if(empty($output['flash_info'])) { ?>
    <form id="add_form" action="index.php?act=store_promotion_flash&op=flash_save" method="post" enctype="multipart/form-data">
    <?php } else { ?>
    <form id="add_form" action="index.php?act=store_promotion_flash&op=flash_edit_save" method="post" enctype="multipart/form-data">
        <input type="hidden" name="flash_id" value="<?php echo $output['flash_info']['flash_id'];?>">
    <?php } ?>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['flash_name'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="flash_name" name="flash_name" type="text"  maxlength="25" class="text w400" value="<?php echo empty($output['flash_info'])?'':$output['flash_info']['flash_name'];?>"/>
          <span></span>
        <p class="hint"><?php echo $lang['flash_name_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt>活动标题<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="flash_title" name="flash_title" type="text"  maxlength="10" class="text w200" value="<?php echo empty($output['flash_info'])?'':$output['flash_info']['flash_title'];?>"/>
          <span></span>
        <p class="hint"><?php echo $lang['flash_title_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt>活动描述<?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="flash_explain" name="flash_explain" type="text"  maxlength="30" class="text w400" value="<?php echo empty($output['flash_info'])?'':$output['flash_info']['flash_explain'];?>"/>
          <span></span>
        <p class="hint"><?php echo $lang['flash_explain_explain'];?></p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>闪购品牌<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <div class="ncsc-upload-thumb flash_brand">
          <p class="picture"><?php if(empty($output['flash_info']['flash_brand'])){ ?>
          <i class="icon-picture"></i>
          <?php } else {?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_brand'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="flash_brand_c" value="<?php echo $output['flash_info']['flash_brand'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_brand']:'';?>">
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="flash_brand" id="flash_brand" nc_type="change_thumb"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">请使用宽度150像素、高度35像素的图片，支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>闪购活动图片<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <div class="ncsc-upload-thumb flash_pic">
          <p class="picture"><?php if(empty($output['flash_info']['flash_pic'])){ ?>
          <i class="icon-picture"></i>
          <?php } else {?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_pic'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="flash_pic_c" value="<?php echo $output['flash_info']['flash_pic'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_pic']:'';?>">
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="flash_pic" id="flash_pic" nc_type="change_thumb"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">请使用宽度590像素、高度210像素的图片，支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>闪购横幅图片<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <div class="ncsc-upload-thumb flash_banner">
          <p class="picture"><?php if(empty($output['flash_info']['flash_banner'])){ ?>
          <i class="icon-picture"></i>
          <?php } else {?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_banner'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="flash_banner_c" value="<?php echo $output['flash_info']['flash_banner'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_banner']:'';?>">
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="flash_banner" id="flash_banner" nc_type="change_thumb"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">请使用宽度2000像素、高度385像素的图片，支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i>闪购推荐图片<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <div class="ncsc-upload-thumb flash_recommend_pic">
          <p class="picture"><?php if(empty($output['flash_info']['flash_recommend_pic'])){ ?>
          <i class="icon-picture"></i>
          <?php } else {?>
          <img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_recommend_pic'];?>" />
          <?php }?></p>
        </div>
        <input type="hidden" name="flash_recommend_pic_c" value="<?php echo $output['flash_info']['flash_recommend_pic'] !='' ?UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$output['flash_info']['flash_recommend_pic']:'';?>">
        <div class="ncsc-upload-btn"> <a href="javascript:void(0);"><span>
          <input type="file" hidefocus="true" size="1" class="input-file" name="flash_recommend_pic" id="flash_recommend_pic" nc_type="change_thumb"/>
          </span>
          <p><i class="icon-upload-alt"></i>图片上传</p>
          </a> 
        </div>        
        <span class="error"></span>
        <p class="hint">请使用宽度240像素、高度144像素的图片，支持jpg、jpeg、gif、png格式上传。</p>
      </dd>
    </dl>
    <?php if(empty($output['flash_info'])) { ?>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['start_time'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="start_time" name="start_time" type="text" class="text w130" /><em class="add-on"><i class="icon-calendar"></i></em><span></span>
        <p class="hint">
<?php if (!$output['isOwnShop'] && $output['current_flash_quota']['start_time'] > 1) { ?>
        <?php echo sprintf($lang['flash_add_start_time_explain'],date('Y-m-d H:i',$output['current_flash_quota']['start_time']));?>
<?php } ?>
        </p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo $lang['end_time'];?><?php echo $lang['nc_colon'];?></dt>
      <dd>
          <input id="end_time" name="end_time" type="text" class="text w130"/><em class="add-on"><i class="icon-calendar"></i></em><span></span>
        <p class="hint">
<?php if (!$output['isOwnShop']) { ?>
        <?php echo sprintf($lang['flash_add_end_time_explain'],date('Y-m-d H:i',$output['current_flash_quota']['end_time']));?>
<?php } ?>
        </p>
      </dd>
    </dl>
    <?php } ?>
    <dl>
      <dt><i class="required">*</i>购买上限<?php echo $lang['nc_colon'];?></dt>
      <dd>
        <input id="upper_limit" name="upper_limit" type="text" class="text w130" value="<?php echo empty($output['flash_info'])?'1':$output['flash_info']['upper_limit'];?>"/><span></span>
        <p class="hint">参加活动的最大购买数量，0为不限制，默认为1</p>
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
<script>
$(document).ready(function(){
    <?php if(empty($output['flash_info'])) { ?>
    $('#start_time').datetimepicker({
        controlType: 'select'
    });

    $('#end_time').datetimepicker({
        controlType: 'select'
    });
    <?php } ?>

    $('input[nc_type="change_thumb"]').change(function(){
      var src = getFullPath($(this)[0]);
      $(this).parentsUntil('dl').find('p.picture').html('<img src="'+src+'">');
      $(this).parentsUntil('dl').find('input:hidden').val(src).change();
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
            var error_td = element.parent('dd').children('span');
            error_td.append(error);
        },
        onfocusout: false,
    	submitHandler:function(form){
    		ajaxpost('add_form', '', '', 'onerror');
    	},
        rules : {
            flash_name : {
                required : true
            },
            start_time : {
                required : true,
                greaterThanDate : '<?php echo date('Y-m-d H:i',$output['current_flash_quota']['start_time']);?>'
            },
            end_time : {
                required : true,
<?php if (!$output['isOwnShop']) { ?>
                lessThanDate : '<?php echo date('Y-m-d H:i',$output['current_flash_quota']['end_time']);?>',
<?php } ?>
                greaterThanStartDate : true
            },
            upper_limit: {
                required: true,
                digits: true,
                min: 0
            },
            flash_brand_c : {
              required : true
            },
            flash_pic_c : {
              required : true
            },
            flash_banner_c : {
              required : true
            },
            flash_recommend_pic_c : {
              required : true
            }
        },
        messages : {
            flash_name : {
                required : '<i class="icon-exclamation-sign"></i><?php echo $lang['flash_name_error'];?>'
            },
            start_time : {
            required : '<i class="icon-exclamation-sign"></i>开始时间不能为空',
                greaterThanDate : '<i class="icon-exclamation-sign"></i><?php echo sprintf($lang['flash_add_start_time_explain'],date('Y-m-d H:i',TIMESTAMP));?>'
            },
            end_time : {
            required : '<i class="icon-exclamation-sign"></i>结束时间不能为空',
<?php if (!$output['isOwnShop']) { ?>
                lessThanDate : '<i class="icon-exclamation-sign"></i><?php echo sprintf($lang['flash_add_end_time_explain'],date('Y-m-d H:i',$output['current_flash_quota']['end_time']));?>',
<?php } ?>
                greaterThanStartDate : '<i class="icon-exclamation-sign"></i><?php echo $lang['greater_than_start_time'];?>'
            },
            upper_limit: {
                required : '<i class="icon-exclamation-sign"></i>购买上限不能为空',
                digits: '<i class="icon-exclamation-sign"></i>购买上限必须为数字',
                min: '<i class="icon-exclamation-sign"></i>购买上限不能小于0'
            },
            flash_brand_c : {
              required : '<i class="icon-exclamation-sign"></i>请上传闪购品牌图片'
            },
            flash_pic_c : {
              required : '<i class="icon-exclamation-sign"></i>请上传闪购活动图片'
            },
            flash_banner_c : {
              required : '<i class="icon-exclamation-sign"></i>请上传闪购横幅图片'
            },
            flash_recommend_pic_c : {
              required : '<i class="icon-exclamation-sign"></i>请上传闪购推荐图片'
            }
        }
    });
});
</script>
