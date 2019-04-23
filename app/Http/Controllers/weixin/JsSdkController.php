<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;

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
    //图片上传
    public function upload(){
    	$client=new Client();
    	$serverId=$_GET['serverId'];
    	$sId=date('Y-m-d h:i').'>>>>>>>>>'.$serverId."\n";
    	file_put_contents('logs/wx_upload.logs', $sId,FILE_APPEND);
    	$url='https://api.weixin.qq.com/cgi-bin/media/get?access_token='.getAccessToken().'&media_id='.$serverId;
    	$img=$client->get(new Uri($url));
        //获取文件类型
        $headers=$img->getHeaders();
        $img_name=$headers['Content-disposition'][0];
        $fileInfo=substr($img_name,'-15');
        $img_name=substr(md5(time().mt_rand(1111,9999)),5,8).$fileInfo;
        $img_name=rtrim($img_name,'"');
    	Storage::put('weixin/img/'.$img_name, $img->getBody());

    }
}
