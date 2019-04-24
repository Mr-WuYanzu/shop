<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxwebController extends Controller
{
    public function hd(){
   	  $code=$_GET['code'];
   	  $access_token=json_decode(file_get_contents("https://api.weixin.qq.com/sns/oauth2/access_token?appid=wxe5ff29e2590e9cef&secret=02f770d9872fdf95de605f22c783fe46&code=".$code."&grant_type=authorization_code"),true);
   	  echo "<pre>";print_r($access_token);echo "</pre>";
   	  //用户信息
   	  $url='https://api.weixin.qq.com/sns/userinfo?access_token='.$access_tolen['access_token'].'&openid='.$access_token['openid'].'&lang=zh_CN';
   	  $userInfo=json_decode(file_get_contents($url),true);
	  echo "<pre>";print_r($userInfo);echo "</pre>";
    }
}
