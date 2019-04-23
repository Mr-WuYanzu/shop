<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<button id="img">请选择图片</button>
	<img src="" id="img0">
	<img src="" id="img1">
	<img src="" id="img2">
	<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
	<script type="text/javascript">
		wx.config({
		    debug: true, // 开启调试模式,调用的所有api的返回值会在客户端alert出来，若要查看传入的参数，可以在pc端打开，参数信息会通过log打出，仅在pc端时才会打印。
		    appId:"{{$sdk_config['appId']}}", // 必填，公众号的唯一标识
		    timestamp: "{{$sdk_config['timestamp']}}", // 必填，生成签名的时间戳
		    nonceStr: "{{$sdk_config['nonceStr']}}", // 必填，生成签名的随机串
		    signature: "{{$sdk_config['signature']}}",// 必填，签名
		    jsApiList: ['chooseImage','uploadImage'] // 必填，需要使用的JS接口列表
		});
		console.log("{{$sdk_config['signature']}}");
		wx.ready(function(){
			$('#img').click(function(){
				wx.chooseImage({
					count: 5, // 默认9
					sizeType: ['original', 'compressed'], // 可以指定是原图还是压缩图，默认二者都有
					sourceType: ['album', 'camera'], // 可以指定来源是相册还是相机，默认二者都有
					success: function (res) {
						var localIds = res.localIds; // 返回选定照片的本地ID列表，localId可以作为img标签的src属性显示图片
						var img='';
						$.each(localIds,function(k,v){
							img += v+',';
							var note='#img'+$k;
							$(note).attr('src',v);
						})
						img = img.substr(0,img.length-1);
						console.log(img);
					}
				});
			})
		    
		});
	</script>
</body>
</html>