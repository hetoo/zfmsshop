<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"> <a class="back" href="<?php echo urlAdminShop('promotion_flash', 'flash_list'); ?>" title="返回列表"> <i class="fa fa-arrow-circle-o-left"></i> </a>
      <div class="subject">
        <h3>闪购管理 - 查看活动“<?php echo $output['flash_info']['flash_name'];?>”</h3>
        <h5>查看商家闪购活动详情</h5>
      </div>
    </div>
  </div>

  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>">活动规则</h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>开始时间<?php echo '：'.date('Y-m-d H:i',$output['flash_info']['start_time']);?> - 结束时间<?php echo '：'.date('Y-m-d H:i',$output['flash_info']['end_time']);?></li>
      <li>购买上限：<?php echo $output['flash_info']['upper_limit'];?> </li>
      <li>状态 <?php echo '：'.$output['flash_info']['flash_state_text'];?> </li>
    </ul>
  </div>
  <!-- 列表 -->
  <form id="list_form" method="post">
    <input type="hidden" id="object_id" name="object_id"  />
    <table class="flex-table">
      <thead>
        <tr>
          <th width="24" align="center" class="sign"><i class="ico-check"></i></th>
          <th width="60" align="center" class="handle-s"><?php echo $lang['nc_handle'];?></th>
          <th width="350" align="left"><?php echo $lang['goods_name'];?></th>
          <th width="80" align="center">商品图片</th>
          <th width="100" align="center">商品价格（元）</th>
          <th width="100" align="center">闪购价格（元）</th>
          <th width="100" align="center">折扣率</th>
          <?php if($output['flash_info']['editable']) { ?>
          <th width="80" align="center">推荐</th>
          <?php } ?>
          <th></th>
        </tr>
      </thead>
      <tbody id="treet1">
        <?php if(!empty($output['list']) && is_array($output['list'])){ ?>
        <?php foreach($output['list'] as $k => $val){ ?>
        <tr>
          <td class="sign"><i class="ico-check"></i></td>
          <td class="handle-s"><a href="<?php echo $val['goods_url'];?>" class="btn green" target="_blank"><i class="fa fa-list-alt"></i>查看</a></td>
          <td><?php echo $val['goods_name']; ?></td>
          <td><a class="pic-thumb-tip" onmouseover="toolTip('<img src=<?php echo $val['image_url'];?>>')" onmouseout="toolTip()" href="javascript:void(0);"><i class="fa fa-picture-o"></i></a></td>
          <td><span><?php echo $val['goods_price'];?></span></td>
          <td><span><?php echo $val['flash_price'];?></span></td>
          <td><span><?php echo $val['flash_discount'];?></span></td>
          <?php if($output['flash_info']['editable']) { ?>
          <td class="yes-onoff"><?php if($val['flash_recommend']){ ?>
            <a href="JavaScript:void(0);" class=" enabled" ajax_branch='recommend' nc_type="inline_edit" fieldname="flash_recommend" fieldid="<?php echo $val['flash_goods_id']?>" fieldvalue="1" title="<?php echo $lang['nc_editable'];?>"><img src="<?php echo ADMIN_TEMPLATES_URL;?>/images/transparent.gif"></a>
            <?php }else { ?>
            <a href="JavaScript:void(0);" class=" disabled" ajax_branch='recommend' nc_type="inline_edit" fieldname="flash_recommend" fieldid="<?php echo $val['flash_goods_id']?>" fieldvalue="0" title="<?php echo $lang['nc_editable'];?>"><img src="<?php echo ADMIN_TEMPLATES_URL;?>/images/transparent.gif"></a>
            <?php } ?></td>
          <?php } ?>
          <td></td>
        </tr>
        <?php } ?>
        <?php }else { ?>
        <tr>
          <td class="no-data" colspan="100"><i class="fa fa-exclamation-triangle"></i><?php echo $lang['nc_no_record'];?></td>
        </tr>
        <?php } ?>
      </tbody>
    </table>
  </form>
</div>
<script type="text/javascript">
$(function(){
	$('.flex-table').flexigrid({
		height:'auto',// 高度自动
		usepager: false,// 不翻页
		striped:false,// 不使用斑马线
		resizable: false,// 不调节大小
		title: '参加该活动商品列表',// 表格标题
		reload: false,// 不使用刷新
		columnControl: false,// 不使用列控制
	});
});
</script>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.edit.js" charset="utf-8"></script>