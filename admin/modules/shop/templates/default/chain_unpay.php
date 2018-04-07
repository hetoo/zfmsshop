<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title">
      <div class="subject">
        <h3>门店管理</h3>
        <h5>店铺的审核及经营管理操作</h5>
      </div>
      <?php echo $output['top_link'];?>
    </div>
  </div>
  <div id="flexigrid"></div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=chain&op=get_xml_chain_unpay_list',
        colModel : [
            {display: '操作', name : 'operation', width : 60, sortable : false, align: 'center', className: 'handle-s'},
            {display: '门店ID', name : 'chain_id', width : 40, sortable : true, align: 'center'},
            {display: '门店名称', name : 'chain_name', width : 150, sortable : true, align: 'left'},
            {display: '门店账号', name : 'chain_user', width : 120, sortable : true, align: 'left'},
            {display: '门店图片', name : 'chain_img', width: 60, sortable : false, align : 'center'},
            {display: '门店LOGO', name : 'chain_logo', width: 60, sortable : false, align : 'center'},
            {display: '支持配送', name : 'is_transport', width : 80, sortable : true, align: 'center'},
            {display: '转接订单', name : 'is_forward_order', width : 80, sortable : true, align: 'center'},
            {display: '支持自提', name : 'is_self_take', width : 80, sortable : true, align: 'center'},
            {display: '支持代收货', name : 'is_collection', width : 80, sortable : true, align: 'center'},
            {display: '申请时间', name : 'chain_apply_time', width : 120, sortable : true, align: 'center'},
            {display: '所在地区', name : 'area_info', width : 150, sortable : false, align : 'left'},
            {display: '详细地址', name : 'chain_address', width : 200, sortable : false, align : 'left'},
            {display: '所属店铺', name : 'store_name', width : 120, sortable : false, align: 'left'}
            ],
        searchitems : [
            {display: '门店名称', name : 'chain_name', isdefault: true},
            {display: '店主账号', name : 'chain_user'},
            {display: '所属店铺', name : 'store_name'}
            ],
        sortname: "chain_id",
        sortorder: "asc",
        title: '待付款门店申请列表'
    });
});
</script> 
