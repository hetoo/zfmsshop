<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="alert alert-block mt10">
  <ul class="mt5">
    <li>该列表可以查看店铺分派的待发货和已经发货订单，部分订单需要确认后才能发货。</li>
  </ul>
</div>
<form method="get" action="index.php" target="_self">
  <table class="search-form">
    <input type="hidden" name="act" value="chain_sender" />
    <input type="hidden" name="op" value="index" />
    <tr>
      <td>&nbsp;</td>
      <th style="width: 100px;">门店发货状态</th>
      <td class="w100"><select name="search_state_type">
          <option value="no" <?php if ($_GET['search_state_type'] == 'no') {?>selected<?php }?>>待发货</option>
          <option value="yes" <?php if ($_GET['search_state_type'] == 'yes') {?>selected<?php }?>>已发货</option>
        </select></td>
      <th> 订单编号
      </th>
      <td class="w160"><input type="text" class="text w150" name="keyword" value="<?php echo $_GET['keyword']; ?>"/></td>
      <td class="tc w70"><label class="submit-border">
          <input type="submit" class="submit" value="<?php echo $lang['nc_search'];?>" />
        </label></td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
    <tr nc_type="table_header">
    <th class="w20"></th>
      <th colspan="2">商品</th>
      <th class="w100">单价（元）</th>
      <th class="w50">数量</th>
      <th class="w100">订单金额（元）</th>
      <th class="w300">订单收货人信息</th>
      <th class="w80">订单状态</th>
      <th class="w100">操作</th>
    </tr>
  </thead>
  <tbody>
    <?php if (!empty($output['order_list']) && is_array($output['order_list'])) { ?>
    <?php foreach ($output['order_list'] as $order_info) { ?>
    <tr>
      <th colspan="20" class="tl"><span class="ml10">订单编号：<?php echo $order_info['order_sn'];?></span><span class="ml20">下单时间：<?php echo date('Y-m-d H:i:s',$order_info['add_time']);?></span></th>
    </tr>
    <?php $_i = 0;?>
    <?php $_cont = count($order_info['extend_order_goods']);?>
    <?php foreach ($order_info['extend_order_goods'] as $goods_info) { ?>
    <tr>
    <td class="bdl"></td>
      <td class="w70">
        <div class="goods-thumb"><a href="<?php echo $goods_info['goods_url'];?>" target="_blank"><img src="<?php echo $goods_info['image_url'] ?>"/></a></div></td>
        <td>
        <dl class="goods-info">
          <dt class="goods-name"><a href="<?php echo $goods_info['goods_url'];?>" target="_blank"><?php echo $goods_info['goods_name'];?></a></dt>
          <dd class="goods-spec"><?php echo $goods_info['goods_spec'];?></dd>
          <dd class="goods-type"><?php echo $goods_info['goods_type'] == 5 ? '赠品':''?></dd>
        </dl>
        </td>
      <td><em class="goods-price"><?php echo $goods_info['goods_price'];?></em></td>
      <td><?php echo $goods_info['goods_num'];?></td>
      <?php if ($_i == 0) { ?>
      <td rowspan="<?php echo $_cont;?>" class="bdl">
        <em class="order-amount"><?php echo ncPriceFormat($order_info['order_amount']);?></em>
        <p class="goods-freight">
          <?php if ($order_info['shipping_fee'] > 0){?>
          (含运费<?php echo $order_info['shipping_fee'];?>)
          <?php }else{?>
          免运费
          <?php }?>
        </p>
        <p class="goods-pay"><?php echo $order_info['payment_name']; ?></p>
        </td>
      <td rowspan="<?php echo $_cont;?>" class="bdl">
        <p class="m10 tl">收货人：<?php echo $order_info['extend_order_common']['reciver_name'];?>&nbsp; 
        <?php echo $order_info['extend_order_common']['reciver_info']['phone'];?>&nbsp; 
        <?php echo $order_info['extend_order_common']['reciver_info']['address'];?></p>
        <p class="m10 tl">发票：
            <?php foreach ((array)$order_info['extend_order_common']['invoice_info'] as $key => $value){?>
            <span><?php echo $key;?> (<strong><?php echo $value;?></strong>)</span>
            <?php } ?></p>
        <p class="m10 tl">买家留言：<?php echo $order_info['extend_order_common']['order_message']; ?></p>
        </td>
      <td rowspan="<?php echo $_cont;?>" class="bdl"><?php echo $order_info['state_desc']; ?>
                <?php if(!empty($order_info['shipping_code'])){ ?>
                <p><a href="javascript:void(0);" class="link" onclick="javascript:ajax_form('get_express', '查看物流', 'index.php?act=chain_sender&op=get_express&shipping_express_id=<?php echo $order_info['extend_order_common']['shipping_express_id'];?>&shipping_code=<?php echo $order_info['shipping_code'];?>')">查看物流</a></p>
                <?php }?>
        </td>
      <td rowspan="<?php echo $_cont;?>" class="nscs-table-handle bdl bdr">
        <?php if ($order_info['chain_sender_state'] == 10) { ?>
        <span><a href="javascript:void(0);" class="btn-bluejeans" onclick="javascript:ajax_get_confirm('请确认所有商品库存充足，接单并及时发货？','<?php echo CHAIN_SITE_URL;?>/index.php?act=chain_sender&op=sender_yes&order_id=<?php echo $order_info['order_id'];?>');">
        <p>确认接单</p>
        </a>
        <a href="javascript:void(0);" class="btn-bluejeans" onclick="javascript:ajax_get_confirm('请确认放弃接单，退回订单给商家处理？','<?php echo CHAIN_SITE_URL;?>/index.php?act=chain_sender&op=sender_no&order_id=<?php echo $order_info['order_id'];?>');">
        <p>放弃接单</p>
        </a></span>
        <?php } else if ($order_info['order_state'] != ORDER_STATE_SUCCESS) { ?>
        <span><a href="javascript:void(0);" class="btn-bluejeans" onclick="javascript:ajax_form('chain_sender', '发货', 'index.php?act=chain_sender&op=send&order_id=<?php echo $order_info['order_id'];?>')">
        <p>发货</p>
        </a></span>
        <?php } ?></td>
        <?php } ?>
    </tr>
    <tr style="display:none;">
      <td colspan="20"><div class="ncsc-goods-sku ps-container"></div></td>
    </tr>
    <?php $_i ++;?>
    <?php } ?>
    <?php } ?>
    <?php } else { ?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
    <?php } ?>
  </tbody>
  <tfoot>
    <?php  if (!empty($output['order_list'])) { ?>
    <tr>
      <td colspan="20"><div class="pagination"> <?php echo $output['show_page']; ?> </div></td>
    </tr>
    <?php } ?>
  </tfoot>
</table>
