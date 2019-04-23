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
    	// dd($_SERVER);
    	//当前网页的url
    	$url=$_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'] .$_SERVER['REQUEST_URI'];
    	// echo $url;die;
    	$str="jsapi_ticket=$jsapi_ticket&noncestr=$noncestr&timestamp=$timestamp&url=$url";
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
