<link href="<?php echo SHOP_TEMPLATES_URL;?>/css/chain.css" rel="stylesheet" type="text/css">
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/jquery.reveal.js"></script>
<script type="text/javascript" src="http://api.map.baidu.com/api?v=1.4"></script>
<div class="head-img-wrap"> <img src="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>"> </div>
<div class="chain-detail"> </div>
<div class="basic-box">
  <div class="basic-info">
    <div class="shop-img"><img alt="<?php echo $output['chain_info']['chain_name']?>" src="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>"></div>
    <div class="shop-info">
      <h1 class="shop-name"><?php echo $output['chain_info']['chain_name']?></h1>
      <dl class="del-info">
        <dt><i class="icon-map-marker"></i>地&nbsp;&nbsp;&nbsp;&nbsp;址：</dt>
        <dd><?php echo $output['chain_info']['area_info'].' '.$output['chain_info']['chain_address'];?></dd>
      </dl>
      <dl class="del-info">
        <dt><i class="icon-phone"></i>联系电话：</dt>
        <dd> <?php echo $output['chain_info']['chain_phone']?></dd>
      </dl>
    </div>
    <div class="shop-info" style="margin-top:46px;">
      <dl class="del-info">
        <dt><i class="icon-time"></i>营业时间：</dt>
        <dd><?php echo $output['chain_info']['chain_opening_hours']?></dd>
      </dl>
      <?php if(!empty($output['voucher_list'])){?>
      <dl class="del-info po" >
        <dt><i class="icon-money"></i>领&nbsp;&nbsp;&nbsp;&nbsp;券：</dt>
        <dd><a class="nc-coupon-btn" href="javascript:;">优惠券</a></dd>
        <div id="promotion-ctips_636875" class="promotion-tips promotion-ctips">
          <div class="promotion-tit nc-coupon-downbtn">优惠券<b></b></div>
          <div class="promotion-cont">
          <?php foreach ($output['voucher_list'] as $val) {?>
            <div class="p-coupon-item p-coupon-item-gray" data-id="<?php echo $val['voucher_t_id']?>">
              <div class="coupon-price"><i class="i1"></i><span class="txt">¥<?php echo $val['voucher_t_price']?></span><i class="i2"></i></div>
              <div class="coupon-msg">
                <div><span class="ctype">优惠券</span><span class="cinfo"> 满¥<?php echo $val['voucher_t_limit']?>减¥<?php echo $val['voucher_t_price']?></span></div>
                <div class="ftx-03">有效期至<?php echo date('Y-m-d',$val['voucher_t_end_date']);?></div>
                <i class="zyc-ico"></i>
              </div>
              <div class="coupon-opbtns"><a class="btn-1 coupon-btn get_voucher_btn" href="javascript:;">领取</a></div>
            </div>
          <?php }?>
          </div>
        </div>
      </dl>
      <?php }?>
    </div>
    <div class="map">
      <div class="map-box"> 
        <img width="240" height="90" src="http://api.map.baidu.com/staticimage?center=&width=240&height=90&zoom=18&markers=<?php echo str_replace(' ', '', $output['chain_info']['area_info'].$output['chain_info']['chain_address']);?>">
        <a class="map-zoom big-link" class="" data-reveal-id="myModal" data-animation="fade"><i class="icon"></i></a>
      </div>
    </div>
    <div id="myModal" class="reveal-modal">
      <h3 class="title"><?php echo $output['chain_info']['chain_name']?></h3>
      <p class="info">		
        <span class="name">地址：</span>		
        <span><?php echo $output['chain_info']['area_info'].' '.$output['chain_info']['chain_address'];?></span>
      </p>
      <p class="info">		
        <span class="name">电话：</span>		
        <span><?php echo $output['chain_info']['chain_phone']?></span>
      </p>
      <div class="bigmap" id="baidu_map" style="width: 740px;height: 450px;"></div>
      <p class="action"><span class="desc">注：地图位置标注仅供参考，具体情况以实际道路标实信息为准</span></p>
      <a class="close-reveal-modal">&#215;</a>
    </div>
  </div>
</div>
<?php if(is_array($output['goods_list']) && !empty($output['goods_list'])){?>
<div class="list_item_box">
  <h2 class="title"> <img class="fl" src="<?php echo SHOP_TEMPLATES_URL;?>/images/list_icon_01.png" width="19" height="19"> <span class="fl">商品列表</span> </h2>
  <ul class="list_item">
  <?php foreach($output['goods_list'] as $key=>$value){?>
    <li data-id="<?php echo $value['goods_id']?>" goods-stock="<?php echo $value['stock']?>" store-id="<?php echo $value['store_id']?>">
      <a href="index.php?act=goods&goods_id=<?php echo $value['goods_id']?>" target="_blank">
        <img src="<?php echo cthumb($value['goods_image'], 360, $value['store_id']);?>" alt="<?php echo $value['goods_name']?>" class="item_list_img">
        <div class="item_list_goodsname"> <span><?php echo $value['goods_name']?></span>
          <div> 
            <span class="goods-price fl"> ¥<?php echo ncPriceFormat($value['chain_price'])?></span>
            <span class="goods-sales fr">销量：<i><?php echo $value['goods_salenum']?></i></span>
          </div>
        </div>
      </a>
      <div class="shopping-act"> <a class="shopping-act-btn" nctype="buynow_submit" href="javascript:void(0);"><i class="shopping-cart"></i>立即购买</a> </div>
    </li>
  <?php }?>
  </ul>
</div>
<div class="tc mt20 mb20">
  <div class="pagination">
    <?php echo $output['show_page']; ?>
  </div>
</div>
<?php }?>
<form id="buynow_form" method="post" action="<?php echo SHOP_SITE_URL;?>/index.php">
  <input id="act" name="act" type="hidden" value="buy" />
  <input id="op" name="op" type="hidden" value="buy_step1" />
  <input id="cart_id" name="cart_id[]" type="hidden"/>
</form>
<script type="text/javascript">
$(document).ready(function(){
  $(".nc-coupon-btn").click(function(){
    $(".promotion-tips").slideDown("fast");
  });
  $(".nc-coupon-downbtn").click(function(){
    $(".promotion-tips").slideUp("fast");
  });
  baidu_init();
});
$('a.get_voucher_btn').click(function(){
  <?php if ($_SESSION['is_login'] !== '1'){?>
    login_dialog();
  <?php }else{?>
    var t_id = $(this).parents('div.p-coupon-item').attr('data-id');
    $.ajax({
      type: 'post',
      url:"index.php?act=show_chain&op=get_voucher",
      data: {tid:t_id},
      dataType: 'json',
      success: function(result) {
        if(result.state){
          var p_html = '兑换成功';
          alert(p_html);
        }else{
          alert(result.msg);
        }
      }
    });
  <?php }?>
});
$('a[nctype="buynow_submit"]').click(function(){  
  if(quantity < 1){
    alert('库存不足');return;
  }
  var obj = $(this).parents('li');
  var goods_id = parseInt(obj.attr('data-id'));
  var quantity = parseInt(obj.attr('goods-stock'));
  var store_id = parseInt(obj.attr('store-id'));
  
  buynow(goods_id,1,store_id);
});
// 立即购买js
function buynow(goods_id,quantity,store_id){
<?php if ($_SESSION['is_login'] !== '1'){?>
  login_dialog();
<?php }else{?>
  if (!quantity) {
      return;
  }
  var member_store_id = <?php echo $_SESSION['store_id']>0?$_SESSION['store_id']:0;?>;
  if (member_store_id == store_id) {
    alert('不能购买自己店铺的商品');return;
  }
  $("#cart_id").val(goods_id+'|'+quantity);
  var chain_id = '<?php echo $output['chain_info']['chain_id']; ?>';
  var area_id = '<?php echo $output['chain_info']['area_id']; ?>';
  var area_name = '<?php echo $output['chain_info']['chain_name'].'('.$output['chain_info']['area_info'].$output['chain_info']['chain_address'].')'; ?>';
  var area_id_2 = '<?php echo $output['chain_info']['area_id_2']; ?>';
  
  $('#buynow_form').append('<input type="hidden" name="ifchain" value="1"><input type="hidden" name="chain_id" value="'+chain_id+'"><input type="hidden" name="area_id" value="'+area_id+'"><input type="hidden" name="area_name" value="'+area_name+'"><input type="hidden" name="area_id_2" value="'+area_id_2+'">');
  $("#buynow_form").submit();
<?php }?>
}

function local_city(cityResult){
      var center_point = cityResult.center;
      baidu_map.centerAndZoom(center_point, 16);
      var myGeo = new BMap.Geocoder();
      myGeo.getPoint("<?php echo $output['chain_info']['area_info'].' '.$output['chain_info']['chain_address'];?>", function(point){
        if (point) {
          baidu_map.centerAndZoom(point, 16);
          cur_point = point;
        }else{
          cur_point = center_point;
        }
        select_district(cur_point);
      });      
  }
  function select_district(obj) {
        baidu_map.clearOverlays();
        var point = new BMap.Point(obj.lng, obj.lat);
        var marker = new BMap.Marker(point);
        marker.setTitle("<?php echo $output['chain_info']['chain_name']?>");
        baidu_map.addOverlay(marker);
        marker_info(marker,obj);
        baidu_map.setViewport([obj]);
  }
  function marker_info(marker,obj){//开启信息窗口
      marker.addEventListener("click", function(){
          var point = new BMap.Point(obj.lng, obj.lat);
            var opts = {
                'title': "<p style='font-size:12px;'><?php echo $output['chain_info']['chain_name']?></p>"
            }
          var infoWindow = new BMap.InfoWindow("<p style='font-size:12px;'>地址：<?php echo $output['chain_info']['area_info'].' '.$output['chain_info']['chain_address'];?></p>",opts);
          baidu_map.openInfoWindow(infoWindow,point);
      });
  }
  function baidu_init() {//初始化地图
    baidu_map = new BMap.Map("baidu_map", {enableMapClick:false});
    var city = new BMap.LocalCity();
    var top_left_navigation = new BMap.NavigationControl();
    var overView = new BMap.OverviewMapControl();
    baidu_map.enableScrollWheelZoom(true);
    baidu_map.enableDoubleClickZoom(true);
    city.get(local_city);
  }
</script>