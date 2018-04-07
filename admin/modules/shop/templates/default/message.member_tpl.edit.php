<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=message&op=member_tpl" title="返回用户消息模板列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3><?php echo $lang['nc_message_set'];?> - <?php echo $lang['nc_edit'];?>用户消息模板“<?php echo $output['mmtpl_info']['mmt_name'];?>”</h3>
        <h5><?php echo $lang['nc_message_set_subhead'];?></h5>
      </div>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>平台可以选择开启一种或多种消息通知方式。</li>
      <li>短消息、邮件需要用户绑定手机、邮箱后才能正常接收。</li>
      <li class="red">编辑完成后请清理“用户消息模板”缓存。</li>
      <li>微信通知要先绑定模板后才能选择开启状态。</li>
    </ul>
  </div>
  <div class="homepage-focus" nctype="sellerTplContent">
    <div class="title">
      <h3>消息模板切换选择</h3>
      <ul class="tab-base nc-row">
        <li><a href="javascript:void(0);" class="current">站内信模板</a></li>
        <li><a href="javascript:void(0);">手机短信模板</a></li>
        <li><a href="javascript:void(0);">邮件模板</a></li>
        <li><a href="javascript:void(0);">微信通知模板</a></li>
      </ul>
    </div>
    <!-- 站内信 S -->
    <form class="tab-content" method="post" name="message_form" >
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="code" value="<?php echo $output['mmtpl_info']['mmt_code'];?>" />
      <input type="hidden" name="type" value="message" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label>站内信开关</label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="message_switch1" class="cb-enable <?php if($output['mmtpl_info']['mmt_message_switch'] == 1){?>selected<?php }?>"><?php echo $lang['open'];?></label>
              <label for="message_switch0" class="cb-disable <?php if($output['mmtpl_info']['mmt_message_switch'] == 0){?>selected<?php }?>"><?php echo $lang['close'];?></label>
              <input id="message_switch1" name="message_switch" <?php if($output['mmtpl_info']['mmt_message_switch'] == 1){?>checked="checked"<?php }?> value="1" type="radio">
              <input id="message_switch0" name="message_switch" <?php if($output['mmtpl_info']['mmt_message_switch'] == 0){?>checked="checked"<?php }?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>消息内容</label>
          </dt>
          <dd class="opt">
            <textarea name="message_content" rows="6" class="tarea"><?php echo $output['mmtpl_info']['mmt_message_content'];?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"> <a href="JavaScript:void(0);" onclick="document.message_form.submit();" class="ncap-btn-big ncap-btn-green"><?php echo $lang['nc_submit'];?></a> </div>
      </div>
    </form>
    <!-- 站内信 E --> 
    <!-- 短消息 S -->
    <form class="tab-content" method="post" name="short_name" style="display:none;">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="code" value="<?php echo $output['mmtpl_info']['mmt_code'];?>" />
      <input type="hidden" name="type" value="short" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label>手机短信开关</label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="short_switch1" class="cb-enable <?php if($output['mmtpl_info']['mmt_short_switch'] == 1){?>selected<?php }?>"><?php echo $lang['open'];?></label>
              <label for="short_switch0" class="cb-disable <?php if($output['mmtpl_info']['mmt_short_switch'] == 0){?>selected<?php }?>"><?php echo $lang['close'];?></label>
              <input id="short_switch1" name="short_switch" <?php if($output['mmtpl_info']['mmt_short_switch'] == 1){?>checked="checked"<?php }?> value="1" type="radio">
              <input id="short_switch0" name="short_switch" <?php if($output['mmtpl_info']['mmt_short_switch'] == 0){?>checked="checked"<?php }?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>消息内容</label>
          </dt>
          <dd class="opt">
            <textarea name="short_content" rows="6" class="tarea"><?php echo $output['mmtpl_info']['mmt_short_content'];?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"> <a href="JavaScript:void(0);" onclick="document.short_name.submit();" class="ncap-btn-big ncap-btn-green"><?php echo $lang['nc_submit'];?></a> </div>
      </div>
    </form>
    <!-- 短消息 E --> 
    <!-- 邮件 S -->
    <form class="tab-content" method="post" name="mail_form" style="display:none;">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="code" value="<?php echo $output['mmtpl_info']['mmt_code'];?>" />
      <input type="hidden" name="type" value="mail" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label>邮件开关</label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="mail_switch1" class="cb-enable <?php if($output['mmtpl_info']['mmt_mail_switch'] == 1){?>selected<?php }?>"><?php echo $lang['open'];?></label>
              <label for="mail_switch0" class="cb-disable <?php if($output['mmtpl_info']['mmt_mail_switch'] == 0){?>selected<?php }?>"><?php echo $lang['close'];?></label>
              <input id="mail_switch1" name="mail_switch" <?php if($output['mmtpl_info']['mmt_mail_switch'] == 1){?>checked="checked"<?php }?> value="1" type="radio">
              <input id="mail_switch0" name="mail_switch" <?php if($output['mmtpl_info']['mmt_mail_switch'] == 0){?>checked="checked"<?php }?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>邮件标题</label>
          </dt>
          <dd class="opt">
            <textarea name="mail_subject" rows="6" class="tarea"><?php echo $output['mmtpl_info']['mmt_mail_subject'];?></textarea>
            <span class="err"></span>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>邮件内容</label>
          </dt>
          <dd class="opt">
            <?php showEditor('mail_content', $output['mmtpl_info']['mmt_mail_content']);?>
          </dd>
          <p class="notic"> </p>
          </dd>
        </dl>
        <div class="bot"> <a href="JavaScript:void(0);" onclick="document.mail_form.submit();" class="ncap-btn-big ncap-btn-green"><?php echo $lang['nc_submit'];?></a> </div>
      </div>
    </form>
    <!-- 邮件 E --> 
    <!-- 短消息 S -->
    <form class="tab-content" method="post" name="wx_name" style="display:none;">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="code" value="<?php echo $output['mmtpl_info']['mmt_code'];?>" />
      <input type="hidden" name="type" value="wx" />
      <div class="ncap-form-default">
        <?php if(!empty($output['mmtpl_info']['mp_msg_id'])){?>
        <dl class="row">
          <dt class="tit">
            <label>微信通知开关</label>
          </dt>
          <dd class="opt">
            <div class="onoff">
              <label for="wx_switch1" class="cb-enable <?php if($output['mmtpl_info']['mmt_wx_switch'] == 1){?>selected<?php }?>"><?php echo $lang['open'];?></label>
              <label for="wx_switch0" class="cb-disable <?php if($output['mmtpl_info']['mmt_wx_switch'] == 0){?>selected<?php }?>"><?php echo $lang['close'];?></label>
              <input id="wx_switch1" name="wx_switch" <?php if($output['mmtpl_info']['mmt_wx_switch'] == 1){?>checked="checked"<?php }?> value="1" type="radio">
              <input id="wx_switch0" name="wx_switch" <?php if($output['mmtpl_info']['mmt_wx_switch'] == 0){?>checked="checked"<?php }?> value="0" type="radio">
            </div>
            <p class="notic"> </p>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>消息内容</label>
          </dt>
          <dd class="opt">
            <pre><?php echo $output['mmtpl_info']['mmt_wx_content'];?></pre>
            <span class="err"></span>
            <p class="notic">模板内容是微信固定的，不可编辑。</p>
          </dd>
        </dl>
        <div class="bot"> <a href="JavaScript:void(0);" onclick="document.wx_name.submit();" class="ncap-btn-big ncap-btn-green"><?php echo $lang['nc_submit'];?></a> </div>
        <?php } else { ?>
        <p class="notic">未绑定公众号的模板，不能使用。</p>
        <?php } ?>
      </div>
    </form>
  </div>
</div>
<script>
$(function(){
    $('div[nctype="sellerTplContent"] > .title > ul').find('a').click(function(){
        $(this).addClass('current').parent().siblings().find('a').removeClass('current');
        var _index = $(this).parent().index();
        var _form = $('div[nctype="sellerTplContent"]').find('form');
        _form.hide();
        _form.eq(_index).show();
    });
});
</script>