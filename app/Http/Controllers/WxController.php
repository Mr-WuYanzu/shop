<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class WxController extends Controller
{
    //微信第一次调用接口
    public function valid(){
    	echo $_GET['echostr'];
    }
    //接受微信推送消息
    public function event(){
    	$client=new Client();
        $data = file_get_contents("php://input");
        $time=date('Y-m-d H:i:s');
        $str=$time.$data."\n";
        is_dir('logs') or mkdir('logs',0777,true);
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $obj=simplexml_load_string($data);
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $type=$obj->MsgType;
    }
}
