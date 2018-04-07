<?php defined('InShopNC') or exit('Access Invalid!'); ?>
<?php if ($output['mr_rel_gc']) { ?>

<div class="ncs-sidebar-container ncs-class-bar">
  <div class="title">
    <h4>相关分类</h4>
  </div>
  <div class="content">
    <ul class="ncs-mall-category-list">
      <?php foreach ((array) $output['mr_rel_gc'] as $v) { ?>
      <li><a href="<?php echo urlShop('search', 'index', array('cate_id'=> $v['gc_id'])); ?>" title="<?php echo $v['gc_name']; ?>"><?php echo $v['gc_name']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
</div>
<?php } ?>
<?php if ($output['mr_rel_brand']) { ?>
<div class="ncs-sidebar-container ncs-class-bar">
  <div class="title">
    <h4>相关品牌</h4>
  </div>
  <div class="content">
    <ul class="ncs-mall-brand-list">
      <?php foreach ((array) $output['mr_rel_brand'] as $v) { ?>
      <li><a href="<?php echo urlShop('brand', 'list', array('brand'=> $v['brand_id'])); ?>" title="<?php echo $v['brand_name']; ?>"><?php echo $v['brand_name']; ?></a></li>
      <?php } ?>
    </ul>
  </div>
</div>
<?php } ?>
<?php if ($output['mr_hot_sales']) { ?>
<div class="ncs-sidebar-container ncs-top-bar">
  <div class="title">
    <h4><?php echo $output['mr_hot_sales_gc_name']; ?>热销排行榜</h4>
  </div>
  <div class="content">
    <div id="hot_sales_list" class="ncs-top-panel">
      <ol>
        <?php foreach ((array) $output['mr_hot_sales'] as $g) { ?>
        <li>
          <dl>
            <dt><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $g['goods_id'])); ?>"><?php echo $g['goods_name']; ?></a></dt>
            <dd class="goods-pic"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $g['goods_id'])); ?>"><span class="thumb size40"><i></i><img src="<?php echo thumb($g, 60); ?>"  onload="javascript:DrawImage(this,40,40);"></span></a>
              <p><span class="thumb size100"><i></i><img src="<?php echo thumb($g, 240); ?>" onload="javascript:DrawImage(this,100,100);" title="<?php echo $g['goods_name']; ?>"><big></big><small></small></span></p>
            </dd>
            <dd class="price pngFix"><?php echo ncPriceFormat($g['goods_promotion_price']); ?></dd>
          </dl>
        </li>
        <?php } ?>
      </ol>
    </div>
  </div>
</div>
<?php } ?>
<?php if ($output['mr_rec_products']) { ?>
<div class="ncs-sidebar-container ncs-top-bar">
  <div class="title">
    <h4>推荐商品</h4>
  </div>
  <div id="hot_sales_list" class="content">
    <ul class="ncs-mall-booth-list">
      <?php foreach ((array) $output['mr_rec_products'] as $g) { ?>
      <li>
        <div class="goods-pic"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $g['goods_id'])); ?>"><img src="<?php echo thumb($g, 240); ?>"></a></div>
        <div class="goods-name"><a href="<?php echo urlShop('goods', 'index', array('goods_id' => $g['goods_id'])); ?>"><?php echo $g['goods_name']; ?></a></div>
        <div class="goods-price"><?php echo ncPriceFormat($g['goods_promotion_price']); ?></div>
      </li>
      <?php } ?>
    </ul>
  </div>
</div>
<?php } ?>
