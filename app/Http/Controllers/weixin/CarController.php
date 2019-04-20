<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Session;
use App\Http\Controllers\Controller;
use App\model\Car;
use App\model\Order;
use App\model\Order_tail;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\DB;

class CarController extends Controller
{
	//主页
	public function index(){
		$data=DB::table('p_wx_goods')->where('status',0)->get();
		return view('weixin.index',['data'=>$data]);
	}
	//添加购物车
	public function addCar($goods_id=0){
		$session_token=Session::getId();
		if($goods_id<=0){
			header('Refresh:2;url=/weixin/index');
			die('请选择正确的商品，三秒后会跳转至主页');
		}else{
			$where=[
				'goods_id'=>$goods_id,
				'status'=>0
			];
			$res=DB::table('p_wx_goods')->where($where)->first();
			if(!$res){
				header('Refresh:2;url=/weixin/index');
				die('此商品已下架或不存在，三秒后会跳转至主页');
			}
			$carInfo=Car::where(['uid'=>Auth::id(),'goods_id'=>$goods_id,'status'=>0])->first();
			if($carInfo){
				$rs=Car::where(['id'=>$carInfo->id])->update(['buy_num'=>$carInfo->buy_num+1]);
				if($rs){
					return redirect('/weixin/car');
				}else{
					header('Refresh:2;url=/weixin/index');
					die('加入购物车失败，三秒后会跳转至主页');
				}
			}else{
				$data=[
					'goods_id'=>$goods_id,
					'uid'=>Auth::id(),
					'add_time'=>time(),
					'buy_num'=>1,
					'session_token'=>$session_token
				];
				$rs=Car::insert($data);
				if($rs){
					return redirect('/weixin/car');
				}else{
					header('Refresh:2;url=/weixin/index');
					die('加入购物车失败，三秒后会跳转至主页');
				}
			}
			
		}
	}
	//购物车列表
    public function car(){
    	$session_token=Session::getId();
    	$uid=Auth::id();
    	$carInfo=Car::where(['uid'=>$uid,'p_wx_goods.status'=>0,'session_token'=>$session_token])
    					->join('p_wx_goods','p_wx_goods.goods_id','=','p_wx_car.goods_id')
    					->get();
    	// dd($car_model);
    	if(count($carInfo)>0){
    		$carInfo=$carInfo->toArray();
    		$total=0;
    		foreach($carInfo as $k=>$v){
    			$total += $v['buy_num']*$v['goods_price'];
    		}
    		$data=[
    			'data'=>$carInfo,
    			'total'=>$total
    		];
    	}else{
    		header('Refresh:2;url=/weixin/index');
    		die('购物车为空,三秒钟后会跳转至主页');
    	}
    	return view('weixin.car',$data);
    }
    //购物车结算生成订单
    public function success(){
    	$uid=Auth::id();

    	$carInfo=Car::where(['uid'=>$uid,'p_wx_goods.status'=>0,'p_wx_car.status'=>0])
    					->join('p_wx_goods','p_wx_goods.goods_id','=','p_wx_car.goods_id')
    					->get();
    	if(count($carInfo)<=0){
    		header('Refresh:2;url=/weixin/index');
    		die('购物车为空,三秒钟后会跳转至主页');
    	}
    	$str=substr(md5(Str::random(32)),-15);
    	$order_sn='1809a'.date('ymdhi').'jd'.$str;
    	$carInfo=$carInfo->toArray();
    		$total=0;
    		foreach($carInfo as $k=>$v){
    			$total += $v['buy_num']*$v['goods_price'];
    		}
    	$data=[
    		'order_sn'=>$order_sn,
    		'uid'=>Auth::id(),
    		'order_amount'=>$total/100,
    		'add_time'=>time()
    	];
    	$oid=Order::insertGetId($data);


    	foreach($carInfo as $k=>$v){
    		$info=[
    			'order_sn'=>$order_sn,
    			'oid'=>$oid,
    			'uid'=>Auth::id(),
    			'goods_id'=>$v['goods_id'],
    			'goods_name'=>$v['goods_name'],
    			'goods_price'=>$v['goods_price'],
    			'buy_num'=>$v['buy_num']
    		];
    		Order_tail::insert($info);
    	}
    	Car::where(['uid'=>Auth::id()])->update(['status'=>1]);
        return redirect('/weixin/order');
    }
    public function order(){
        $order=Order::where(['uid'=>Auth::id(),'is_del'=>0])->get()->toArray();
        return view('weixin.order',['order'=>$order]);
    }
}
