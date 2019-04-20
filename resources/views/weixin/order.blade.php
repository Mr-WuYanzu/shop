<!DOCTYPE html>
<html>
<head>
	<title>购物车</title>
</head>
<body>
	<div style="padding-left:500px;padding-top:300px">
		<ul>
			@foreach($order as $k=>$v)
			<li order_sn="{{$v['order_sn']}}">订单号>>{{$v['order_sn']}}--商品总价{{$v['order_amount']}} <input type="button" value="微信支付" class="but"></li>
			@endforeach

		</ul>
	</div>
</body>
</html>
<script type="text/javascript" src="/js/weixin/jquery-3.2.1.min.js"></script>
<script type="text/javascript">
	$('.but').click(function(){
		var order_sn=$(this).parent().attr('order_sn');
		location.href="/weixin/wxPay/"+order_sn;
	})
	
</script>