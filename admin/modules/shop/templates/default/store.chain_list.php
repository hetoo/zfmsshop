<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back();" title="返回列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>店铺管理 - 查看店铺“<?php echo $output['store_array']['store_name'];?>”的门店</h3>
        <h5><?php echo $lang['nc_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=store&op=get_chain_xml&store_id=<?php echo $output['store_array']['store_id'];?>',
        colModel : [
            {display: '操作', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
            {display: '门店ID', name : 'chain_id', width : 40, sortable : true, align: 'center'},
            {display: '门店名称', name : 'chain_name', width : 150, sortable : false, align: 'left'},
            {display: '店主账号', name : 'chain_user', width : 120, sortable : true, align: 'left'},
            {display: '门店状态', name : 'chain_state', width : 120, sortable : true, align: 'center'},
            {display: '门店图片', name : 'chain_img', width: 60, sortable : false, align : 'center'},
            {display: '门店LOGO', name : 'chain_logo', width: 60, sortable : false, align : 'center'},
            {display: '所在地区', name : 'area_info', width : 150, sortable : false, align : 'left'},
            {display: '详细地址', name : 'chain_address', width : 200, sortable : false, align : 'left'},
            {display: '交通线路', name : 'chain_traffic_line', width : 200, sortable : false, align : 'left'},
            {display: '营业时间', name : 'chain_opening_hours', width : 150, sortable : false, align : 'left'},
            {display: '联系电话', name : 'chain_phone', width : 120, sortable : false, align : 'left'}
            ],
        searchitems : [
            {display: '门店名称', name : 'chain_name', isdefault: true},
            {display: '店主账号', name : 'chain_user'}
            ],
        sortname: "chain_id",
        sortorder: "asc",
        title: '门店列表'
    });
});
</script>