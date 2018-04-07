<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="eject_con chain_eject_con">
<form method="post" action="<?php echo CHAIN_SITE_URL?>/index.php?act=chain_order&op=send&order_id=<?php echo $output['order_info']['order_id'];?>" id="post_form">
  <input type="hidden" name="form_submit" value="ok" />
    <div class="content">
      <dl>
        <dt>物流公司<?php echo $lang['nc_colon'];?></dt>
        <dd>
          <select name="shipping_express_id">
            <option value="0">-请选择-</option>
            <?php if(!empty($output['express_list']) && is_array($output['express_list'])){?>
            <?php foreach($output['express_list'] as $key=> $val){?>
            <option value="<?php echo $val['id']; ?>" <?php if ($output['order_info']['extend_order_common']['shipping_express_id'] == $val['id']) {?>selected<?php }?>><?php echo $val['e_name']; ?></option>
            <?php } ?>
            <?php } ?>
          </select>
        </dd>
      </dl>
      <dl>
        <dt><i class="required">*</i>物流单号<?php echo $lang['nc_colon'];?></dt>
        <dd>
          <input type="text" class="text w150" name="shipping_code" value="<?php echo $output['order_info']['shipping_code'];?>" />
          <span class="error"></span>
          <p class="hint">请填写正确的快递单号，方便跟踪配送信息。</p>
        </dd>
      </dl>
        <dl>
          <dt>发货备忘<?php echo $lang['nc_colon'];?></dt>
          <dd>
            <textarea name="deliver_explain" rows="2" class="textarea w300"><?php echo $output['order_info']['extend_order_common']['deliver_explain'];?></textarea>
          </dd>
        </dl>
    </div>
    <div class="bottom">
        <label class="submit-border"><input type="submit" class="submit" id="confirm_button" value="确定" /></label>
    </div>
</form>
</div>
<script>
$(function(){
    //input焦点时隐藏/显示填写内容提示信息
    $('#post_form').validate({
        errorPlacement: function(error, element){
            element.next().append(error);
        },
        submitHandler:function(form){
            ajaxpost('post_form', '', '', 'onerror');
        },
        rules : {
            shipping_code : {
                required : true
            }
        },
        messages : {
            shipping_code : {
                required : '请填写物流单号'
            }
        }
    });
});
</script>