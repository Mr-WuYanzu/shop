<!DOCTYPE html>
<html>
<head>
	<title>浏览历史</title>
</head>
<body>
	<div style="padding-left:500px;padding-top:300px">
		<h1>浏览历史</h1>
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