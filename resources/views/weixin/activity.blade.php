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
            jsApiList: ['chooseImage','uploadImage','updateAppMessageShareData'] // 必填，需要使用的JS接口列表
        });
    </script>
</body>
</html>