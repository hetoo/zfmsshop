<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.ncap-form-default dd.selected { border: solid 1px #2CBCA3; box-shadow: 0 0 0 2px rgba(82, 168, 236, 0.15); }



.scrollbar-box { padding:5px; max-height:300px;}



.ncap-form-default .row a.btn{
    background: #fff;
    border: 1px solid #f5f5f5;
    border-radius: 4px;
    color: #999;
    cursor: pointer !important;
    display: inline-block;
    font-size: 12px;
    font-weight: normal;
    height: 20px;
    letter-spacing: normal;
    line-height: 20px;
    margin: 0 0 0 16%;
    padding: 1px 6px;
    vertical-align: top;
}
.ncap-form-default .row a.btn:hover {box-shadow: 2px 2px 0 rgba(0, 0, 0, 0.1);color: #555;text-decoration: none;}
.ncap-form-default .row a.red:hover {background-color: #e84c3d;border-color: #c1392b;color: #fff;}
</style>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>秒杀推荐</h3>
        <h5>秒杀页面添加商品和活动设置</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>因显示空间所限，最多可添加6组数据块，保存后生效，<a class="ncap-btn" target="_blank" href="<?php echo SHOP_SITE_URL;?>/index.php?act=spike">查看</a>。</li>
    </ul>
  </div>
  <form method="post" name="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
        <?php if(!empty($output['list']) && is_array($output['list'])) { ?>
        <?php foreach($output['list'] as $k => $v) { ?>
            <?php if(!empty($v['goods_list']) && is_array($v['goods_list'])) { ?>
      <dl class="row" recommend_id="<?php echo $k;?>">
        <dt class="tit">推荐商品 <div class="btn"> <a class="btn red" h href="JavaScript:del_recommend(<?php echo $k;?>);"><i class="fa fa-trash-o"></i>删除</a></div></dt>
        <dd class="opt" onclick="select_recommend(<?php echo $k;?>,'goods');">
          <input type="hidden" name="spike_list[<?php echo $k;?>][]" value="goods">
          <ul class="dialog-goodslist-s1 goods-list scrollbar-box" >
            <?php foreach($v['goods_list'] as $k2 => $v2) { ?>
            <li id="select_recommend_<?php echo $k;?>_goods_<?php echo $v2['goods_id'];?>" style="margin:0 18px 0 0;">
                <div class="goods-pic" ondblclick="del_recommend_goods(<?php echo $v2['goods_id'];?>);">
                <span onclick="del_recommend_goods(<?php echo $v2['goods_id'];?>);" class="ac-ico"></span>
                <span class="thumb size-72x72"><i></i>
                    <img onload="javascript:DrawImage(this,72,72);" src="<?php echo thumb($v2, 240);?>" title="<?php echo $v2['goods_name'];?>" select_goods_id="<?php echo $v2['goods_id'];?>"></span>
                </div>
                <div class="goods-name"><a target="_blank" href="<?php echo SHOP_SITE_URL."/index.php?act=goods&goods_id=".$v2['goods_id'];?>"><?php echo $v2['goods_name'];?></a></div>
                <input type="hidden" value="<?php echo $v2['spike_goods_id'];?>" name="spike_list[<?php echo $k;?>][]"></li>
            <?php } ?>
          </ul>
        </dd>
      </dl>
            <?php } ?>
            <?php if(!empty($v['spike_list']) && is_array($v['spike_list'])) { ?>
      <dl class="row" recommend_id="<?php echo $k;?>">
        <dt class="tit">推荐活动 <div class="btn"> <a class="btn red" href="JavaScript:del_recommend(<?php echo $k;?>);"><i class="fa fa-trash-o"></i>删除</a></div></dt>
        <dd class="opt" style="text-align: left;" onclick="select_recommend(<?php echo $k;?>,'spike');">
          <input type="hidden" name="spike_list[<?php echo $k;?>][]" value="spike">
          <ul class="dialog-goodslist-s1 goods-list scrollbar-box">
            <?php foreach($v['spike_list'] as $k2 => $v2) { ?>
            <li style="display: inline-block; margin:0 15px 0 0; width:219px; height:200px;" id="select_recommend_<?php echo $k;?>_spike_<?php echo $v2['spike_id'];?>">
                <div class="goods-pic" style=" width:219px; height:150px;" ondblclick="del_recommend_spike(<?php echo $v2['spike_id'];?>);">
                <span onclick="del_recommend_spike(<?php echo $v2['spike_id'];?>);" class="ac-ico"></span>
                <span class="thumb size-72x72"><i></i>
                    <img  style=" width:219px; height:150px;" onload="javascript:DrawImage(this,72,72);" src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_STORE.'/'.$v2['spike_common_bg'];?>" title="<?php echo $v2['spike_name'];?>" select_spike_id="<?php echo $v2['spike_id'];?>"></span>
                </div>
                <div class="goods-name" style="width: 219px;"><a target="_blank" href="<?php echo SHOP_SITE_URL."/index.php?act=spike&op=brand&spike_id=".$v2['spike_id'];?>"><?php echo $v2['spike_name'];?></a></div>
                <input type="hidden" value="<?php echo $v2['spike_id'];?>" name="spike_list[<?php echo $k;?>][]"></li>
            <?php } ?>
          </ul>
        </dd>
      </dl>      
            <?php } ?>
        <?php } ?>
        <?php } ?>
      
      <dl class="row" id="append_goods">
        <dt class="tit"></dt>
        <dd class="opt">
            <div class="mt20"><a class="ncap-btn" href="JavaScript:add_recommend_goods();">商品调用</a>
              <a class="ncap-btn" href="JavaScript:add_spike_brand();">活动调用</a>
            </div>
        </dd>
      </dl>
      <dl class="row" show="goods" style="display:none;">
        <dt class="tit">
        </dt>
        <dd class="opt">
          <input type="text" placeholder="搜索活动或商品名称" value="" name="goods_name" id="goods_name" maxlength="20" class="input-txt">
          <a onclick="goods_search()" href="JavaScript:void(0);" class="ncap-btn mr5"><?php echo $lang['nc_search'];?></a></dd>
      </dl>
      <dl class="row" show="goods" style="display:none;">
        <dt class="tit">选择要推荐的商品</dt>
        <dd class="opt">
          <div id="show_recommend_goods_list" class="show-recommend-goods-list scrollbar-box"></div>
          <p class="notic">每组最多可推荐4个商品</p>
        </dd>
      </dl>
      <dl class="row" show="spike" style="display:none;">
        <dt class="tit">
        </dt>
        <dd class="opt">
          <input type="text" placeholder="搜索活动或店铺名称" value="" name="spike_name" id="spike_name" maxlength="20" class="input-txt">
          <a onclick="spike_search()" href="JavaScript:void(0);" class="ncap-btn mr5"><?php echo $lang['nc_search'];?></a></dd>
      </dl>
      <dl class="row" show="spike" style="display:none;">
        <dt class="tit">选择要推荐的活动</dt>
        <dd class="opt">
          <div id="show_recommend_spike_list" class="show-recommend-goods-list scrollbar-box"></div>
          <p class="notic">每组最多可推荐2个活动</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.settingForm.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.ajaxContent.pack.js"></script>
<script src="<?php echo ADMIN_RESOURCE_URL?>/js/spike_recommend.js"></script>
