<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title">
            <div class="subject">
                <h3>敏感词</h3>
                <h5>敏感词设置</h5>
            </div>
            <?php echo $output['top_link'];?> </div>
    </div>
    <div id="flexigrid"></div>
</div>
<script>
    $(function(){
        $("#flexigrid").flexigrid({
            url: 'index.php?act=sensitive&op=get_sensitive_xml',
            colModel : [
                {display: '<?php echo $lang['nc_handle'];?>', name : 'operation', width : 150, sortable : false, align: 'center', className: 'handle'},
                {display: '敏感词', name : 'admin_name', width : 100, sortable : false, align: 'left'},
                {display: '是否开启', name : 'admin_login_time', width : 120, sortable : false, align : 'left'}
            ],
            buttons : [
                {display: '<i class="fa fa-plus"></i>新增数据', name : 'add', bclass : 'add', onpress : fg_operation }
            ],
            title: '敏感词列表'
        });
    });

    function fg_operation(name, grid) {
        if (name == 'add') {
            window.location.href = 'index.php?act=sensitive&op=sensitive_add';
        }
    }
    function fg_operation_del(word_id){
        if(confirm('删除后将不能恢复，确认删除这项吗？')){
            window.location.href = 'index.php?act=sensitive&op=sensitive_del&word_id='+word_id;
        }
    }
</script>