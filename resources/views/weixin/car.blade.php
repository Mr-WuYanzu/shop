<!DOCTYPE html>
<html>
<head>
	<title>购物车</title>
</head>
<body>
	<div style="padding-left:500px;padding-top:300px">
		<ul>
			@foreach($data as $k=>$v)
			<li>购物车id>>{{$v['id']}}--商品名称>>{{$v['goods_name']}}------×{{$v['buy_num']}}</li>
			@endforeach

		</ul>----------------------------------------------------------------------------
		<form action="/weixin/success" method="post">
			总金额:{{$total/100}}。<input type="submit" value="去结算">
		</form>
	</div>
</body>
</html>