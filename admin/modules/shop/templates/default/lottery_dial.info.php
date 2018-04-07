<?php defined('InShopNC') or exit('Access Invalid!');?>
<style>
.ui-timepicker-select{ padding: 2px 0px; }
</style>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
    	<a class="back" href="javascript:history.go(-1);" title="返回">
        	<i class="fa fa-arrow-circle-o-left"></i>
      	</a>
      <div class="subject">
        <h3>大转盘管理</h3>
        <h5>大转盘活动发布及相关设置</h5>
      </div>
    </div>
  </div>
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>设置指针距离顶部距离、指针图片、大转盘图片,设置完成之后点击保存可查看转盘图片，确认提交之后生效</li>
      <li>转盘初始位置指针必须与最后一个奖项和第一个奖项的分割线重合</li>
    </ul>
  </div>
    <form id="add_form" method="post" enctype="multipart/form-data">
      <input type="hidden" name="form_submit" value="ok" />
      <input type="hidden" name="lot_id" value="<?php echo $output['dial_info']['lot_id'];?>" />
      <div class="ncap-form-default">
        <dl class="row">
          <dt class="tit">
            <label for="turntableimage"><em>*</em><?php echo '转盘图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
              <img src="<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_bg'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_dial.png";}?>" style="max-width: 200px">
            </div>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="pointerimage"><em>*</em><?php echo '指针图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
              <img src="<?php if(is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'])){echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_dial_pointer'];}else{echo UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS."/images/default_pointer.png";}?>" style="max-width: 200px">
            </div>
          </dd>
        </dl>        
        <dl class="row">
          <dt class="tit">
            <label for="active_bg_image"><?php echo '活动背景图片';?></label>
          </dt>
          <dd class="opt">
            <div class="input-file-show">
              <img src="<?php echo is_file(BASE_UPLOAD_PATH.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg']) ? UPLOAD_SITE_URL.DS.ATTACH_LOTTERY_DIAL.DS.$output['dial_info']['lot_bg'] : "";?>" style="max-width: 200px">
            </div>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit"><em>*</em><?php echo '活动时间';?><?php echo $lang['nc_colon'];?></dt>
          <dd class="opt">
            <?php echo date('Y-m-d H:i',$output['dial_info']['start_time']?$output['dial_info']['start_time']:TIMESTAMP); ?>
             到&nbsp;
            <?php echo date('Y-m-d H:i',$output['dial_info']['end_time']?$output['dial_info']['end_time']:TIMESTAMP+86400); ?>
            <span class="error"></span>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="wintips"><em>*</em><?php echo '活动名称';?></label>
          </dt>
          <dd class="opt">
            <?php echo $output['dial_info']['lot_name'];?>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="acexplain"><?php echo '活动说明';?></label>
          </dt>
          <dd class="opt">
            <?php echo $output['dial_info']['lot_discription'];?>
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label for="rate_weight"><em>*</em><?php echo '中奖率';?></label>
          </dt>
          <dd class="opt">
            <?php echo $output['dial_info']['lot_weight'];?>&nbsp;%&nbsp;
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit">
            <label>抽取方式</label>
          </dt>
          <dd class="opt">
            <?php if($output['dial_info']['lot_type']== 0){?>
            按会员抽取
            <?php }else{?>
            按订单抽取
            <?php }?>
          </dd>
        </dl>
        <dl class="row" id="member_num" <?php if($output['dial_info']['lot_type'] == 1){?> style="display: none"<?php }?>>
          <dt class="tit">
            <label for="member_number"><em>*</em><?php echo '每个会员ID抽取次数';?></label>
          </dt>
          <dd class="opt">
            <?php echo $output['dial_info']['lot_count']?$output['dial_info']['lot_count']:1;?>次
          </dd>
        </dl>
        <dl class="row">
          <dt class="tit"><em>*</em>奖项设置</dt>
          <dd class="opt" id="pricerang_table">
            <ul class="ncap-lot-ajax-add">
            <?php if(!empty($output['dial_info']['lot_info'])){ $count_prize = count($output['dial_info']['lot_info']);?>
            <?php foreach($output['dial_info']['lot_info'] as $k => $val){?>
              <li index-data="<?php echo $k;?>">
                <div class="rate_info">
                  <label>奖项名称：</label>
                  <input type="text" class="txt w100 mr5" name="rate_name[<?php echo $k;?>]" value="<?php echo $val['rate_name']?>" readonly>
                  <label class="ml20 mr10">奖品类型：</label>
                  <select name="prize_type[<?php echo $k;?>]" disabled="disabled">
                    <option value="0" <?php if($val['prize_type'] == 0) echo 'selected="selected"';?>>未中奖</option>
                    <option value="1" <?php if($val['prize_type'] == 1) echo 'selected="selected"';?>>积分</option>
                    <option value="2" <?php if($val['prize_type'] == 2) echo 'selected="selected"';?>>平台红包</option>
                    <option value="3" <?php if($val['prize_type'] == 3) echo 'selected="selected"';?>>实物</option>
                  </select>
                </div>
                <div class="prize_info mt20 mb10">
                  <?php switch($val['prize_type']){
                      case 0:
                  ?>
                  <label>未中奖提示语：</label>
                  <input type="text" class="txt w250 mr15" name="prize[<?php echo $k;?>][unprize]" value="<?php echo $val['prize']['unprize']?>" readonly />     
                  <?php
                        break;
                      case 1:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>" readonly />
                  <label>奖励积分数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_num]" value="<?php echo $val['prize']['prize_num']?>" readonly />
                  <?php
                        break;
                      case 2:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>" readonly />
                  <span class="redpacket">
                    <span style="border:dashed 1px #E0E0E0; padding: 5px; "><i class="fa fa-cc-discover"></i><?php echo $val['prize']['coupon_title']?></span>
                  </span>
                  <a href="JavaScript:void(0);" onclick="coupon_list(<?php echo $k;?>)" class="ncap-btn">选择平台红包</a>
                  <?php
                        break;
                      case 3:
                  ?>
                  <label>奖品数：</label>
                  <input type="text" class="txt w100 mr5" name="prize[<?php echo $k;?>][prize_amount]" value="<?php echo $val['prize']['prize_amount']?>" readonly />
                  <label>实物名称：</label>
                  <input type="text" class="txt w250 mr5" name="prize[<?php echo $k;?>][prize_name]" value="<?php echo $val['prize']['prize_name']?>" readonly />
                  <?php 
                        break;
                    }
                  ?>
                </div>
              </li>
            <?php }?>
            <?php }?>  
            </ul>
          </dd>
        </dl>
        <div class="bot"><a id="submitBtn" class="ncap-btn-big ncap-btn-green" href="JavaScript:void(0);">返回</a></div>
      </div>
    </form>
</div>

<script>
  $(function(){
    $('#submitBtn').click(function(){
      history.back();
    });
  });
</script>
