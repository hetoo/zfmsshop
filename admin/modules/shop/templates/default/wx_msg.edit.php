<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>微信公众号消息通知</h3>
        <h5>公众号向会员发送重要的服务通知</h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>此功能使用微信公众号接口，需在注册并认证“服务号”，并获得模板消息的使用权限。</li>
      <li>在公众平台模板库中按照“IT科技 互联网|电子商务”的行业来选择模板，<a class="ncap-btn" target="_blank" href="https://mp.weixin.qq.com/wiki">微信公众平台文档</a>。</li>
      <li>因微信对通知内容要求较严格，不支持营销、到期提醒类消息推送，详细规则<a class="ncap-btn" target="_blank" href="https://mp.weixin.qq.com/wiki/2/def71e3ecb5706c132229ae505815966.html">查看</a>。</li>
    </ul>
  </div>
  <form method="post" name="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">
          <label>是否启用微信通知功能</label>
        </dt>
        <dd class="opt">
          <div class="onoff">
            <label for="weixin_mp_isuse_1" class="cb-enable <?php if($output['list_setting']['weixin_mp_isuse'] == '1'){ ?>selected<?php } ?>" title="<?php echo $lang['open'];?>"><span><?php echo $lang['open'];?></span></label>
            <label for="weixin_mp_isuse_0" class="cb-disable <?php if($output['list_setting']['weixin_mp_isuse'] == '0'){ ?>selected<?php } ?>" title="<?php echo $lang['close'];?>"><span><?php echo $lang['close'];?></span></label>
            <input type="radio" id="weixin_mp_isuse_1" name="weixin_mp_isuse" value="1" <?php echo $output['list_setting']['weixin_mp_isuse']==1?'checked=checked':''; ?>>
            <input type="radio" id="weixin_mp_isuse_0" name="weixin_mp_isuse" value="0" <?php echo $output['list_setting']['weixin_mp_isuse']==0?'checked=checked':''; ?>>
          </div>
          <p class="notic">使用微信关注公众号，与商城会员帐号绑定后就可以在微信客户端收到相关通知。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="weixin_mp_appid">应用标识</label>
        </dt>
        <dd class="opt">
          <input id="weixin_mp_appid" name="weixin_mp_appid" value="<?php echo $output['list_setting']['weixin_mp_appid'];?>" class="input-txt" type="text">
          <p class="notic"><a class="ncap-btn" target="_blank" href="https://mp.weixin.qq.com/">立即在线申请</a></p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="weixin_mp_appsecret">应用密钥</label>
        </dt>
        <dd class="opt">
          <input id="weixin_mp_appsecret" name="weixin_mp_appsecret" value="<?php echo $output['list_setting']['weixin_mp_appsecret'];?>" class="input-txt" type="text">
          <p class="notic">&nbsp;</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label>服务器地址</label>
        </dt>
        <dd class="opt">
          <?php echo MOBILE_SITE_URL.'/index.php?act=weixin';?>
          <p class="notic">在微信公众平台中会要求填写，<a class="ncap-btn" target="_blank" href="https://mp.weixin.qq.com/wiki/17/2d4265491f12608cd170a95559800f2d.html">查看帮助</a>。</p>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="weixin_mp_token">Token</label>
        </dt>
        <dd class="opt">
          <input id="weixin_mp_token" name="weixin_mp_token" value="<?php echo $output['list_setting']['weixin_mp_token'];?>" class="input-txt" type="text">
          <p class="notic">Token可以任意填写，用作生成签名验证，需与微信公众平台的内容一致。</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.settingForm.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
