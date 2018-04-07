<?php defined('InShopNC') or exit('Access Invalid!');?>
<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <a class="back" href="index.php?act=goods_video_album&amp;op=index" title="返回视频空间"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>视频空间 - <?php echo $output['store_name'];?></h3>
        <h5>商品视频及商家店铺视频管理</h5>
      </div>
    </div>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
  $(function(){
    $("#flexigrid").flexigrid({
      url: 'index.php?act=goods_video_album&op=get_video_xml&video_class_id=<?php echo $output['video_class_id']?>&store_id=<?php echo $output['store_id']?>',
      colModel : [
        {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
        {display: '视频名称', name : 'video_name', width : 120, sortable : false, align: 'center'},
        {display: '视频大小', name : 'video_size', width : 80, sortable : true, align: 'center'},
        {display: '上传时间', name : 'upload_time', width : 120, sortable : false, align: 'center'},
        {display: '店铺名称', name : 'store_name', width : 180, sortable : false, align: 'center'},
      ],
      buttons : false,
      searchitems : false,
      sortname: "upload_time",
      sortorder: "asc",
      title: '视频列表'
    });
  });

  function fg_del(video_id , store_id) {
    if(confirm('删除后将不能恢复，确认删除这项吗？')){
      $.getJSON('index.php?act=goods_video_album&op=del_album_video', {video_id : video_id , store_id : store_id}, function(data){
        if (data.state) {
          $("#flexigrid").flexReload();
        } else {
          showError(data.msg)
        }
      });
    }
  }

  //查看视频
  function fg_show_video(ids) {
    _uri = "index.php?act=goods_video_album&op=show_video&id=" + ids;
    CUR_DIALOG = ajax_form('show_video', '查看视频', _uri, 640);
  }
</script>
