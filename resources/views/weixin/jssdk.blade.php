<!DOCTYPE html>
<html>
<head>
	<title></title>
</head>
<body>
	<button id="img" style="width:90px;height:35px;margin:0 auto">请选择图片</button>
	<img src="" id="img0" width="130px">
	<img src="" id="img1" width="130px">
	<img src="" id="img2" width="130px">
	<img src="" id="img3" width="130px">
	<img src="" id="img4" width="130px">
	<img src="" id="img5" width="130px">
	<img src="" id="img6" width="130px">
	<img src="" id="img7" width="130px">
	<img src="" id="img8" width="130px">
	<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
	<script type="text/javascript">
		wx.config({
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
						$.each(localIds,function(k,v){
							var note='#img'+k;
							$(note).attr('src',v);
							// 上传图片
							wx.uploadImage({
								localId: v, // 需要上传的图片的本地ID，由chooseImage接口获得
								isShowProgressTips: 1, // 默认为1，显示进度提示
								success: function (r) {
									var serverId = r.serverId; // 返回图片的服务器端ID
									$.ajax({
										url:"/weixin/upload/?serverId="+serverId,
										type:'get',
										success:function(re){
										}
									})
								}
							});
						})
					}
				});
			})
		    
		});
	</script>
</body>
</html>