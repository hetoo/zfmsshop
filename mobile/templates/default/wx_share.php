/* 微信分享JS
 * @copyright  Copyright (c) 2007-2018 ShopNC Inc. (http://www.shopnc.net)
 * @license    http://www.shopnc.net
 * @link       http://www.shopnc.net
*/

wx.config({
    debug: false,
    appId: '<?php echo $output['appid'];?>', 
    timestamp: <?php echo TIMESTAMP;?>,
    nonceStr: '<?php echo $output['nonceStr'];?>', 
    signature: '<?php echo $output['signature'];?>',
    jsApiList: ['onMenuShareTimeline','onMenuShareAppMessage']
});

wx.ready(function () {
    wx.onMenuShareTimeline({
        title: "<?php echo $output['title'];?>", //分享标题
        link: "<?php echo $output['link'];?>", //分享链接
        imgUrl: "<?php echo $output['imgUrl'];?>", //分享图标
        success: function () { 
        },
        cancel: function () { 
        }
    });
    wx.onMenuShareAppMessage({
        title: "<?php echo $output['title'];?>", //分享标题
        desc: "<?php echo $output['desc'];?>", //分享描述
        link: "<?php echo $output['link'];?>", //分享链接
        imgUrl: "<?php echo $output['imgUrl'];?>", //分享图标
        type: '', 
        dataUrl: '',
        success: function () { 
        },
        cancel: function () { 
        }
    });
});
_wap_wx = 0;

/* 版权：天津市网城天创科技有限责任公司	*/