<link type="text/css" rel="stylesheet" href="<?php echo RESOURCE_SITE_URL."/js/jquery-ui/themes/ui-lightness/jquery.ui.css";?>"/>
<div class="ncsc-form-default">
  <form id="add_form" method="post" action="<?php echo CHAIN_SITE_URL?>/index.php?act=voucher&op=<?php echo $output['type']=='add'?'templateadd':'templateedit'; ?>">
  	<input type="hidden" id="act" name="act" value="voucher"/>
  	<?php if ($output['type'] == 'add'){?>
  	<input type="hidden" id="op" name="op" value="templateadd"/>
  	<?php }else {?>
  	<input type="hidden" id="op" name="op" value="templateedit"/>
  	<input type="hidden" id="tid" name="tid" value="<?php echo $output['t_info']['voucher_t_id'];?>"/>
  	<?php }?>
  	<input type="hidden" id="form_submit" name="form_submit" value="ok"/>
    <h3>门店优惠券信息填写</h3>
    <dl>
      <dt><i class="required">*</i><?php echo '代金券名称'.$lang['nc_colon']; ?></dt>
      <dd>
        <input type="text" class="w300 text" name="txt_template_title" value="<?php echo $output['t_info']['voucher_t_title'];?>" maxlength=50 />
        <span></span>
      </dd>
    </dl>
    <dl>
      <dt><em class="pngFix"></em><?php echo '有效期'.$lang['nc_colon']; ?></dt>
      <dd>
      	<input type="text" class="text w70" id="txt_template_enddate" name="txt_template_enddate" value="" readonly><em class="add-on"><i class="icon-calendar"></i></em>
        <span></span><p class="hint">
           留空则默认30天之后到期
           </p>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo '面额'.$lang['nc_colon']; ?></dt>
      <dd>
        <input type="text" name="txt_template_price" id="txt_template_price" class="text w70" value="<?php echo $output['t_info']['voucher_t_price'];?>"><em class="add-on"><i class="icon-renminbi"></i></em>
        <span></span>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo '每人限领'.$lang['nc_colon']; ?></dt>
      <dd>
      	<select name="eachlimit" class="w80">
      		<option value="0">不限</option>
      		<?php for($i=1;$i<=intval(C('promotion_voucher_buyertimes_limit'));$i++){?>
      		<option value="<?php echo $i;?>" <?php echo $output['t_info']['voucher_t_eachlimit'] == $i?'selected':'';?>><?php echo $i;?>张</option>
      		<?php }?>
        </select>
      </dd>
    </dl>
    <dl>
      <dt><i class="required">*</i><?php echo '消费金额'.$lang['nc_colon']; ?></dt>
      <dd>
        <input type="text" name="txt_template_limit" class="text w70" value="<?php echo $output['t_info']['voucher_t_limit'];?>"><em class="add-on"><i class="icon-renminbi"></i></em>
        <span></span>
        <p class="hint">代金券的消费金额须大于面额</p>
      </dd>
    </dl> 
    <dl>
	      <dt><i class="required">*</i><?php echo '代金券描述'.$lang['nc_colon']; ?></dt>
	      <dd>
	        <textarea  name="txt_template_describe" class="textarea w400 h600"><?php echo $output['t_info']['voucher_t_desc'];?></textarea>
	        <span></span>
	      </dd>
    </dl>
	      <?php if ($output['type'] == 'edit'){?>
	      <dl>
	      	<dt><em class="pngFix"></em><?php echo $lang['nc_status'].$lang['nc_colon']; ?></dt>
	      	<dd>
	      		<input type="radio" value="<?php echo $output['templatestate_arr']['usable'][0];?>" name="tstate" <?php echo $output['t_info']['voucher_t_state'] == $output['templatestate_arr']['usable'][0]?'checked':'';?>> <?php echo $output['templatestate_arr']['usable'][1];?>
	      		<input type="radio" value="<?php echo $output['templatestate_arr']['disabled'][0];?>" name="tstate" <?php echo $output['t_info']['voucher_t_state'] == $output['templatestate_arr']['disabled'][0]?'checked':'';?>> <?php echo $output['templatestate_arr']['disabled'][1];?>
	      	</dd>
    </dl>
    <?php }?>
    <div class="bottom">
	    
	      <a id='btn_add' class="submit" href="javascript:void(0);"><?php echo $lang['nc_submit'];?></a>
	    
	      <a href="index.php?act=voucher" class="nc-btn" style="display: inline-block ;">返回</a>
    </div>
  </form>
</div>
<script src="<?php echo RESOURCE_SITE_URL;?>/js/jquery-ui/i18n/zh-CN.js"></script>
<script>
$(document).ready(function(){
    //日期控件
    $('#txt_template_enddate').datepicker();
    var currDate = new Date();
    var date = currDate.getDate();
    date = date + 1;
    currDate.setDate(date);
    $('#txt_template_enddate').datepicker( "option", "minDate", currDate);
    $('#txt_template_enddate').val("<?php echo $output['t_info']['voucher_t_end_date']?@date('Y-m-d',$output['t_info']['voucher_t_end_date']):'';?>");

    $("#btn_add").click(function(){
        if($("#add_form").valid()){
        	$("#add_form").submit();
    	}
	});

	jQuery.validator.addMethod("numberlimit", function(value, element) {
        var price_limit = parseFloat(value);
        var price_num = parseInt($('#txt_template_price').val());
        return price_limit > price_num;
    }, "");
	
    //表单验证
    $('#add_form').validate({
        errorPlacement: function(error, element){
	    	var error_td = element.parent('dd').children('span');
			error_td.append(error);
	    },
        submitHandler:function(form){
            ajaxpost('add_form', '', '', 'onerror');
        },
        rules : {
            txt_template_title: {
                required : true,
                rangelength:[1,50]
            },
            txt_template_price: {
                required : true,
                digits : true,
                min: 1
            },
            txt_template_limit: {
                required : true,
                number : true,
                numberlimit : true
            },
            txt_template_describe: {
                required : true,
                rangelength:[1,200]
			}
        },
        messages : {
            txt_template_title: {
                required : '<i class="icon-exclamation-sign"></i>代金券名称不能为空',
                rangelength : '<i class="icon-exclamation-sign"></i>代金券名称长度不能超过50个字'
            },
            txt_template_price: {
                required : '<i class="icon-exclamation-sign"></i>代金券面额不能为空',
                digits : '<i class="icon-exclamation-sign"></i>代金券面额必须为正整数',
                min: '<i class="icon-exclamation-sign"></i>代金券面额必须为正整数'
            },
            txt_template_limit: {
                required : '<i class="icon-exclamation-sign"></i>消费金额不能为空',
                number : '<i class="icon-exclamation-sign"></i>消费金额必须大于代金券面额',
                numberlimit : '<i class="icon-exclamation-sign"></i>消费金额必须大于代金券面额'
            },
            txt_template_describe: {
                required : '<i class="icon-exclamation-sign"></i>代金券描述不能为空',
                rangelength:'<i class="icon-exclamation-sign"></i>代金券描述长度不能超过200字'
			}
        }
    });
});
</script>