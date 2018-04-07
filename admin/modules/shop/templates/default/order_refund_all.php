<?php defined('InShopNC') or exit('Access Invalid!');?>

  <form id="admin_form" method="post" action='<?php echo urlAdminShop('order', 'add_refund_all');?>&order_id=<?php echo $output['order']['order_id']; ?>' name="adminForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          退款原因：
        </dt>
        <dd class="opt">
          取消订单，全部退款<?php echo $output['order']['order_type'] == 2 ? '[订金不退]' : null;?>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          退款金额：
        </dt>
        <dd class="opt">
          <strong class="green"><?php echo ncPriceFormat($output['order']['allow_refund_amount']); ?></strong> 元
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          退款说明：
        </dt>
        <dd class="opt">
          <textarea name="buyer_message" rows="3" class="textarea w300">平台管理员替买家申请退款</textarea>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
<script type="text/javascript">
$(function(){$("#submitBtn").click(function(){
     ajaxpost('admin_form', '', '', 'onerror');
	});
});
</script>