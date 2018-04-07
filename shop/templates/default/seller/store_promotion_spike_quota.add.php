<?php defined('InShopNC') or exit('Access Invalid!'); ?>

<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<div class="ncsc-form-default">
  <form id="add_form" action="index.php?act=store_promotion_spike&op=spike_quota_add_save" method="post">
    <dl>
      <dt><i class="required">*</i><?php echo $lang['spike_quota_add_quantity'].$lang['nc_colon'];?></dt>
      <dd>
          <input id="spike_quota_quantity" name="spike_quota_quantity" type="text" class="text w30" /><em class="add-on"><?php echo $lang['text_month'];?></em><span></span>
        <p class="hint"><?php echo $lang['spike_price_explain1'];?></p>
        <p class="hint"><?php echo $lang['spike_price_explain2'].$output['setting_config']['promotion_spike_price'].$lang['nc_yuan'];?> ; </p>
        <p class="hint"><strong style="color: red">相关费用会在店铺的账期结算中扣除</strong></p>
      </dd>
    </dl>
    <div class="bottom">
      <label class="submit-border"><input id="submit_button" type="submit" class="submit" value="<?php echo $lang['nc_submit'];?>"></label>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/common.js" charset="utf-8"></script>
<script type="text/javascript">
$(document).ready(function(){
    //页面输入内容验证
    $("#add_form").validate({
        errorPlacement: function(error, element){
            var error_td = element.parent('dd').children('span');
            error_td.append(error);
        },
    	submitHandler:function(form){
            var unit_price = <?php echo $output['setting_config']['promotion_spike_price'];?>;
            var quantity = $("#spike_quota_quantity").val();
            var price = unit_price * quantity;
            showDialog('<?php echo $lang['spike_quota_add_confirm'];?>'+price+'<?php echo $lang['nc_yuan'];?>', 'confirm', '', function(){
            	ajaxpost('add_form', '', '', 'onerror');
            	});
    	},
            rules : {
                spike_quota_quantity : {
                    required : true,
                    digits : true,
                    min : 1,
                    max : 12
                }
            },
                messages : {
                    spike_quota_quantity : {
                        required : "<i class='icon-exclamation-sign'></i><?php echo $lang['spike_quota_quantity_error'];?>",
                        digits : "<i class='icon-exclamation-sign'></i><?php echo $lang['spike_quota_quantity_error'];?>",
                        min : "<i class='icon-exclamation-sign'></i><?php echo $lang['spike_quota_quantity_error'];?>",
                        max : "<i class='icon-exclamation-sign'></i><?php echo $lang['spike_quota_quantity_error'];?>"
                    }
                }
    });
});
</script>
