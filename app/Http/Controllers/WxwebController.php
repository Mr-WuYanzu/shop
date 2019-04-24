<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\model\User;

class WxwebController extends Controller
{
    public function hd(){
   	  $code=$_GET['code'];
   	  $access_token=json_decode(file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe5ff29e2590e9cef&secret=02f770d9872fdf95de605f22c783fe46&code=".$code."&grant_type=authorization_code"),true);
   	  // echo "<pre>";print_r($access_token);echo "</pre>";
   	  //用户信息
   	  $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_token['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
   	  $userInfo=json_decode(file_get_contents($url),true);
	  // echo "<pre>";print_r($userInfo);echo "</pre>";
	  // 用户信息入库
	  $openid=$userInfo['openid'];
	  $res=User::where('openid',$openid)->first();
	  // dd($res);
	  if($res){
	  		echo '欢迎回来:'.$res['user_name'];
	  }else{
	  		
	  		$data=[
			  	'openid'=>$openid,
			  	'user_name'=>$userInfo['nickname'],
			  	'user_sex'=>$userInfo['sex'],
			  	'user_country'=>$userInfo['country'],
			  	'user_province'=>$userInfo['province'],
			  	'user_city'=>$userInfo['city'],
			  	'headimgurl'=>$userInfo['headimgurl']
		  	];
	   	  	User::insert($data);
	   	  	echo '欢迎:'.$userInfo['nickname'];
	  }
	  
    }
}
