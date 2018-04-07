<?php defined('InShopNC') or exit('Access Invalid!');?>
<form method="post" name="form1" id="form1" action="<?php echo urlAdminDistribute('distri_member', 'batch_auth');?>">
    <input type="hidden" name="form_submit" value="ok" />
    <input type="hidden" value="<?php echo $output["member_id"];?>" name="member_id">
    <div class="ncap-form-default">
        <dl class="row">
            <dt class="tit">
                <label for="verify_type">审核操作：</label>
            </dt>
            <dd class="opt">
                <div class="onoff">
                    <label for="verify_1" class="cb-enable selected" ><span>通过</span></label>
                    <label for="verify_2" class="cb-disable" ><span>拒绝</span></label>
                    <input id="verify_1" name="verify_type" checked="checked" value="1" type="radio">
                    <input id="verify_2" name="verify_type" value="0" type="radio">
                </div>
            </dd>
        </dl>
        <dl class="row">
            <dt class="tit">
                <label for="joinin_message">审核意见：</label>
            </dt>
            <dd class="opt">
                <textarea rows="6" class="tarea" cols="60" name="joinin_message" id="joinin_message"></textarea>
            </dd>
        </dl>
        <div class="bot"><a href="javascript:void(0);" class="ncap-btn-big ncap-btn-green" nctype="btn_submit"><?php echo $lang['nc_submit'];?></a></div>
    </div>
</form>
<script>
    $(function(){
        $('a[nctype="btn_submit"]').click(function(){
            ajaxpost('form1', '', '', 'onerror');
        });
        //自定义radio样式
        $(".cb-enable").click(function(){
            var parent = $(this).parents('.onoff');
            $('.cb-disable',parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox',parent).attr('checked', true);
        });
        $(".cb-disable").click(function(){
            var parent = $(this).parents('.onoff');
            $('.cb-enable',parent).removeClass('selected');
            $(this).addClass('selected');
            $('.checkbox',parent).attr('checked', false);
        });
    });
</script>