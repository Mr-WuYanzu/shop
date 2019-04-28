<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
class ActivityController extends Controller
{
    //获取带参数的二维码
    public function index(){
        $client=new Client();
        $url='https://api.weixin.qq.com/cgi-bin/qrcode/create?access_token='.getAccessToken();
        $arr=[
            'expire_seconds'=>604800,
            'action_name'=>'QR_SCENE',
            'action_info'=>[
                'scene'=>[
                    'scene_id'=>'666'
                ]
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);
        $res=json_decode($response->getBody(),true);
        $ticket=urlencode($res['ticket']);
        $ul='https://mp.weixin.qq.com/cgi-bin/showqrcode?ticket='.$ticket;
        return redirect($ul);
    }
    public function view(){
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
        $sdk_config=[
            'appId'=> $appid,
            'timestamp'=> $timestamp,
            'nonceStr'=> $noncestr,
            'signature'=> $sign,
        ];
        $data=[
            'sdk_config'=>$sdk_config
        ];
        return view('weixin.activity',$data);
    }
}
