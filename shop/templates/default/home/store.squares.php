<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.store-info-o .chat_online, .store-info-o .chat_offline{vertical-align: initial;}
.no-results { color: #AAA; padding: 200px 0; text-align: center;}
</style>
<?php if(!empty($output['store_list'])){?>
<ul>
  <?php foreach($output['store_list'] as $val){?>
  <li class="store-list">
    <div class="store-left">
      <div class="store-info">
        <div class="store-img"><a href="<?php echo urlShop('show_store','index',array('store_id' => $val['store_id']),$val['store_domain']);?>" title="<?php echo $val['store_name']?>" target="_blank"><img src="<?php echo getStoreLogo($val['store_avatar']);?>" alt="<?php echo $val['store_name']?>"></a></div>
        <div class="store-info-o">
          <p> <a class="store-name m-r-5" href="<?php echo urlShop('show_store','index',array('store_id' => $val['store_id']),$val['store_domain']);?>" title="<?php echo $val['store_name']?>" target="_blank">
            <?php if($val['is_own_shop'] == 1){?><span class="goods_self m-r-5">自营</span><?php }?><?php echo $val['store_name']?></a> 
            <span c_name="<?php echo $val['member_name'];?>" member_id="<?php echo $val['default_im'];?>"></span> 
          </p>
          <p>所在地：<span><?php echo $val['area_info'];?></span></p>
          <p>主营商品：<span class="store-major" title="<?php echo $val['store_zy']?>"><?php echo $val['store_zy']?></span></p>
        </div>
      </div>
      <div class="store-sever">
        <div class="store-volume"> 
          <span>销量<em>&nbsp;<?php echo $val['store_sales']?></em></span> 
          <span>共有<em>&nbsp;<?php echo $val['online_goods_count'];?>&nbsp;</em>件商品</span> 
        </div>
        <div class="store-privilege"> <em class="pf"></em>
          <div class="popup-storeinfo">
            <div class="popup-storeinfo-arrow"></div>
            <div class="popup-wrap">
              <div class="ncs-detail-rate">
                <dl>
                  <dt>店铺评分 </dt>
                  <dd>描述相符：<?php echo $val['store_credit']['store_desccredit']['credit'];?>分</dd>
                  <dd>服务态度：<?php echo $val['store_credit']['store_servicecredit']['credit'];?>分</dd>
                  <dd>物流服务：<?php echo $val['store_credit']['store_deliverycredit']['credit'];?>分</dd>
                </dl>
                <dl>
                  <dt>同类对比</dt>
                  <?php if($val['sc_id'] > 0){?>
                  <dd>
                    <div class="<?php echo $val['store_credit']['store_desccredit']['percent_class'];?>"><span><i></i><?php echo $val['store_credit']['store_desccredit']['percent_text'];?></span><?php echo $val['store_credit']['store_desccredit']['percent'];?></div>
                  </dd>
                  <dd>
                    <div class="<?php echo $val['store_credit']['store_servicecredit']['percent_class'];?>"><span><i></i><?php echo $val['store_credit']['store_servicecredit']['percent_text'];?></span><?php echo $val['store_credit']['store_servicecredit']['percent'];?></div>
                  </dd>
                  <dd>
                    <div class="<?php echo $val['store_credit']['store_deliverycredit']['percent_class'];?>"><span><i></i><?php echo $val['store_credit']['store_deliverycredit']['percent_text'];?></span><?php echo $val['store_credit']['store_deliverycredit']['percent'];?></div>
                  </dd>
                  <?php }else{?>
                  <dd>
                    <div class="equal"><span><i></i>持平</span>--</div>
                  </dd>
                  <dd>
                    <div class="equal"><span><i></i>持平</span>--</div>
                  </dd>
                  <dd>
                    <div class="equal"><span><i></i>持平</span>--</div>
                  </dd>
                  <?php }?>
                </dl>
              </div>
            </div>
          </div>
        </div>
      </div>
      <div class="fav-store"> <a href="javascript:collect_store('<?php echo $val['store_id'];?>','count','store_collect')" nc_data="<?php echo $val['store_id'];?>"> <i class="icon fa fa-star-o"></i>收藏店铺 </a> </div>
    </div>
    <div class="store-right">
      <div class="warp">
        <div class="store-goods-container">
          <?php if(is_array($val['commend_goods_list']) && !empty($val['commend_goods_list'])){?>
          <ul>
            <?php foreach ($val['commend_goods_list'] as $goods) {?>
            <li class="store-goods"> <a class="goods" href="<?php echo urlShop('goods','index',array('goods_id' => $goods['goods_id']))?>" title="<?php echo $goods['goods_name']?>" target="_blank"><img src="<?php echo thumb($goods, 240); ?>" alt="<?php echo $goods['goods_name']?>"></a>
              <div class="goods-info">
                <p class="goods-name m-t-5"><a href="<?php echo urlShop('goods','index',array('goods_id' => $goods['goods_id']))?>" title="<?php echo $goods['goods_name']?>" target="_blank"><?php echo $goods['goods_name'];?></a></p>
                <p class="goods-price m-t-5"> <em><?php echo ncPriceFormatForList($goods['goods_promotion_price']);?></em> <span>售出<em><?php echo $goods['goods_salenum'];?></em>件</span> </p>
              </div>
            </li>
            <?php }?>
          </ul>
          <?php }?>
        </div>
      </div>
    </div>
  </li>
  <?php }?>
</ul>
<script type="text/javascript">
    $(function(){
      $(".store-privilege").hover(function(){
            $(this).find(".popup-storeinfo").show();
        },function(){
            $(this).find(".popup-storeinfo").hide();
        });
      $(".av-store a").click(function(){
        var store_id = $(this).attr('nc_data');
      });
    });
</script>
<?php }else{ ?>
  <div class="no-results"><i></i>没有找到符合条件的店铺</div>
<?php } ?>