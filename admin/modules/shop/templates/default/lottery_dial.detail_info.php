<?php defined('InShopNC') or exit('Access Invalid!');?>

<div class="page">
  <div class="fixed-bar">
    <div class="item-title"><a class="back" href="index.php?act=lottery_dial&op=detail_list&lot_id=<?php echo $_GET['lot_id']?>" title="返回抽奖管理列表"><i class="fa fa-arrow-circle-o-left"></i></a>
      <div class="subject">
        <h3>大转盘活动管理 - 抽奖管理 - 查看会员“<?php echo $output['lot_info']['member_name'];?>”的中奖信息</h3>
        <h5><?php echo $lang['nc_store_manage_subhead'];?></h5>
      </div>
    </div>
  </div>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">活动信息</th>
      </tr>
    </thead>
    <tbody>
      <tr>
        <th>活动名称：</th>
        <td><?php echo $output['dial_info']['lot_name'];?></td>
        <th>活动时间：</th>
        <td colspan="20"><?php echo date('Y-m-d H:i',$output['dial_info']['start_time']);?> 至 <?php echo date('Y-m-d H:i',$output['dial_info']['end_time']);?></td>
      </tr>
      <tr>
        <th class="w150">活动说明：</th>
        <td colspan="20"><?php echo $output['dial_info']['lot_discription'];?></td>
      </tr>      
      <tr>
        <th>中奖率：</th>
        <td><?php echo $output['dial_info']['lot_weight'];?>%</td>
        <th>抽取方式：</th>
        <td><?php echo $output['dial_info']['lot_type']== 0?'按会员抽取':'按订单抽取';?></td>
        <th>每个会员ID<br>抽取次数：</th>
        <td><?php echo $output['dial_info']['lot_count']?$output['dial_info']['lot_count']:1;?> 次</td>
      </tr>
    </tbody>
  </table>
  <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
    <thead>
      <tr>
        <th colspan="20">活动奖项信息</th>
      </tr>
    </thead>
    <tbody>
    <?php if(!empty($output['dial_info']['lot_info'])){ $count_prize = count($output['dial_info']['lot_info']);?>
    <?php foreach($output['dial_info']['lot_info'] as $k => $val){?>
    <?php if($val['prize_type'] != 0){?>
      <tr>
        <th class="w150"><?php echo $val['rate_name']?>:</th>
        <td>
          <?php 
              switch($val['prize_type']){
                case 1:
                      echo $val['prize']['prize_num']." 积分,共 ".$val['prize']['prize_amount']." 份";
                      break;
                case 2:
                      echo $val['prize']['coupon_title']." 平台红包,共 ".$val['prize']['prize_amount']." 份";
                      break;
                case 3:
                      echo $val['prize']['prize_name'].",共 ".$val['prize']['prize_amount']." 份";
                      break;
              }                
          ?>
        </td>
      </tr>
      <?php }?>
     <?php }?>
    <?php }else{?>
      <tr>
        <td  colspan="20">奖项信息读取失败</td>
      </tr>
    <?php }?>
    </tbody>
  </table>
  <form id="form_send_prize" action="index.php?act=lottery_dial&op=change_state" method="post">
    <input name="m_id" type="hidden" value="<?php echo $_GET['m_id'];?>" />
    <input name="lot_id" type="hidden" value="<?php echo $_GET['lot_id'];?>" />
    <input name="act_id" type="hidden" value="<?php echo $_GET['act_id'];?>" />
    <table border="0" cellpadding="0" cellspacing="0" class="store-joinin">
      <thead>
        <tr>
          <th colspan="20">抽奖结果</th>
        </tr>
      </thead>
      <tbody>
        <tr>
          <th>奖项名称：</th>
          <td colspan="20"><?php echo $output['lot_info']['rate_name']?></td>
        </tr>
        <tr>
          <th>奖品详情：</th>
          <td colspan="20">
            <?php 
              $prize = unserialize($output['lot_info']['prize_info']);
              switch($output['lot_info']['rate_type']){
                  case 0: 
                        echo $prize['unprize'];break;
                  case 1:
                        echo $prize['prize_num']." 积分";break;
                  case 2:
                        echo $val['prize']['coupon_title']." 平台红包";break;
                  case 3:
                        echo $val['prize']['prize_name'];break;
                }  
            ?>          
          </td>
        </tr>
        <tr>
          <th>派奖状态：</th>
          <td colspan="20"><?php echo $output['lot_info']['prize_state']?'已派奖':'未派奖';?></td>
        </tr>
        <?php if($output['lot_info']['prize_state'] == 0){?>
        <tr>
          <th>派奖说明：</th>
          <td colspan="2"><textarea id="prize_content" name="prize_content"></textarea></td>
        </tr>
        <?php }?>
      </tbody>
    </table>
    <?php if($output['lot_info']['prize_state'] == 0){?>
      <div class="bottom">
        <a id="btn_send" class="ncap-btn-big ncap-btn-green mr10" href="JavaScript:void(0);">派奖</a>
    <?php }else{?>
      <div class="bottom">
        <?php }?>
        <a class="ncap-btn-big" href="index.php?act=lottery_dial&op=detail_list&lot_id=<?php echo $_GET['lot_id']?>">返回</a> 
      </div>
  </form>
</div>
<script type="text/javascript">
    $(document).ready(function(){
        $('#btn_send').on('click', function() {
          if(confirm('确认派发奖品吗？')) {
            $('#form_send_prize').submit();
          }
        });
    });
</script>