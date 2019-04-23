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
</body>
</html>