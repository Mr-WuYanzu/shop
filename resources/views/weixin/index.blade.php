<!DOCTYPE html>
<html>
<head>
	<title>主页</title>
</head>
<body>
	<button><a href="https://open.weixin.qq.com/connect/oauth2/authorize?appid=wxe5ff29e2590e9cef&redirect_uri=http%3A%2F%2F1809zhanghaibo.comcto.com%2Faa&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect">网页授权</a></button>
	<div style="padding-left:500px;padding-top:300px">
		<ul>
			@foreach($data as $k=>$v)
			<li goods_id="{{$v->goods_id}}">商品名称>><a href="/weixin/detail?goods_id={{$v->goods_id}}">{{$v->goods_name}}</a>------价格>>{{$v->goods_price}} <input type="button" value="加入购物车" class="but"></li>
			@endforeach

		</ul>---------------------------------------------------------------------------
	</div>
</body>
</html>
<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
	$('.but').click(function(){
		var goods_id=$(this).parent().attr('goods_id');
		location.href="/weixin/addCar/"+goods_id;
	})
	
</script>