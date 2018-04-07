<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3><?php echo $lang['nc_mall_set'];?></h3>
        <h5><?php echo $lang['nc_mall_set_subhead'];?></h5>
      </div>
      <?php echo $output['top_link'];?> </div>
  </div>
  <form method="post" id="settingForm" name="settingForm">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <dl class="row">
        <dt class="tit">发票内容</dt>
        <dd class="opt">
          <textarea id="inv_content" name="inv_content" class="tarea" style="width:120px;height:360px;"><?php echo $output['list_setting']['inv_content'];?></textarea>
        <p class="notic">可以对内容添加、编辑、删除，保存后会员下单时，如果选择开发票会显示在页面中。</p>
        </dd>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.settingForm.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
