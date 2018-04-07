<?php defined('InShopNC') or exit('Access Invalid!');?>

  <form id="admin_form" method="post" action='<?php echo urlAdminShop('order', 'add_refund');?>&order_id=<?php echo $output['order']['order_id']; ?>&goods_id=<?php echo $output['goods']['rec_id']; ?>' name="adminForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          退款原因：
        </dt>
        <dd class="opt">
              <select class="w150" name="reason_id">
                <?php if (is_array($output['reason_list']) && !empty($output['reason_list'])) { ?>
                <?php foreach ($output['reason_list'] as $key => $val) { ?>
                <option value="<?php echo $val['reason_id'];?>"><?php echo $val['reason_info'];?></option>
                <?php } ?>
                <?php } ?>
                <option value="0" selected>其他</option>
              </select>
          <span class="err"></span>
          <p class="notic"></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          退款金额：
        </dt>
        <dd class="opt">
          <input type="text" class="text w50" name="refund_amount" value="<?php echo $output['goods']['goods_pay_price']; ?>" /> 元
          （最多 <strong class="green" title="可退金额由系统根据订单商品实际成交额和已退款金额自动计算得出。"><?php echo $output['goods']['goods_pay_price']; ?></strong> 元）
          <span class="err"></span>
          <p class="notic">退款金额不能超过可退金额。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          是否退货：
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="state1" class="cb-enable" title="<?php echo $lang['nc_yes'];?>"><?php echo $lang['nc_yes'];?></label>
            <label for="state0" class="cb-disable selected" title="<?php echo $lang['nc_no'];?>"><?php echo $lang['nc_no'];?></label>
            <input id="state1" name="refund_type" value="2" type="radio">
            <input id="state0" name="refund_type" checked="checked" value="1" type="radio">
          </div>
          <span class="err"></span>
          <p class="notic">如果需退货，在商家审核同意后，买家要先寄回商品才能进行退款。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          退货数量：
        </dt>
        <dd class="opt">
          <input type="text" class="text w50" name="goods_num" value="<?php echo $output['goods']['goods_num']; ?>" />
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
});
</script>