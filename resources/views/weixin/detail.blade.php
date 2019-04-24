<!DOCTYPE html>
<html>
<head>
	<title>购物车</title>
</head>
<body>
	<div style="float:left">
		<h1>商品详情</h1>
		<p>商品名称:{{$goodsInfo->goods_name}}</p>
		<p>商品价格:{{$goodsInfo->goods_price}}</p>
		<p>浏览次数：{{$history_num}}</p>
	</div>
	<div style="float:right">
		<h1>浏览历史</h1>
		<ul>
			@foreach($data as $k=>$v)
			<li goods_id="{{$v->goods_id}}">商品名称>><a href="/weixin/detail?goods_id={{$v->goods_id}}">{{$v->goods_name}}</a>------价格>>{{$v->goods_price}} <input type="button" value="加入购物车" class="but"></li>
			@endforeach

		</ul>---------------------------------------------------------------------------
	</div>
	<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
	<script type="text/javascript">
		wx.config({
		    appId:"{{$sdk_config['appId']}}", // 必填，公众号的唯一标识
		    timestamp: "{{$sdk_config['timestamp']}}", // 必填，生成签名的时间戳
		    nonceStr: "{{$sdk_config['nonceStr']}}", // 必填，生成签名的随机串
		    signature: "{{$sdk_config['signature']}}",// 必填，签名
		    jsApiList: ['updateAppMessageShareData','onMenuShareAppMessage',''] // 必填，需要使用的JS接口列表
		});
		 wx.ready(function () {   //需在用户可能点击分享按钮前就先调用
		 	wx.onMenuShareAppMessage({
				title: '哈哈', // 分享标题
				desc: 'dd', // 分享描述
				link: 'http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id='+"{{$goodsInfo->goods_id}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
				imgUrl: 'http://1809zhanghaibo.comcto.com/img/link.jpg', // 分享图标
				type: 'link', // 分享类型,music、video或link，不填默认为link
				dataUrl: '', // 如果type是music或video，则要提供数据链接，默认为空
				success: function () {
					alert('分享成功');
				}
			});
			wx.onMenuShareTimeline({
			    title: '哈哈', // 分享标题
			    link: 'http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id='+"{{$goodsInfo->goods_id}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
			    imgUrl: 'http://1809zhanghaibo.comcto.com/img/link.jpg', // 分享图标
			    success: function () {
			    	// 用户点击了分享后执行的回调函数
			    	alert('分享成功');
				},
			})
		        wx.updateAppMessageShareData({
		            title: '哈哈', // 分享标题
		            desc: 'dd', // 分享描述
		            link: 'http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id='+"{{$goodsInfo->goods_id}}", // 分享链接，该链接域名或路径必须与当前页面对应的公众号JS安全域名一致
		            imgUrl: 'http://1809zhanghaibo.comcto.com/img/link.jpg', // 分享图标
		            success: function () {
		                alert('分享成功');
		            }
		        })
		    });
	</script>
</body>
</html>