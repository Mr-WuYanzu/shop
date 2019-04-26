<?php

namespace App\Admin\Controllers;

use App\model\User;
use App\Http\Controllers\Controller;
use Encore\Admin\Controllers\HasResourceActions;
use DB;
use Encore\Admin\Layout\Content;
use GuzzleHttp\Client;
class MessageController extends Controller
{
    use HasResourceActions;

    public function index(Content $content){
        $data=DB::table('p_wx_user')->get();
        return $content
            ->header('用户管理')
            ->description('群发消息')
            ->body(view('admin.weixin.message',['data'=>$data]));
    }
    public function Add(){
        $client=new Client();
        $openid=$_GET['openid'];
        $text=$_GET['text'];
        $openid=explode(',',$openid);

        $arr=[
            'touser' => $openid,
            'msgtype' => 'text',
            'text' => [
                'content'=>$text
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getAccessToken();
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);

    }
}
