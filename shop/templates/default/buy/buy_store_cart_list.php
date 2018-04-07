<?php defined('InShopNC') or exit('Access Invalid!');?>


    <?php foreach($output['store_cart_list'] as $store_id => $cart_list) {?>
    <tbody store_id="<?php echo $store_id;?>">
      <tr>
        <th colspan="20"> <!-- S 店铺名称 -->
          
          <div class="ncc-store-name">店铺：<a href="<?php echo urlShop('show_store','index',array('store_id'=>$store_id));?>"><?php echo $cart_list[0]['store_name']; ?></a></div>
          
          <!-- E 店铺名称 --> 
          <!-- S 店铺满即送 -->
          
          <?php if (!empty($output['store_mansong_rule_list'][$store_id])) {?>
          <div class="ncc-store-sale ms"> <span>满即送</span><?php echo $output['store_mansong_rule_list'][$store_id]['desc'];?> </div>
          <?php } ?>
          
          <!-- E 店铺满即送 --> 
          <!-- S 店铺满金额包邮 -->
          
          <?php if (!empty($output['cancel_calc_sid_list'][$store_id])) {?>
          <div class="ncc-store-sale"> <span>免运费</span><?php echo $output['cancel_calc_sid_list'][$store_id]['desc'];?></div>
          <?php } ?>
          
          <!-- S 店铺满金额包邮 --> </th>
      </tr>
      <?php foreach($cart_list as $cart_info) {?>
      <tr id="cart_item_<?php echo $cart_info['cart_id'];?>" class="shop-list <?php echo ($cart_info['state'] && $cart_info['storage_state']) ? '' : 'item_disabled';?>"
<?php if ($cart_info['jjgRank'] > 0) { ?>
        data-jjg="<?php echo $cart_info['jjgRank']; ?>"
<?php } ?>
>
        <td class="td-border-left 
		<?php if ($cart_info['bl_id'] != '0') {?>
        td-bl
		<?php }?>
		<?php if ($cart_info['jjgRank'] > 0) { ?>
        td-bl
		<?php }?>"><?php if ($cart_info['state'] && $cart_info['storage_state']) {?>
          <input type="hidden" value="<?php echo $cart_info['cart_id'].'|'.$cart_info['goods_num'];?>" store_id="<?php echo $store_id?>" name="cart_id[]">
          <input type="hidden" value="<?php echo $cart_info['goods_id'].'|'.$cart_info['goods_num'];?>" store_id="<?php echo $store_id?>" name="goods_id[]">
          <?php } ?></td>
        <?php if ($cart_info['bl_id'] == '0') {?>
        <td class="w100"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$cart_info['goods_id']));?>" target="_blank" class="ncc-goods-thumb"><img src="<?php echo thumb($cart_info);?>" alt="<?php echo $cart_info['goods_name']; ?>" /></a></td>
        <?php } ?>
        <td class="tl" <?php if ($cart_info['bl_id'] != '0') {?>colspan="2"<?php }?>><dl class="ncc-goods-info">
            <dt>
              <?php if ($cart_info['bl_id'] != '0'){?>
              【套装】
              <?php }?>
              <a href="<?php echo urlShop('goods','index',array('goods_id'=>$cart_info['goods_id']));?>" target="_blank"><?php echo $cart_info['goods_name']; ?></a></dt>
            <?php if (!$cart_info['bl_id']) { ?>
            <dd class="goods-spec"><?php echo $cart_info['goods_spec'];?></dd>
            <?php } ?>
            <?php if ($cart_info['bl_id'] != '0') {?>
            <dd> <span class="buldling">优惠套装，单套直降<em>￥<?php echo $cart_info['down_price']; ?></em></span></dd>
            <?php }?>
            
            <!-- S 消费者保障服务 -->
            <?php if($cart_info["contractlist"]){?>
            <dd class="goods-cti">
              <?php foreach($cart_info["contractlist"] as $gcitem_k=>$gcitem_v){?>
              <span <?php if($gcitem_v['cti_descurl']){ ?>onclick="window.open('<?php echo $gcitem_v['cti_descurl'];?>');" style="cursor: pointer;"<?php }?> title="<?php echo $gcitem_v['cti_name']; ?>"> <img src="<?php echo $gcitem_v['cti_icon_url_60'];?>"/> </span>
              <?php }?>
            </dd>
            <?php }?>
            <!-- E 消费者保障服务 --> <!-- S 商品赠品列表 -->
            <?php if (!empty($cart_info['gift_list'])) { ?>
            <dd class="ncc-goods-gift"><span>赠品</span>
              <ul class="ncc-goods-gift-list">
                <?php foreach ($cart_info['gift_list'] as $goods_info) { ?>
                <li nc_group="<?php echo $cart_info['cart_id'];?>"><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['gift_goodsid']));?>" target="_blank" class="thumb" title="赠品：<?php echo $goods_info['gift_goodsname']; ?> * <?php echo $goods_info['gift_amount'] * $cart_info['goods_num']; ?>"><img src="<?php echo cthumb($goods_info['gift_goodsimage'],60,$store_id);?>" alt="<?php echo $goods_info['gift_goodsname']; ?>"/></a> </li>
                <?php } ?>
              </ul>
            </dd>
            <?php  } ?>
            <!-- E 商品赠品列表 -->
          </dl></td>
        <td><!-- S 商品单价 -->
          
          <?php if (!empty($cart_info['xianshi_info'])) {?>
          <em class="goods-old-price tip" title="商品原价格"><?php echo $cart_info['goods_yprice']; ?></em>
          <?php } ?>
          <em class="goods-price"><?php echo $cart_info['goods_price']; ?></em><!-- E 商品单价 --> 
          <!-- S 商品促销-限时折扣 -->
          
          <?php if ($cart_info['promotion_info']['promotion_type']==2) {?>
          <dl class="ncc-goods-sale">
            <dt>商家促销<i class="icon-angle-down"></i></dt>
            <dd>
              <p>活动名称：限时折扣</p>
              <p>满<strong><?php echo $cart_info['xianshi_info']['lower_limit'];?></strong>件，单价直降<em>￥<?php echo $cart_info['xianshi_info']['down_price']; ?></em></p>
            </dd>
          </dl>
          <?php }?>
          
          <!-- E 商品促销-限时折扣 --> 
            <?php if ($cart_info['promotion_info']['promotion_type']==3) {?>
            <dl class="ncc-goods-sale">
              <dt>商家促销<i class="icon-angle-down"></i></dt>
              <dd>
                <p>活动名称：秒杀</p>
                <p>最多限购：<strong><?php echo $cart_info['promotion_info']['upper_limit']; ?></strong>件，单价直降<em>￥<?php echo $cart_info['promotion_info']['down_price']; ?></em></p>
              </dd>
            </dl>
            <?php }?>
            <?php if ($cart_info['promotion_info']['promotion_type']==4) {?>
            <dl class="ncc-goods-sale">
              <dt>商家促销<i class="icon-angle-down"></i></dt>
              <dd>
                <p>活动名称：闪购</p>
                <p>最多限购：<strong><?php echo $cart_info['promotion_info']['upper_limit']; ?></strong>件，单价直降<em>￥<?php echo $cart_info['promotion_info']['down_price']; ?></em></p>
              </dd>
            </dl>
            <?php }?>
          <!-- S 商品促销-团购 -->
          
          <?php if ($cart_info['ifgroupbuy']) {?>
          <dl class="ncc-goods-sale">
            <dt>商家促销<i class="icon-angle-down"></i></dt>
            <dd>
              <p>活动名称：团购</p>
              <?php if ($cart_info['upper_limit']) {?>
              <p>最多限购：<strong><?php echo $cart_info['upper_limit']; ?></strong>件 </p>
              <?php } ?>
            </dd>
          </dl>
          <?php }?>
          
          <!-- E 商品促销-团购 --> </td>
        <td><?php echo $cart_info['state'] ? $cart_info['goods_num'] : ''; ?></td>
        <td class="td-border-right"><?php if ($cart_info['state'] && $cart_info['storage_state']) {?>
          <em cart_id="<?php echo $cart_info['cart_id']; ?>" goods_id="<?php echo $cart_info['goods_id'];?>" nc_type="eachGoodsTotal<?php echo $store_id?>" tpl_id="<?php echo $cart_info['transport_id']?>" class="goods-subtotal"><?php echo $cart_info['goods_total']; ?></em> <span id="no_send_tpl_<?php echo $cart_info['transport_id']?>" style="color: #F00;display:none">无货</span>
          <?php } elseif (!$cart_info['storage_state']) {?>
          <span style="color: #F00;">库存不足</span>
          <?php } elseif (!$cart_info['state']) {?>
          <span style="color: #F00;">无效</span>
          <?php }?></td>
      </tr>
      
      <!-- S bundling goods list -->
      <?php if (is_array($cart_info['bl_goods_list'])) {?>
      <?php foreach ($cart_info['bl_goods_list'] as $goods_info) { ?>
      <tr class="shop-list <?php echo $cart_info['state'] && $cart_info['storage_state'] ? '' : 'item_disabled';?>  bundling-list">
        <td class="tree td-border-left"></td>
        <td><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank" class="ncc-goods-thumb"><img src="<?php echo cthumb($goods_info['goods_image'],$store_id);?>" alt="<?php echo $goods_info['goods_name']; ?>" /></a></td>
        <td class="tl"><dl class="ncc-goods-info">
            <dt><a href="<?php echo urlShop('goods','index',array('goods_id'=>$goods_info['goods_id']));?>" target="_blank"><?php echo $goods_info['goods_name']; ?></a> </dt>
            <?php if ($goods_info['goods_spec']) { ?>
            <dd class="goods-spec"><?php echo $goods_info['goods_spec'];?></dd>
            <?php } ?>
            <!-- S 消费者保障服务 -->
            <?php if($goods_info["contractlist"]){?>
            <dd class="goods-cti">
              <?php foreach($goods_info["contractlist"] as $gcitem_k=>$gcitem_v){?>
              <span <?php if($gcitem_v['cti_descurl']){ ?>onclick="window.open('<?php echo $gcitem_v['cti_descurl'];?>');" style="cursor: pointer;"<?php }?> title="<?php echo $gcitem_v['cti_name']; ?>"> <img src="<?php echo $gcitem_v['cti_icon_url_60'];?>"/> </span>
              <?php }?>
            </dd>
            <?php }?>
            <!-- E 消费者保障服务 -->
          </dl></td>
        <td><em class="goods-price"><?php echo $goods_info['bl_goods_price'];?></em></td>
        <td><?php echo $cart_info['goods_num'];?></td>
        <td class="td-border-right"><em goods_id="<?php echo $goods_info['goods_id'];?>" cart_id="<?php echo $cart_info['cart_id'];?>" nc_type="eachGoodsTotal<?php echo $store_id?>" class="goods-subtotal"><?php echo ncPriceFormat($goods_info['bl_goods_price']*$cart_info['goods_num']);?></em> <span style="color: #F00;display:none">无货</span></td>
      </tr>
      <?php } ?>
      <?php  } ?>
      <!-- E bundling goods list -->
      
      <?php } ?>
      <tr>
        <td colspan="20"><div class="ncc-msg">买家留言：
            <textarea  name="pay_message[<?php echo $store_id;?>]" class="ncc-msg-textarea" placeholder="选填：对本次交易的说明（建议填写已经和商家达成一致的说明）" title="选填：对本次交易的说明（建议填写已经和商家达成一致的说明）"  maxlength="150"></textarea>
          </div>
          <div class="ncc-store-account">
            <dl>
              <dt>商品金额：</dt>
              <dd class="rule"></dd>
              <dd class="sum"><em id="eachStoreGoodsTotal_<?php echo $store_id;?>"><?php echo $output['store_goods_total'][$store_id];?></em></dd>
            </dl>
            <?php if ($output['store_mansong_rule_list'][$store_id]['discount'] > 0) {?>
            <dl store_mansong_price="<?php echo $output['store_mansong_rule_list'][$store_id]['price'];?>">
              <dt>店铺优惠：</dt>
              <dd class="rule"><?php echo $output['store_mansong_rule_list'][$store_id]['desc'];?></dd>
              <dd class="sum"><em id="eachStoreManSong_<?php echo $store_id;?>" class="subtract">-<?php echo $output['store_mansong_rule_list'][$store_id]['discount'];?></em></dd>
            </dl>
            <?php } ?>
            
            <!-- S voucher list -->
            
            <?php if (!empty($output['store_voucher_list'][$store_id]) && is_array($output['store_voucher_list'][$store_id])) {?>
            <dl>
              <dt>优惠卡券：</dt>
              <dd class="rule">
                <select nctype="voucher" name="voucher[<?php echo $store_id;?>]" class="select">
                  <option value="<?php echo $voucher['voucher_t_id'];?>|<?php echo $store_id;?>|0.00">-选择使用<?php echo empty($_POST['ifchain']) ? '店铺' : '门店';?>代金券-</option>
                  <?php foreach ($output['store_voucher_list'][$store_id] as $voucher) {?>
                  <option value="<?php echo $voucher['voucher_t_id'];?>|<?php echo $store_id;?>|<?php echo $voucher['voucher_price'];?>"><?php echo $voucher['desc'];?></option>
                  <?php } ?>
                </select>
              </dd>
              <dd class="sum"><em id="eachStoreVoucher_<?php echo $store_id;?>" class="subtract">-0.00</em></dd>
            </dl>
            <!-- E voucher list -->
            <?php } ?>
            <dl>
              <dt>物流运费：</dt>
              <dd class="rule">
                <?php if (!empty($output['cancel_calc_sid_list'][$store_id])) {?>
                <?php echo $output['cancel_calc_sid_list'][$store_id]['desc'];?>
                <?php } ?>
              </dd>
              <dd class="sum"><em nc_type="eachStoreFreight" id="eachStoreFreight_<?php echo $store_id;?>">0.00</em></dd>
            </dl>
            <dl class="total">
              <dt>本店合计：</dt>
              <dd class="rule"></dd>
              <dd class="sum"><em store_id="<?php echo $store_id;?>" nc_type="eachStoreTotal"></em><?php echo $lang['currency_zh'];?></dd>
            </dl>
          </div></td>
      </tr>
    </tbody>
      <?php } ?>