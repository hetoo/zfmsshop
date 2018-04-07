<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>闪购管理</h3>
        <h5>店铺商品闪购促销活动设置及管理</h5>
      </div>
      <ul class="tab-base nc-row">
        <?php   foreach($output['menu'] as $menu) {  if($menu['menu_type'] == 'text') { ?>
        <li><a href="JavaScript:void(0);" class="current"><?php echo $menu['menu_name'];?></a></li>
        <?php }  else { ?>
        <li><a href="<?php echo $menu['menu_url'];?>" ><?php echo $menu['menu_name'];?></a></li>
        <?php  } }  ?>
      </ul>
    </div>
  </div>
  <!-- 操作说明 -->
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>可以传三张图片，在闪购页头部显示，建议使用2000px * 385px</li>
      <li>可选择删除图片，提交保存后生效</li>
    </ul>
  </div>
  <form method="post" enctype="multipart/form-data" name="form1">
    <input type="hidden" name="form_submit" value="ok" />
    <div class="ncap-form-default">
      <?php for ($i = 1;$i <= $output['size'];$i++) { ?>
      <dl class="row">
        <dt class="tit">
          <label>横幅大图<?php echo $i;?></label>
        </dt>
        <dd class="opt">
          <div class="input-file-show">
            <?php if(!empty($output['list'][$i]['pic'])){ ?>
            <span class="show" id="show<?php echo $i;?>"><a class="nyroModal" rel="gal" href="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/'.$output['list'][$i]['pic'];?>"><img src="<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/'.$output['list'][$i]['pic'];?>" onMouseOver="toolTip('<img src=<?php echo UPLOAD_SITE_URL.'/'.ATTACH_COMMON.'/'.$output['list'][$i]['pic'];?>>')" onMouseOut="toolTip()"/></a></span>
            <?php } ?>
            <span class="type-file-box">
            <input type="text" name="textfield" id="textfield<?php echo $i;?>" class="type-file-text" />
            <input type="button" name="button" id="button<?php echo $i;?>" value="选择上传..." class="type-file-button" />
            <input class="type-file-file" id="pic<?php echo $i;?>" name="pic<?php echo $i;?>" type="file" size="30" hidefocus="true" title="点击前方预览图可查看大图，点击按钮选择文件并提交表单后上传生效">
            <input type="hidden" name="show_pic<?php echo $i;?>" id="show_pic<?php echo $i;?>" value="<?php echo $output['list'][$i]['pic'];?>" />
            </span></div> <a href="JavaScript:void(0);" class="ncap-btn" onclick="clear_pic(<?php echo $i;?>)"><i class="fa fa-trash"></i>删除</a> 
                <label title="图片链接，以[http://]开头"><i class="fa fa-link"></i><input name="url<?php echo $i;?>" value="<?php echo $output['list'][$i]['url'];?>" class="input-txt" type="text"></label>
          <p class="notic"></p>
        </dd>
      </dl>
      <?php } ?>
      <div class="bot"><a href="JavaScript:void(0);" class="ncap-btn-big ncap-btn-green" onclick="document.form1.submit()"><?php echo $lang['nc_submit'];?></a></div>
    </div>
  </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script>

<script type="text/javascript">
$(function(){
    $('input[class="type-file-file"]').change(function(){
    	var pic=$(this).val();
    	var extStart=pic.lastIndexOf(".");
    	var ext=pic.substring(extStart,pic.length).toUpperCase();
    	$(this).parent().find(".type-file-text").val(pic);
		if(ext!=".PNG"&&ext!=".GIF"&&ext!=".JPG"&&ext!=".JPEG"){
		    alert("<?php echo $lang['default_img_wrong'];?>");
			$(this).attr('value','');
			return false;
		}
	});
    $('.nyroModal').nyroModal();
});
function clear_pic(n){//置空
	$("#show"+n+"").remove();
	$("#textfield"+n+"").val("");
	$("#pic"+n+"").val("");
	$("#show_pic"+n+"").val("");
}
</script> 
