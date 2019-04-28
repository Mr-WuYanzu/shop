<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use GuzzleHttp\Client;
use GuzzleHttp\Psr7\Uri;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Storage;
use DB;
use App\model\TmpUserModel;
use App\model\User;

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
        		$v=DB::table('p_wx_goods')->orderBy('add_time','desc')->first();
        		// dd($data);
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
						      <PicUrl><![CDATA['.'http://1809zhanghaibo.comcto.com/img/link.jpg'.']]></PicUrl>
						      <Url><![CDATA['.'http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id='.$v->goods_id.']]></Url>
						    </item>
						  </Articles>
						</xml>';
        	}
        }else if($type=='image'){
            $media_id=$obj->MediaId;
        }else if($type='event'){
            $event=$obj->Event;
            $EventKey=$obj->EventKey;
            $goods_id=substr($EventKey,0,1);

            if($goods_id==3){
                $goods_id=substr($EventKey,1);
                return redirect('http://1809zhanghaibo.comcto.com/weixin/detail?goods_id='.$goods_id);
            }else{
                switch($event){
                    case 'SCAN':
                        if(isset($obj->EventKey)){
                            $this->qrcode($obj);//扫带参数二维码
                        }
                        break;
                    case 'subscribe':
                        $this->subscribe($obj);//扫码关注
                        break;
                    default:
                        $response_xml = 'success';
                }

                echo $response_xml;
            }

        }
    }
    //扫带参数二维码
    public function qrcode($obj){
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $EventKey=$obj->EventKey;

        //验证用户是否存在
        $res=TmpUserModel::where(['openid'=>$openid,'event_key'=>$EventKey])->first();
        if($res){
            $response_xml= '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[最新活动]]></Title>
                          <Description><![CDATA[description1]]></Description>
                          <PicUrl><![CDATA['.'http://1809zhanghaibo.comcto.com/img/link (1).jpg'.']]></PicUrl>
                          <Url><![CDATA['.'http://1809zhanghaibo.comcto.com/weixin/view'.']]></Url>
                        </item>
                      </Articles>
                    </xml>';
        }else{
            $response_xml= '<xml>
                      <ToUserName><![CDATA['.$openid.']]></ToUserName>
                      <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                      <CreateTime>'.time().'</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[最新活动]]></Title>
                          <Description><![CDATA[description1]]></Description>
                          <PicUrl><![CDATA['.'http://1809zhanghaibo.comcto.com/img/link (1).jpg'.']]></PicUrl>
                          <Url><![CDATA['.'http://1809zhanghaibo.comcto.com/weixin/view'.']]></Url>
                        </item>
                      </Articles>
                    </xml>';
            $data=[
                'openid'=>$openid,
                'event_key'=>$EventKey,
                'create_time'=>$obj->CreateTime
            ];
            TmpUserModel::insert($data);
        }
        die($response_xml);

    }
//用户扫码关注
    public function subscribe($obj){
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;
        $res=User::where('openid',$openid)->first();
        if($res){
            $response_xml='<xml>
                              <ToUserName><![CDATA['.$openid.']]></ToUserName>
                              <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                              <CreateTime>'.time().'</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA[欢迎回来：'.$res->user_name.']]></Content>
                            </xml>';
        }else{
            $u=$this->WxUserTail($obj->FromUserName);
            $info=[
                'openid'=>$u['openid'],
                'nickname'=>$u['nickname'],
                'sex'=>$u['sex'],
                'city'=>$u['city'],
                'province'=>$u['province'],
                'country'=>$u['country'],
                'headimgurl'=>$u['headimgurl'],
                'subscribe_time'=>$u['subscribe_time'],
                'subscribe_scene'=>$u['subscribe_scene']
            ];
            $id=User::insertGetId($info);
            $response_xml='<xml>
                              <ToUserName><![CDATA['.$openid.']]></ToUserName>
                              <FromUserName><![CDATA['.$wx_id.']]></FromUserName>
                              <CreateTime>'.time().'</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA[欢迎关注：'.$u["nickname"].']]></Content>
                            </xml>';
        }
        die($response_xml);
    }
//查询用户资料
    public function WxUserTail($openid){
        $data=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".$this->access_token()."&openid=".$openid."&lang=zh_CN");
        $arr=json_decode($data,true);
        return $arr;
    }


    //获取微信的素材
    public function fodder(){
       return view('weixin.upload');
    }
}
