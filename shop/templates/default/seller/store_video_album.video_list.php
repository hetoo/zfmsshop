<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="tabmenu">
  <?php include template('layout/submenu');?>
</div>
<form name="select_sort" id="select_sort">
  <input type="hidden" name="act" value="store_video" />
  <input type="hidden" name="op" value="album_video_list" />
  <input type="hidden" name="id" value="<?php echo $output['class_info']['video_class_id']?>" />
  <table class="search-form">
    <tr>
      <td>&nbsp;</td><th>排序方式</th>
      <td class="w100">
        <select name="sort" id="video_sort">
          <option value="0"  <?php if($_GET['sort'] == '0'){?>selected <?php }?> >按上传时间从晚到早</option>
          <option value="1"  <?php if($_GET['sort'] == '1'){?>selected <?php }?> >按上传时间从早到晚</option>
          <option value="2"  <?php if($_GET['sort'] == '2'){?>selected <?php }?> >按视频从大到小</option>
          <option value="3"  <?php if($_GET['sort'] == '3'){?>selected <?php }?> >按视频从小到大</option>
          <option value="4"  <?php if($_GET['sort'] == '4'){?>selected <?php }?> >按视频名降序</option>
          <option value="5"  <?php if($_GET['sort'] == '5'){?>selected <?php }?> >按视频名升序</option>
        </select>
      </td>
    </tr>
  </table>
</form>
<table class="ncsc-default-table">
  <thead>
  <tr>
    <th class="w100">视频名称</th>
    <th class="w100">视频大小</th>
    <th class="w80">上传时间</th>
    <th class="w130">操作</th>
  </tr>
  </thead>
  <tbody>
  <?php if(!empty($output['video_list']) && is_array($output['video_list'])){?>
    <?php foreach($output['video_list'] as $key => $value){?>
      <tr class="bd-line">
        <td height="30"><?php echo $value['video_name'];?></td>
        <td><?php echo number_format($value['video_size']/1024/1024,2) . 'MB';?></td>
        <td><?php echo date('Y-m-d H:s', $value['upload_time']);?></td>
        <td class="nscs-table-handle">
          <span><a class="btn-grapefruit" href="javascript:void(0);" nctype="show_video" data-video-id="<?php echo $value['video_id'];?>"><i class="icon-eye-open"></i>
              <p><?php echo '查看';?></p></a>
          </span><span><a nctype="btn_del_video" data-video-id="<?php echo $value['video_id'];?>" href="javascript:;" class="btn-grapefruit"><i class="icon-trash"></i>
              <p><?php echo $lang['nc_del'];?></p></a></span>
        </td>
      </tr>
    <?php }?>
  <?php }else{?>
    <tr>
      <td colspan="20" class="norecord"><div class="warning-option"><i class="icon-warning-sign"></i><span><?php echo $lang['no_record'];?></span></div></td>
    </tr>
  <?php }?>
  </tbody>
  <tfoot>
  <tr>
    <td colspan="20"><div class="pagination"><?php echo $output['show_page']; ?></div></td>
  </tr>
  </tfoot>
</table>
<form id="del_form" method="post" action="<?php echo urlShop('store_video', 'album_video_del');?>">
  <input id="del_video_id" name="video_id" type="hidden" />
</form>
<script type="text/javascript">
  $(document).ready(function(){
    $('[nctype="btn_del_video"]').on('click', function() {
      var video_id = $(this).attr('data-video-id');
      if(confirm('确认删除？')) {
        $('#del_video_id').val(video_id);
        ajaxpost('del_form', '', '', 'onerror');
      }
    });

    $('[nctype="show_video"]').click(function(){
      _video_id = $(this).attr('data-video-id');
      ajax_form('show_video', '查看视频', 'index.php?act=store_video&op=album_video_show&video_id=' + _video_id, 640);
    });

    $("#video_sort").change(function(){
      $('#select_sort').submit();
    });
    $("#video_move").click(function(){
      if($('#batchClass').css('display') == 'none'){
        $('#batchClass').show();
      }else {
        $('#batchClass').hide();
      }
    });
  });
</script>
