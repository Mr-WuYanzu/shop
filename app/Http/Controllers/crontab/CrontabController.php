<?php

namespace App\Http\Controllers\Crontab;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\model\Order;

class CrontabController extends Controller
{
    //删除超过三十分钟未支付的订单
    public function delOrder(){
    	$time=time();
    	$data=Order::where(['is_del'=>0,'pay_status'=>0])->get();
    	foreach($data as $k=>$v){
    		if($time-$v->add_time>1800){
    			Order::where(['oid'=>$v->oid])->update(['is_del'=>1]);
    		}
    	}
    }
}
