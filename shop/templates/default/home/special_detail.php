<?php defined('InShopNC') or exit('Access Invalid!');?>
<style type="text/css">
.head-app {	display: block;}
.public-nav-layout {width: 1200px;}
.public-nav-layout .site-menu {	max-width: 788px;}
.wrapper {width: 1200px !important;}
.no-content {font: normal 16px/20px Arial, "microsoft yahei";	color: #999999;	text-align: center;	padding: 150px 0;}
.nc-appbar-tabs a.compare {	display: none !important;}
.public-nav-layout .category .sub-class {width: 746px;}
.public-nav-layout .category .sub-class-right {	display: none;}
</style>

<div id="body">
  <div id="cms_special_content" class="cms-content">
    <?php echo $output['special_file']; ?>
  </div>
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/dial.js"></script>
<script type="text/javascript" src="<?php echo SHOP_RESOURCE_SITE_URL;?>/js/jq.Slide.js" charset="utf-8"></script>

<script type="text/javascript">
  	if($("div#special_content_lottery_view").size() > 0){
  		var obj = $("div#special_content_lottery_view");
  		var lot_id = obj.find("input[nctype='lot_id']").val();
  		var obj_info = obj.find(".lottery_info");
  		var obj_view = obj.find(".lottery_view");
  		var lot_info = '';
		var qua_amount = '';
		var dial_err = '';
		var lottery;
		var prize_info = '';
  		$.ajax({
            type:'post',
            url:"index.php?act=lottery_dial&op=dial_info", 
            data:{lot_id:lot_id},
            dataType:'json',
            async:false,
            success:function(result){
                if(result.code == 200){
                	lot_info = result.datas.dial_info;
                	qua_amount = parseInt(result.datas.dail_qua);
                	if(lot_info.lot_bg != ''){
                		$('#special_content_lottery_view').css({'background':'url("'+lot_info.lot_bg+'") no-repeat center','background-size':'100%'});
                	}else{
                		$('#special_content_lottery_view').css({'background':'#faebc0'});
                	}                	

                	var html = '<div class="m-ui-dial" style="background:url(\''+lot_info.lot_dial_bg+'\') no-repeat center;background-size:100%">';
                	html += '<div id="js_pointer" class="pointer" style="background:url(\''+lot_info.lot_dial_pointer+'\') no-repeat center;background-size:100%">';
                	html += '<a class="btn" href="javascript:;"></a></div></div>';
	                obj_view.html(html);
	                var prize_list = result.datas.prize_list;
	                var p_html = '';
	                if(!$.isEmptyObject(prize_list)){
	                	p_html += '<div class="winner-list">';
		            		p_html += '<ul class="list-wrap sd" style="display: block; top: 10px; ">';
		            			for (var i = 0; i < prize_list.length; i++) {
		            				var info = prize_list[i];
		            				p_html += '<li class="winner-info"><span class="w-name">'+info.member_name+'</span><span class="w-prize">'+info.prize_info+'</span></li>';
		            			}
				            p_html += '</ul>';
				        p_html += '</div>';
	                }else{
	                	p_html += "<div class='winner-none'>还没出现中奖者，期待您赢取大奖！</div>";
	                }
	                obj_info.find('.winner-name').append(p_html);
	                var a= obj_info.find(".winner-info").length;
			        if(a>10){
			            $("#temp1").Slide({
			                effect : "scroolTxt",
			                speed : "normal",
			                timer : 1000,
			                steps:1,
							claNav: "list-wrap",
							claCon: "list-wrap"
			            });
			        }
                }
            }
        });

        lottery = new LotteryDial(document.getElementById('js_pointer'), {
	        speed: 30, //每帧速度
	        areaNumber: lot_info.prize_size?lot_info.prize_size:8 //奖区数量
	    });
		var index = -1;
		$('.pointer').on('click','a.btn',function(){
			<?php if($_SESSION['is_login'] == '1'){?>
				if(dial_err == '' && qua_amount > 0 && lot_info != ''){
	                lottery.draw();
	    		}else{
	    			alert('您的抽奖机会已用完啦~~~');
	    		}
			<?php }else{?>
				alert('请先登陆后再抽奖');
				return false;
			<?php }?>
		});

		lottery.on('start', function () {
			var key = '<?php echo $_SESSION['member_id'];?>';
	        //请求获取中奖结果
	        $.ajax({
	            type:'post',
	            url:'index.php?act=lottery_dial&op=dial_prize',
	            data:{key:key,lot_id:lot_id},
	            dataType:'json',
	            success:function(result){
	                if(result.code == 200){
	                    index = parseInt(result.datas.prize_grade,10);
	                    prize_info = result.datas.prize_info;
	                    if(index == -1){
	                        alert(prize_info.prize_detial);
	                        lottery.reset();
	                        return false;
	                    }
	                    lottery.setResult(index);
	                }else{
	                    alert(result.datas.error);
	                    lottery.reset();
	                }
	            }
	        });
	    });

	    lottery.on('end', function () {
	    	if(prize_info != ''){
	    		qua_amount--;
	            if(qua_amount == 0){
	                dial_err = '您的抽奖机会已用完啦~~~';
	            }
	            if(parseInt(prize_info.prize_type) == 0){
	                alert(prize_info.prize_detial);
	            }else{
	            	var p_html = '';
	            	if($('.winner-name .winner-list').size() > 0){
	            		p_html = '<li class="winner-info"><span class="w-name"><?php echo $_SESSION['member_name'];?></span><span class="w-prize">'+prize_info.prize_detial+'</span></li>';
	            		$('.winner-name .winner-list').find('ul').append(p_html);
	            		var a= $('.winner-name .winner-list').find(".winner-info").length;
				        if(a == 9){
				            $("#temp1").Slide({
				                effect : "scroolTxt",
				                speed : "normal",
				                timer : 1000,
				                steps:1,
								claNav: "list-wrap",
								claCon: "list-wrap"
				            });
				        }
	            	}else{
	            		p_html += '<div class="winner-list">';
		            	p_html += '<ul class="list-wrap sd" style="display: block; top: 10px; ">';
		            	p_html += '<li class="winner-info"><span class="w-name"><?php echo $_SESSION['member_name'];?></span><span class="w-prize">'+prize_info.prize_detial+'</span></li>';
				        p_html += '</ul>';
				        p_html += '</div>';
				        $('.winner-name').find('.winner-none').remove();
				        $('.winner-name').append(p_html);
	            	}	            	
	                alert('恭喜您获得：'+prize_info.rate_name+'【'+prize_info.prize_detial+'】');
	            }
	    		
	    	}
	    });
  	}
</script>
</div>
