<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="javascript:history.back();" title="返回<?php echo $lang['pending'];?>列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>门店管理 - 查看门店“<?php echo $output['chain_info']['chain_name'];?>”的信息</h3>
        <h5>店铺的审核及经营管理操作</h5>
      </div>
    </div>
  </div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">所属店铺信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th class="w150">店铺名称：</th>
        <td colspan="20"><a href="<?php echo urlShop('show_store','index',array('store_id',$output['store_info']['store_id']));?>" target="_blank"><?php echo $output['store_info']['store_name'];?></a>【<?php echo $output['store_info']['store_company_name'];?>】</td>
      </tr>
      <tr>
        <th class="w150">主营商品：</th>
        <td colspan="20"><?php echo $output['store_info']['store_zy'];?></td>
      </tr>
      <tr>
        <th>商家账号：</th>
        <td><?php echo $output['store_info']['seller_name'];?></td>
        <th>店铺等级：</th>
        <td><?php echo $output['grade_info']['sg_name'];?>（<?php echo $output['grade_info']['sg_price']?>元/年）</td>
        <th>开店时间：</th>
        <td><?php echo ($t = $output['store_array']['store_time'])?@date('Y-m-d',$t):'--';?> </td>
      </tr>
      <tr>
        <th>所在地区：</th>
        <td><?php echo $output['store_info']['area_info'];?></td>
        <th>详细地址：</th>
        <td colspan="20"><?php echo $output['store_info']['store_address'];?></td>
      </tr>
      <tr>
        <th>商家电话：</th>
        <td><?php echo $output['store_info']['store_phone'];?></td>
        <th>商家旺旺：</th>
        <td><?php echo $output['store_info']['store_ww'];?></td>
        <th>商家QQ：</th>
        <td><?php echo $output['store_info']['store_qq'];?></td>
      </tr>
    </tbody>
  </table>
    <form id="form_store_verify" action="index.php?act=chain&op=chain_joinin_detail" method="post">
    <input id="verify_type" name="verify_type" type="hidden" />
    <input name="chain_id" type="hidden" value="<?php echo $output['chain_info']['chain_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">门店信息</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th class="w150">门店名称：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_name'];?></td>
        </tr>
        <tr>
          <th>登录名：</th>
          <td><?php echo $output['chain_info']['chain_user'];?></td>
          <th>门店结算周期：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_cycle'];?>&nbsp;天</td>
        </tr>
        <tr>
          <th>所在地区：</th>
          <td><?php echo $output['chain_info']['area_info'];?></td>
          <th>详细地址：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_address'];?></td>
        </tr>
        <tr>
          <th class="w150">联系方式：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_phone'];?></td>
        </tr>
        <tr>
          <th class="w150">营业时间：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_opening_hours'];?></td>
        </tr>
        <tr>
          <th class="w150">交通线路：</th>
          <td colspan="20"><?php echo $output['chain_info']['chain_traffic_line'];?></td>
        </tr>
        <tr>
          <th>门店LOGO：</th>
          <td colspan="20"><a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_logo'],$output['chain_info']['store_id']);?>" alt="" /> </a></td>
        </tr>
        <tr>
          <th>门店图片：</th>
          <td colspan="20"><a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_img'],$output['chain_info']['store_id']);?>" alt="" /> </a></td>
        </tr>
        <tr>
          <th>门店横幅：</th>
          <td colspan="20"><a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['chain_banner'],$output['chain_info']['store_id']);?>" alt="" /> </a></td>
        </tr>
        <?php if($output['chain_info']['is_own'] == 0){?>
        <tr>
          <th>应付保证金：</th>
          <td colspan="20"><?php echo C('chain_earnest_money');?>&nbsp;元</td>
        </tr>
        <?php if(in_array(intval($output['chain_info']['chain_state']), array(1, 5))) {?>
        <?php if($output['chain_info']['pay_certificate_type'] == 'offline'){?>
        <tr>
          <th>付款凭证：</th>
          <td colspan="20"><a nctype="nyroModal"  href="<?php echo getChainImage($output['chain_info']['paying_money_certificate'],$output['chain_info']['store_id']);?>"> <img src="<?php echo getChainImage($output['chain_info']['paying_money_certificate'],$output['chain_info']['store_id']);?>" alt="" /> </a></td>
        </tr>
        <?php }?>
        <tr>
          <th>付款凭证说明：</th>
          <td colspan="20"><?php echo $output['chain_info']['paying_money_certif_exp'];?></td>
        </tr>
        <?php } ?>
        <?php if(in_array(intval($output['chain_info']['chain_state']), array('2', '5'))) { ?>
        <tr>
          <th>审核意见：</th>
          <td colspan="20"><textarea id="chain_check_info" name="chain_check_info"></textarea></td>
        </tr>
        <?php } ?>
        <?php }?>
      </tbody>
    </table>
    <?php if(in_array(intval($output['chain_info']['chain_state']), array(2, 5))) { ?>
    <div id="validation_message" style="color:red;display:none;"></div>
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
            if($('#joinin_message').val() == '') {
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