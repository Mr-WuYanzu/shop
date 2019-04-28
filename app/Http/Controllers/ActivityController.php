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
}
