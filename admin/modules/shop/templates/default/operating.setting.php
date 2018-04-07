<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>运营设置</h3>
        <h5>各个运营模块的相关设置</h5>
      </div>
    </div>
  </div>
  <form method="post" name="settingForm" id="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit"><?php echo $lang['open_pointshop_isuse'];?></dt>
        <dd class="opt">
          <div class="onoff">
            <label for="pointshop_isuse_1" class="cb-enable <?php if($output['list_setting']['pointshop_isuse'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['nc_open'];?>"><span><?php echo $lang['nc_open'];?></span></label>
            <label for="pointshop_isuse_0" class="cb-disable <?php if($output['list_setting']['pointshop_isuse'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['nc_close'];?>"><span><?php echo $lang['nc_close'];?></span></label>
            <input id="pointshop_isuse_1" name="pointshop_isuse" <?php if($output['list_setting']['pointshop_isuse'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="pointshop_isuse_0" name="pointshop_isuse" <?php if($output['list_setting']['pointshop_isuse'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic"><?php echo sprintf($lang['open_pointshop_isuse_notice'],"index.php?act=setting&op=pointshop_setting");?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit"><?php echo $lang['open_pointprod_isuse'];?></dt>
        <dd class="opt">
          <div class="onoff">
            <label for="pointprod_isuse_1" class="cb-enable <?php if($output['list_setting']['pointprod_isuse'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="pointprod_isuse_0" class="cb-disable <?php if($output['list_setting']['pointprod_isuse'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="pointprod_isuse_1" name="pointprod_isuse" <?php if($output['list_setting']['pointprod_isuse'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="pointprod_isuse_0" name="pointprod_isuse" <?php if($output['list_setting']['pointprod_isuse'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic"><?php echo $lang['open_pointprod_isuse_notice'];?></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">平台红包</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="redpacket_allow_1" class="cb-enable <?php if($output['list_setting']['redpacket_allow'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="redpacket_allow_0" class="cb-disable <?php if($output['list_setting']['redpacket_allow'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="redpacket_allow_1" name="redpacket_allow" <?php if($output['list_setting']['redpacket_allow'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="redpacket_allow_0" name="redpacket_allow" <?php if($output['list_setting']['redpacket_allow'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic">平台红包开启后，可以在后台“平台红包”功能中发布红包，会员通过相应的方式领取红包，在下单时选择使用拥有的红包，从而得到优惠</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">消费者保障服务</dt>
        <dd class="opt">
          <div class="onoff">
            <label for="contract_allow_1" class="cb-enable <?php if($output['list_setting']['contract_allow'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><?php echo $lang['open'];?></label>
            <label for="contract_allow_0" class="cb-disable <?php if($output['list_setting']['contract_allow'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><?php echo $lang['close'];?></label>
            <input id="contract_allow_1" name="contract_allow" <?php if($output['list_setting']['contract_allow'] == '1'){ ?>checked="checked"<?php } ?> value="1" type="radio">
            <input id="contract_allow_0" name="contract_allow" <?php if($output['list_setting']['contract_allow'] == '0'){ ?>checked="checked"<?php } ?> value="0" type="radio">
          </div>
          <p class="notic">消费者保障服务开启后，店铺可以申请加入保障服务，为消费者提供商品筛选依据</p>
        </dd>
      </dl>
      <!-- 促销开启 -->
      <dl class="row">
        <dt class="tit">
          <label for="baidu_map_key">百度地图密钥</label>
        </dt>
        <dd class="opt">
          <input id="baidu_map_key" name="baidu_map_key" value="<?php echo $output['list_setting']['baidu_map_key'];?>" class="input-txt" type="text">
          <p class="notic">用于WAP手机端定位当前所在城市，<a class="ncap-btn" target="_blank" href="http://lbsyun.baidu.com/apiconsole/key?application=key">立即在线申请</a></p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" id="submitBtn"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script>
$(function(){$("#submitBtn").click(function(){
    if($("#settingForm").valid()){
      $("#settingForm").submit();
	}
	});
});
</script>
