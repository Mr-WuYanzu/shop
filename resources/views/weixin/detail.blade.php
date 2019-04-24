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
	<button id="share" style="width:80px;height:30px">分享</button>
	<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
	<script type="text/javascript" src="http://res2.wx.qq.com/open/js/jweixin-1.4.0.js"></script>
	<script type="text/javascript">
		wx.config({
			debug:true,
		    appId:"{{$sdk_config['appId']}}", // 必填，公众号的唯一标识
		    timestamp: "{{$sdk_config['timestamp']}}", // 必填，生成签名的时间戳
		    nonceStr: "{{$sdk_config['nonceStr']}}", // 必填，生成签名的随机串
		    signature: "{{$sdk_config['signature']}}",// 必填，签名
		    jsApiList: ['chooseImage','uploadImage','updateAppMessageShareData'] // 必填，需要使用的JS接口列表
		});
	</script>
</body>
</html>