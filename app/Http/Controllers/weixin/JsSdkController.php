<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;

class JsSdkController extends Controller
{
    //微信jssdk
    public function jssdk(){
    	$token=getAccessToken();
    	$appid=env('APPID');
    	//计算签名
    	$noncestr=Str::random(12);
    	$jsapi_ticket=createticket($token);
    	$timestamp=time();
    	$url="http://mp.weixin.qq.com?params=value";
    	$str=$jsapi_ticket.'&'.$noncestr.'&'.$timestamp.'&'.$url;
    	$sign=sha1($str);
    	// echo $sign;die;
    	$sdk_config=[
		    'appId'=> $appid, 
		    'timestamp'=> $timestamp, 
		    'nonceStr'=> $noncestr, 
		    'signature'=> $sign,
    	];
    	$data=[
    		'sdk_config'=>$sdk_config
    	];


    	return view('weixin.jssdk',$data);
    }
}
