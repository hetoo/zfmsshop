<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>实名认证</h3>
                <h5>会员实名认证管理</h5>
            </div>
        </div>
    </div>
    <!-- 操作说明 -->
    <div class="explanation" id="explanation">
        <div class="title" id="checkZoom"><i class="fa fa-lightbulb-o"></i>
            <h4 title="<?php echo $lang['nc_prompts_title'];?>"><?php echo $lang['nc_prompts'];?></h4>
            <span id="explanationZoom" title="<?php echo $lang['nc_prompts_span'];?>"></span> </div>
        <ul>
            <li>在认证申请中可对普通会员提出的实名认证进行审核</li>
        </ul>
    </div>
    <div id="flexigrid"></div>
</div>
<script type="text/javascript">
    $(function(){
        $("#flexigrid").flexigrid({
            url: 'index.php?act=certification&op=get_xml',
            colModel : [
                {display: '操作', name : 'operation', width : 150, sortable : false, align: 'center'},
                {display: '会员ID', name : 'member_id', width : 40, sortable : true, align: 'center'},
                {display: '会员名称', name : 'member_name', width : 150, sortable : true, align: 'left'},
                {display: '申请状态', name : 'distri_stat', width : 60, sortable : true, align: 'left'},
                {display: '会员手机', name : 'member_mobile', width : 80, sortable : true, align: 'center'},
                {display: '会员邮箱', name : 'member_email', width : 150, sortable : true, align: 'left'},
                {display: '真实姓名', name : 'id_card_name', width : 150, sortable : true, align: 'left'},
                {display: '身份证号', name : 'id_card_code', width : 150, sortable : true, align: 'left'},
            ],
            buttons : [
                {display: '<i class="fa fa-file-excel-o"></i>导出数据', name : 'csv', bclass : 'csv', title : '将选定行数据导出CSV文件', onpress : fg_operation }	,
            ],
            searchitems : [
                {display: '会员ID', name : 'member_id'},
                {display: '会员名称', name : 'member_name'}
            ],
            sortname: "member_id",
            sortorder: "desc",
            title: '会员实名认证列表'
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

