<style>
.dialog-goodslist-s2 .goods-pic {
    height:84px;
    width: 260px;
}

.size-72x72 { display:block; padding:5px;float:left; }
.size-72x72 img{border:1px dotted #cbe9f3;} 
.dialog-goodslist-s2 .goods-pic { background-color:#eef9fd;}
.dialog-goodslist-s2 .goods-name { float:left;
    display: block;
    height: 25px;
    line-height: 25px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 120px; margin-top:10px;
 
}
.dialog-goodslist-s2 li {
    width: 252px;
}
.dialog-goodslist-s2 .goods-name02 { float:left;
    display: block;

    width: 160px; margin-left:5px;
 
}
.dialog-goodslist-s2 .goods-name02 span{ color:#047bb2; }
.dialog-goodslist-s2 .goods-name span{ color:#047bb2; font-weight:bolder;  }
</style>


<?php defined('InShopNC') or exit('Access Invalid!');?>
<div id="show_recommend_goods_list">
<?php if(!empty($output['coupon_list']) && is_array($output['coupon_list'])){ ?>
<ul class="dialog-goodslist-s2">
  <?php foreach($output['coupon_list'] as $k => $v){ ?>
  <li>
    <div onclick="select_redpacket(<?php echo $v['rpacket_t_id'];?>);" class="goods-pic">
      <span class="ac-ico"></span> 
      <span class="thumb size-72x72"><i></i>
        <img coupon_id="<?php echo $v['rpacket_t_id'];?>" rpacket_price="<?php echo $v['rpacket_t_price'];?>" rpacket_limit="<?php echo $v['rpacket_t_limit'];?>" rpacket_start="<?php echo $v['rpacket_t_start_date'];?>" rpacket_giveout="<?php echo $v['rpacket_t_giveout'];?>" rpacket_total="<?php echo $v['rpacket_t_total'];?>" rpacket_end="<?php echo $v['rpacket_t_end_date'];?>" 
        title="<?php echo $v['rpacket_t_title'];?>" rpacket_name="<?php echo $v['rpacket_t_title'];?>" src="<?php echo $v['rpacket_t_customimg_url'];?>" onerror="this.src='<?php echo UPLOAD_SITE_URL.DS.defaultGoodsImage(240);?>'" onload="javascript:DrawImage(this,72,72);" />
      </span>
      <div class="goods-name"><span><?php echo $v['rpacket_t_title'];?></span></div>
      <div class="goods-name02">消费满￥<?php echo $v['rpacket_t_limit'];?>元减￥<?php echo $v['rpacket_t_price'];?>元</div>
    </div>
  </li>
  <?php } ?>
  <div class="clear"></div>
</ul>
<div id="show_recommend_goods" class="pagination"> <?php echo $output['show_page'];?> </div>
<?php }else { ?>
<p class="no-record"><?php echo $lang['nc_no_record'];?></p>
<?php } ?>
<div class="clear"></div>
<div>
<script type="text/javascript">
  $('#show_recommend_goods .demo').ajaxContent({
    target:'#show_recommend_goods_list'
  });
  function select_redpacket(coupon_id){
    var obj = $('img[coupon_id="'+coupon_id+'"]');
    var index = <?php echo $output['index']?>;
    var redpacket = $('li[index-data="'+index+'"]').find('span.redpacket');
    var rpacket_price = obj.attr('rpacket_price');
    var rpacket_name = obj.attr('rpacket_name')+' 消费满￥'+obj.attr('rpacket_limit')+'元，减￥'+rpacket_price+'元';
    
    var html = '<span style="border:dashed 1px #E0E0E0; padding: 5px; "><i class="fa fa-cc-discover"></i>'+rpacket_name+'</span>';
    html += '<input type="hidden" name="prize['+index+'][coupon_title]" value="'+rpacket_name+'"/>';
    html += '<input type="hidden" name="prize['+index+'][coupon_quota]" value="'+rpacket_price+'"/>';
    html += '<input type="hidden" name="prize['+index+'][coupon_id]" value="'+coupon_id+'"/>';
    $('li[index-data="'+index+'"]').find('input[name^="rpacket_"]').val(coupon_id).change();
    redpacket.html(html);
    DialogManager.close('cou_lists');
  }
</script>