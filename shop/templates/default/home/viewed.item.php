<?php defined('InShopNC') or exit('Access Invalid!');?>
<?php if (!empty($output['goods_list']) && is_array($output['goods_list'])) { ?>
<?php foreach($output['goods_list'] as $goods_info) { ?>
<div class="item">
  <div class="scope">
    <dl class="goods">
      <dt class="goods-thumb"> <a title="<?php echo $goods_info['goods_name'];?>" target="_blank" href="<?php echo urlShop('goods', 'index', array('goods_id' => $goods_info['goods_id']));?>">
        <img src="<?php echo cthumb($goods_info['goods_image'], 240, $goods_info['store_id']);?>" /></a> </dt>
      <dd class="goods-name"><a target="_blank" href="<?php echo urlShop('goods', 'index', array('goods_id' => $goods_info['goods_id']));?>"><?php echo $goods_info['goods_name'];?></a></dd>
    </dl>
    <div class="goods-price"><span class="sale">商城价：<em><?php echo ncPriceFormatForList($goods_info['goods_promotion_price']);?></em></span>
    </div>
  </div>
</div>
<?php } ?>
<?php } ?>