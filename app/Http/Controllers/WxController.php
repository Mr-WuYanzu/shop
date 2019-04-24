<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DB;

class WxController extends Controller
{
    //微信第一次调用接口
    public function valid(){
    	echo $_GET['echostr'];
    }
    //接受微信推送消息
    public function event(){
    	// $client=new Client();
        $data = file_get_contents("php://input");
        $time=date('Y-m-d H:i:s');
        $str=$time.$data."\n";
        is_dir('logs') or mkdir('logs',0777,true);
        file_put_contents("logs/wx_event.log",$str,FILE_APPEND);
        $obj=simplexml_load_string($data);
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $type=$obj->MsgType;
        if($type=='text'){
        	// echo "ss";
        	// echo $obj->Content;
        	if($obj->Content=="最新商品"){
        		$data=DB::table('p_wx_goods')->orderBy('add_time','desc')->limit(5)->get();
        		foreach($data as $k=>$v){
        			echo '<xml>
						  <ToUserName><![CDATA['.$openid.']]></ToUserName>
						  <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
						  <CreateTime>'.time().'</CreateTime>
						  <MsgType><![CDATA[news]]></MsgType>
						  <ArticleCount>1</ArticleCount>
						  <Articles>
						    <item>
						      <Title><![CDATA['.$v->goods_name.']]></Title>
						      <Description><![CDATA['.$v->desc.']]></Description>
						      <PicUrl><![CDATA['.'http://1809zhanghaibo.comcto.com/img'.']]></PicUrl>
						      <Url><![CDATA[url]]></Url>
						    </item>
						  </Articles>
						</xml>';
        		}
        	}
        }
    }
}
