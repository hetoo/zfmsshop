<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="alert alert-block mt10">
  <ul class="mt5">
    <li>该列表可以查看门店代收的订单，对于有代收费用的，请收到后再进行出货操作。</li>
  </ul>
</div>
<div class="wrapper mt10" style="min-height: 400px;">
<form method="get" action="index.php" target="_self">
    <input type="hidden" name="act" value="chain_reciver" />
    <input type="hidden" name="op" value="index" />
  <table class="search-form">
    <tr>
      <td>&nbsp;</td>
      <th style="width: 100px;">门店代收状态</th>
      <td class="w100"><select name="search_state_type">
          <option value="no" <?php if ($_GET['search_state_type'] == 'no') {?>selected<?php }?>>待自提</option>
          <option value="yes" <?php if ($_GET['search_state_type'] == 'yes') {?>selected<?php }?>>已自提</option>
        </select></td>
      <th> <select name="search_key_type">
          <option value="dlyo_pickup_code" <?php if ($_GET['search_key_type'] == 'dlyo_pickup_code') {?>selected<?php }?>>提货码</option>
          <option value="order_sn" <?php if ($_GET['search_key_type'] == 'order_sn') {?>selected<?php }?>>订单号</option>
          <option value="reciver_mobphone" <?php if ($_GET['search_key_type'] == 'reciver_mobphone') {?>selected<?php }?>>手机号</option>
        </select>
      </th>
      <td class="w160"><input type="text" class="text w150" name="keyword" value="<?php echo $_GET['keyword']; ?>"/></td>
      <td class="tc w70"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
  <table class="ncd-table">
    <thead> 
      <tr>
        <th width="20%">商城订单</th>
        <th width="20%">物流运单</th>
        <th>收货人信息</th>
        <th width="10%" class="tc">代收费用</th>
        <th width="10%" class="tc">状态</th>
        <th width="10%" class="tc">操作</th>
      </tr>
     
    </thead>
    <tbody>
      <?php if(!empty($output['dorder_list'])){ ?>
      <?php foreach($output['dorder_list'] as $k => $v){ ?>
      <tr class="hover">
        <td><dl>
            <dt>订单号：<?php echo $v['order_sn'];?></dt>
            <dd class="date"><?php echo date('Y-m-d H:i:s', $v['addtime']);?></dd>
          </dl></td>
        <td><dl>
            <dt>运单号：<?php echo $v['shipping_code'];?><span class="express">(<?php echo $v['express_name'];?>)</span></dt>
            <dd>
                <?php if(!empty($v['shipping_code'])){ ?>
                <a href="javascript:void(0);" class="link" onclick="javascript:ajax_form('get_express', '查看物流', 'index.php?act=chain_reciver&op=get_express&e_code=<?php echo $v['express_code'];?>&shipping_code=<?php echo $v['shipping_code'];?>')">查看物流跟踪</a>
                <?php }?>
                </dd>
            
          </dl></td>
        <td><dl>
            <dt>收件人：<?php echo $v['reciver_name'];?></dt>
            <dd class="tel"><span>手机：<?php echo $v['reciver_mobphone'];?></span><span>座机：<?php echo $v['reciver_telphone'];?></span></dd>
            
          </dl></td>
        <td class="tc"><?php echo ncPriceFormat($v['chain_price']); ?></td>
        <td class="tc"><?php echo $output['dorder_state'][$v['dlyo_state']];?></td>
        <td class="tc handle"><?php if ($output['order_list'][$v['order_id']]['order_state'] == ORDER_STATE_SEND && $v['dlyo_state'] == 10) {?>
          <a href="javascript:void(0);" class="btn" onclick="javascript:ajax_get_confirm('请确认包裹已经到店，提醒买家取件？','<?php echo CHAIN_SITE_URL;?>/index.php?act=chain_reciver&op=arrive_point&order_id=<?php echo $v['order_id'];?>');">到店</a>
          <?php } else if ($v['dlyo_state'] == 20) {?>
          <a href="javascript:void(0);" class="btn" onclick="javascript:ajax_form('pickup_parcel', '取货', 'index.php?act=chain_reciver&op=pickup_parcel&order_id=<?php echo $v['order_id'];?>')">取货</a>
          <?php } else {?>
          --
          <?php }?></td>
      </tr>
      <?php } ?>
      <?php } else { ?>
      <tr>
        <td colspan="10"><div class="ncd-nodata">很抱歉，暂无任何数据</div></td>
      </tr>
      <?php } ?>
    </tbody>
    <tfoot>
      <?php if(!empty($output['dorder_list'])){ ?>
      <tr class="tfoot">
        <td colspan="16" class="tc"><div class="pagination"> <?php echo $output['show_page'];?> </div></td>
      </tr>
      <?php } ?>
    </tfoot>
  </table>
</div>