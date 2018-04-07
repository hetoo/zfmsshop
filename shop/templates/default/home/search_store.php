<?php defined('InShopNC') or exit('Access Invalid!');?>
<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/store_search.css" rel="stylesheet" type="text/css">
<link href="<?php echo SHOP_RESOURCE_SITE_URL;?>/font/font-awesome-4.7/css/font-awesome.min.css" rel="stylesheet" type="text/css">
<div class="nch-breadcrumb-layout" style="display: block;">
  <div class="nch-breadcrumb wrapper"> <i class="fa fa-home"></i> <span> <a href="<?php echo SHOP_SITE_URL;?>">首页</a> </span> <span class="arrow">&gt;</span> <span>搜索结果</span></div>
</div>
<div class="store-layout">
  <div class="store-sort-bar">
    <div class="store-sort-wrap ">
      <div class="store-sort-array">
        <ul class="sort-list">
          <li <?php if(!$_GET['key']){?>class="selected"<?php }?>>
            <a href="<?php echo dropParam(array('order', 'key'));?>" title="默认排序">默认排序</a>
          </li>
          <li <?php if($_GET['key'] == '1'){?>class="selected"<?php }?>>
            <a href="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '1') ? replaceParam(array('key' => '1', 'order' => '1')):replaceParam(array('key' => '1', 'order' => '2')); ?>" title="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '1')?'点击按成交量从低到高排序':'点击按成交量从高到低排序'; ?>">成交量</a>
          </li>
          <li <?php if($_GET['key'] == '2'){?>class="selected"<?php }?>>
            <a href="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '2') ? replaceParam(array('key' => '2', 'order' => '1')):replaceParam(array('key' => '2', 'order' => '2')); ?>" title="<?php echo ($_GET['order'] == '2' && $_GET['key'] == '2')?'收藏量成交量从低到高排序':'收藏量成交量从高到低排序'; ?>">收藏量</a>
          </li>
        </ul>
      </div>
      <div class="store-sort-filter">
        <div class="widget-label"> <span class="widget-label-txt" style="width:38px; text-align:right"><?php echo $_GET['area_id']>0?$output['name_areas'][$_GET['area_id']]:'所在地';?></span><i class="widget-label-arrow"></i> </div>
        <div class="widget-location">          
          <?php require_once (BASE_TPL_PATH.'/home/search_store.area.php');?>
        </div>
      </div>
    </div>
    <div class="search-store">
      <?php require_once (BASE_TPL_PATH.'/home/store.squares.php');?>
    </div>
    <div class="tc mt20 mb20">
      <div class="pagination"> <?php echo $output['show_page']; ?> </div>
    </div>
  </div>
</div>