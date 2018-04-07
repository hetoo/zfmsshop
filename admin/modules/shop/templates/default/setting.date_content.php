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
        <dt class="tit">配送日期</dt>
        <dd class="opt">
          <textarea id="order_date_content" name="order_date_content" class="tarea" style="width:150px;height:150px;"><?php echo $output['list_setting']['order_date_content'];?></textarea>
        <p class="notic">可以对内容添加、编辑、删除，保存后下单时会显示在订单确认页面中。</p>
        </dd>
        </dd>
      </dl>
      <dl class="row">
        <dt class="tit">
          <label for="order_date_msg">配送说明</label>
        </dt>
        <dd class="opt">
          <input id="order_date_msg" name="order_date_msg" value="<?php echo $output['list_setting']['order_date_msg'];?>" class="input-txt" type="text" />
          <span class="err"></span>
          <p class="notic">对买家选择配送选择的提示文字，在其下部显示。</p>
        </dd>
      </dl>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.settingForm.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
