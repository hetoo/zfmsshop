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
  <div class="explanation" id="explanation">
    <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
      <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
      <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
    <ul>
      <li>如果当前门店处于关闭状态，前台将不能继续浏览该门店，但是店主仍然可以编辑该门店</li>
    </ul>
  </div>
  <div id="flexigrid"></div>
    <div class="ncap-search-ban-s" id="searchBarOpen"><i class="fa fa-search-plus"></i>高级搜索</div>
    <div class="ncap-search-bar">
      <div class="handle-btn" id="searchBarClose"><i class="fa fa-search-minus"></i>收起边栏</div>
      <div class="title">
        <h3>高级搜索</h3>
      </div>
      <form method="get" name="formSearch" id="formSearch">
        <div id="searchCon" class="content">
          <div class="layout-box">
            <dl>
              <dt>门店名称</dt>
              <dd>
                <input type="text" value="" name="chain_name" id="chain_name" class="s-input-txt">
              </dd>
            </dl>
            <dl>
              <dt>店主账号</dt>
              <dd>
                <input type="text" value="" name="chain_user" id="chain_user" class="s-input-txt">
              </dd>
            </dl>
            <dl>
              <dt>所属店铺名称</dt>
              <dd>
                <input type="text" value="" name="store_name" id="store_name" class="s-input-txt">
              </dd>
            </dl>
            <dl>
              <dt>是否自营</dt>
              <dd>
                <select name="is_own" class="s-select">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
                  <option value="1">是</option>
                  <option value="0">否</option>
                </select>
              </dd>
            </dl>
            <dl>
              <dt>门店状态</dt>
              <dd>
                <select name="chain_state" class="s-select">
                  <option value=""><?php echo $lang['nc_please_choose'];?></option>
                  <option value="3">未通过</option>
                  <option value="1">开启</option>
                  <option value="0">关闭</option>
                </select>
              </dd>
            </dl>
          </div>
        </div>
        <div class="bottom">
          <a href="javascript:void(0);" id="ncsubmit" class="ncap-btn ncap-btn-green">提交查询</a>
          <a href="javascript:void(0);" id="ncreset" class="ncap-btn ncap-btn-orange" title="撤销查询结果，还原列表项所有内容"><i class="fa fa-retweet"></i><?php echo $lang['nc_cancel_search'];?></a>
        </div>
      </form>
    </div>
</div>
<script type="text/javascript">
$(function(){
    $("#flexigrid").flexigrid({
        url: 'index.php?act=chain&op=get_xml_chain_list',
        colModel : [
            {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
            {display: '门店ID', name : 'chain_id', width : 40, sortable : true, align: 'center'},
            {display: '门店名称', name : 'chain_name', width : 150, sortable : true, align: 'left'},
            {display: '自营', name : 'chain_name', width : 40, sortable : true, align: 'center'},
            {display: '门店账号', name : 'chain_user', width : 120, sortable : true, align: 'left'},
            {display: '门店图片', name : 'chain_img', width: 60, sortable : false, align : 'center'},
            {display: '门店LOGO', name : 'chain_logo', width: 60, sortable : false, align : 'center'},
            {display: '当前状态', name : 'chain_state', width : 80, sortable : true, align: 'center'},
            {display: '支持配送', name : 'is_transport', width : 80, sortable : true, align: 'center'},
            {display: '转接订单', name : 'is_forward_order', width : 80, sortable : true, align: 'center'},
            {display: '支持自提', name : 'is_self_take', width : 80, sortable : true, align: 'center'},
            {display: '支持代收货', name : 'is_collection', width : 80, sortable : true, align: 'center'},
            {display: '开店时间', name : 'chain_time', width : 100, sortable : true, align: 'center'},
            {display: '关闭时间', name : 'chain_close_time', width : 100, sortable : true, align: 'center'},
            {display: '所在地区', name : 'area_info', width : 150, sortable : false, align : 'left'},
            {display: '详细地址', name : 'chain_address', width : 200, sortable : false, align : 'left'},
            {display: '所属店铺', name : 'store_name', width : 120, sortable : false, align: 'left'}
            ],
        buttons : [
            {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }						
        ],
        searchitems : [
            {display: '门店名称', name : 'chain_name', isdefault: true},
            {display: '店主账号', name : 'chain_user'},
            {display: '所属店铺', name : 'store_name'}
            ],
        sortname: "chain_id",
        sortorder: "asc",
        title: '店铺列表'
    });

    // 高级搜索提交
    $('#ncsubmit').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=chain&op=get_xml_chain_list&'+$("#formSearch").serialize(),query:'',qtype:''}).flexReload();
    });

    // 高级搜索重置
    $('#ncreset').click(function(){
        $("#flexigrid").flexOptions({url: 'index.php?act=chain&op=get_xml_chain_list'}).flexReload();
        $("#formSearch")[0].reset();
    });
});

function fg_operation(name, bDiv) {
    if (name == 'csv') {
        if ($('.trSelected', bDiv).length == 0) {
            if (!confirm('您确定要下载全部数据吗？')) {
                return false;
            }
        }
        var itemids = new Array();
        $('.trSelected', bDiv).each(function(i){
            itemids[i] = $(this).attr('data-id');
        });
        fg_csv(itemids);
    }
}

function fg_csv(ids) {
    id = ids.join(',');
    window.location.href = $("#flexigrid").flexSimpleSearchQueryString()+'&op=export_csv&id=' + id;
}
</script>