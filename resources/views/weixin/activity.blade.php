<!doctype html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Document</title>
</head>
<body>
    欢迎
    <script src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
    <script src="/js/weixin/jquery-3.2.1.min.js"></script>
    <script>
        wx.config({
            appId:"{{$sdk_config['appId']}}", // 必填，公众号的唯一标识
            timestamp: "{{$sdk_config['timestamp']}}", // 必填，生成签名的时间戳
            nonceStr: "{{$sdk_config['nonceStr']}}", // 必填，生成签名的随机串
            signature: "{{$sdk_config['signature']}}",// 必填，签名
            jsApiList: ['updateAppMessageShareData'] // 必填，需要使用的JS接口列表
        });
        wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
            wx.updateAppMessageShareData({
                title: '最新活动', // 分享标题
                desc: '进来看看', // 分享描述
                link: 'http://1809zhanghaibo.comcto.com/weixin/view', // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
                imgUrl: 'http://1809zhanghaibo.comcto.com/img/link (1).jpg', // 分享图标
                success: function () {
                    // 设置成功
                }
            })
        });
    </script>
</body>
</html>