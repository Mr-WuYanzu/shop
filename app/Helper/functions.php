<?php 
use Illuminate\Support\Facades\Redis;
	//获取access_token
	function getAccessToken(){
		$key="Access_token";
		$token=Redis::get($key);
		if($token){
			return $token;
		}else{
			$url='https://api.weixin.qq.com/cgi-bin/token?grant_type=client_credential&appid='.env('APPID').'&secret='.env('SECRET');
			$arr=json_decode(file_get_contents($url),true);
			if(isset($arr['access_token'])){
				Redis::set($key,$arr['access_token']);
				Redis::expire($key,3600);
				return $arr['access_token'];
			}
		}	
	}
	//获取jsapi_ticket
	function createticket($token){
		$key='ticket';
		$ticket=Redis::get($key);
		if($ticket){
			return $ticket;
		}else{
			$url='https://api.weixin.qq.com/cgi-bin/ticket/getticket?access_token='.$token.'&type=jsapi';
			$ticket=json_decode(file_get_contents($url),true);
			if(isset($ticket)){
				Redis::set($key,$ticket['ticket']);
				Redis::expire($key,3600);
				return $ticket['ticket'];
			}
		}
	}


 ?>