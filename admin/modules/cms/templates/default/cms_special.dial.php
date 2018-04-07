<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.dialog-goodslist-s2 .goods-pic {
    height:74px;
    width: 260px;
    background-color:#eef9fd;
}
.dialog-goodslist-s2 .goods-name { 
    float:left;
    display: block;
    height: 25px;
    line-height: 25px;
    overflow: hidden;
    text-overflow: ellipsis;
    white-space: nowrap;
    width: 120px;
    margin-top:10px;
}
.dialog-goodslist-s2 li {
    width: 252px;
    height: auto;
}
.dialog-goodslist-s2 .goods-name02 { 
    float:left;
    display: block;
    width: 240px; 
    margin-left:5px;
}
.dialog-goodslist-s2 .goods-name02 span{ color:#047bb2; }
.dialog-goodslist-s2 .goods-name span{ color:#047bb2; font-weight:bolder;  margin-left: 5px;}
.dialog-goodslist-s2 .goods-name em{ color:#FCFCFC; font-weight:bolder; background-color: #F04060;  margin-left: 5px; padding: 1px 5px; border:1px solid #E05070; border-radius: 5px;}
</style>

<div id="show_recommend_goods_list">
<?php if(!empty($output['dial_list']) && is_array($output['dial_list'])){ ?>
<ul class="dialog-goodslist-s2">
  <?php foreach($output['dial_list'] as $k => $v){ ?>
  <li>
    <div onclick="select_dial(<?php echo $v['lot_id'];?>);" class="goods-pic">
      <span class="ac-ico"></span> 
        <?php $lot_weight = $v['lot_weight']/100;?>
        <input type="hidden" lot_id="<?php echo $v['lot_id'];?>" lot_weight="<?php echo $lot_weight;?>" lot_count="<?php echo $v['lot_count'];?>"lot_state="<?php echo $v['lot_state'];?>" start_time="<?php echo $v['start_time'];?>" end_time="<?php echo $v['end_time'];?>" title="<?php echo $v['lot_name'];?>" lot_name="<?php echo $v['lot_name'];?>" />
      <div class="goods-name"><em title="中奖率"><?php echo floatval($v['lot_weight']).'%';?></em><span><?php echo $v['lot_name'];?></span></div>
      <div class="goods-name02">活动时间：<?php echo date('Y-m-d',$v['start_time']).' - '.date('Y-m-d',$v['end_time'])?></div>
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
</div>
<script type="text/javascript">
  $('#show_recommend_goods .demo').ajaxContent({
    target:'#show_recommend_goods_list'
  });
  function select_dial(lot_id){
    var obj = $('input[lot_id="'+lot_id+'"]');
    var lot_name = obj.attr('lot_name');
    var check_obj = $("#special_content");
    var html = check_obj.val();
    if($("#div_content_view").find('div#special_content_lottery_view').size() > 0){
      alert('已存在抽奖活动，请删除后再添加');
      return false;
    }
    html += "<div class='special-content-lottery-view' id='special_content_lottery_view'>";
    html += "<input type='hidden' nctype='lot_id' value='"+lot_id+"'>";
    html += "<div class='lottery_info'>";
      html += "<div id='temp1' class='winner-name'>";
        html += "<div class='winner-title'>";
          html += "<div class='winner-title-bg'></div>";
          html += "<div class='winner-title-con'>获奖名单</div>";
        html += "</div>";
        html += "<div class='winner-inner-tit'>";
          html += "<span class='w-zm'>中奖者名单</span><span class='w-zm'>奖品信息</span>";
        html += "</div>";
      html += "</div>";
    html += "</div>";
    html += "<div class='lottery_view'>抽奖活动：【"+lot_name+"】</div>";
    html += "</div>";
    $("#special_content").val(html);
    $("#div_content_view").html($("#special_content").val());
    DialogManager.close('dialog_special_add_lottery');
  }
</script>