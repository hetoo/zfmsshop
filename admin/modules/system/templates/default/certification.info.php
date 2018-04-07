<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
    <div class="fixed-bar">
        <div class="item-title"><a class="back" href="index.php?act=certification" title="返回<?php echo $lang['pending'];?>列表"><i class="fa fa-arrow-circle-o-left"></i></a>
            <div class="subject">
                <h3>实名认证 - 认证详情</h3>
                <h5>会员实名认证管理</h5>
            </div>
        </div>
    </div>
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
        <thead>
        <tr>
            <th colspan="20">会员详情</th>
        </tr>
        </thead>
        <tbody>
        <tr>
            <th class="w150">会员名称：</th>
            <td colspan="20"><?php echo $output['member_info']['member_name'];?></td>
        </tr>
        <tr>
            <th>真实姓名：</th>
            <td><?php echo $output['member_info']['id_card_name'];?></td>
            <th>身份证号：</th>
            <td><?php echo $output['member_info']['id_card_code'];?></td>
            <th>注册时间：</th>
            <td><?php echo date('Y-m-d H:i:s',$output['member_info']['member_time']);?></td>
        </tr>
        <tr>
            <th>绑定手机：</th>
            <td><?php echo ($output['member_info']['member_mobile_bind'] == 1)?$output['member_info']['member_mobile']:'--';?></td>
            <th>绑定邮箱：</th>
            <td><?php echo ($output['member_info']['member_email_bind'] == 1)?$output['member_info']['member_email']:'--';?></td>
            <th>最后登录时间：</th>
            <td><?php echo date('Y-m-d H:i:s',$output['member_info']['member_login_time']);?></td>
        </tr>
        <tr>
            <th class="w150">身份证正面：</th>
            <td colspan="20">
                <a nctype="nyroModal"  href="<?php echo getMemberIDCardImageUrl($output['id_card_img1']);?>"> <img src="<?php echo getMemberIDCardImageUrl($output['id_card_img1']);?>" alt="" /> </a>
            </td>
        </tr>
        <tr>
            <th class="w150">身份证反面：</th>
            <td colspan="20">
                <a nctype="nyroModal"  href="<?php echo getMemberIDCardImageUrl($output['id_card_img2']);?>"> <img src="<?php echo getMemberIDCardImageUrl($output['id_card_img2']);?>" alt="" /> </a>
            </td>
        </tr>
        </tbody>
    </table>

    <form id="form_store_verify" action="index.php?act=certification&op=auth" method="post">
        <input id="verify_type" name="verify_type" type="hidden" />
        <input name="member_id" type="hidden" value="<?php echo $output['member_info']['member_id'];?>" />
        <?php if(in_array(intval($output['member_info']['id_card_state']), array(1))) { ?>
            <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
                <tbody>
                <tr>
                    <th>审核意见：</th>
                    <td colspan="2">
                        <textarea id="message" name="message"></textarea>
                        <div id="validation_message" style="color:red;display:none;"></div>
                    </td>
                </tr>
                </tbody>
            </table>
            <div class="bottom">
                <a id="btn_pass" class="ncap-btn-big ncap-btn-green mr10" href="JavaScript:void(0);">通过</a>
                <a id="btn_fail" class="ncap-btn-big ncap-btn-red" href="JavaScript:void(0);">拒绝</a>
            </div>
        <?php } ?>
    </form>
</div>
<script type="text/javascript" src="<?php echo ADMIN_RESOURCE_URL;?>/js/jquery.nyroModal.js"></script>
<script type="text/javascript" src="<?php echo RESOURCE_SITE_URL;?>/js/jquery.poshytip.min.js" charset="utf-8"></script>

<script type="text/javascript">
    $(document).ready(function(){
        $('a[nctype="nyroModal"]').nyroModal();

        $('#btn_fail').on('click', function() {
            if($('#message').val() == '') {
                $('#validation_message').text('请输入审核意见');
                $('#validation_message').show();
                return false;
            } else {
                $('#validation_message').hide();
            }
            if(confirm('确认拒绝申请？')) {
                $('#verify_type').val('fail');
                $('#form_store_verify').submit();
            }
        });
        $('#btn_pass').on('click', function() {
            $('#validation_message').hide();
            if(confirm('确认通过申请？')) {
                $('#verify_type').val('pass');
                $('#form_store_verify').submit();
            }
        });
    });
</script>