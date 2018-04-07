<div class="eject_con">
<?php if ($output['order_info']) {?>
  <form id="postform" method="post" action="index.php?act=store_deliver&op=chain&order_id=<?php echo $output['order_info']['order_id']; ?>" onsubmit="ajaxpost('postform', '', '', 'onerror');return false;">
    <input type="hidden" name="form_submit" value="ok" />
    <dl>
      <dt><?php echo $lang['store_order_sn'].$lang['nc_colon'];?></dt>
      <dd><span class="num"><?php echo $output['order_info']['order_sn']; ?></span>
        <?php if (is_array($output['chain_info']) && !empty($output['chain_info'])) { ?>
        <p class="hint">已经分派给门店<?php echo $lang['nc_colon'];?><?php echo $output['chain_info']['chain_name']; ?></p>
        <?php } else { ?>
        <p class="hint">等待管理员分派或门店拒绝接收订单</p>
        <?php } ?>
        </dd>
    </dl>
    <dl>
      <dt>选择门店<?php echo $lang['nc_colon'];?></dt>
      <dd>
      <select name="chain_id">
        <?php if (is_array($output['chain_list']) && !empty($output['chain_list'])) { ?>
        <?php foreach ($output['chain_list'] as $key => $val) { ?>
        <option value="<?php echo $val['chain_id']; ?>" <?php if ($output['order_info']['chain_id'] == $val['chain_id']) {?>selected<?php }?>>
            <?php echo $val['chain_name']; ?><?php if ($val['is_auto_forward']==0) {?>（需确认）<?php } ?></option>
        <?php } ?>
        <?php } ?>
      </select><p class="hint">部分门店需要人工确认订单后才发货</p>
      </dd>
    </dl>
    <div class="bottom">
        <label class="submit-border"><input type="submit" class="submit" value="<?php echo $lang['nc_ok'];?>" /></label>
    </div>
  </form>
<?php } else { ?>
<p style="line-height:80px;text-align:center">该订单并不存在，请检查参数是否正确!</p>
<?php } ?>
</div>