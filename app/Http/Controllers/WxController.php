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
use App\model\Goods;
use App\model\Signin;
use Illuminate\Support\Facades\Redis;

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
            }else if(strpos($obj->Content,'+天气')){
                $city=explode('+',$obj->Content)[0];
                $url="https://free-api.heweather.net/s6/weather/now?parameters&location=".$city."&key=HE1904161030301545";
                $arr=json_decode(file_get_contents($url),true);
                if($arr['HeWeather6'][0]['status']!=='ok'){
                    echo "<xml>
                              <ToUserName><![CDATA[".$openid."]]></ToUserName>
                              <FromUserName><![CDATA[".$wx_id."]]></FromUserName>
                              <CreateTime>".time()."</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA[城市信息有误]]></Content>
                          </xml>";
                }else{
                    $city=$arr['HeWeather6'][0]['basic']['parent_city'];
                    $cond_txt=$arr['HeWeather6'][0]['now']['cond_txt'];
                    $fl=$arr['HeWeather6'][0]['now']['fl'];
                    $tmp=$arr['HeWeather6'][0]['now']['tmp'];
                    $wind_dir=$arr['HeWeather6'][0]['now']['wind_dir'];
                    $wind_sc=$arr['HeWeather6'][0]['now']['wind_sc'];
                    $wind_spd=$arr['HeWeather6'][0]['now']['wind_spd'];
                    $str="城市:".$city."\n"."天气状况:".$cond_txt."\n"."体感温度:".$fl."\n"."温度:".$tmp."\n"."风向:".$wind_dir."\n"."风力:".$wind_sc."\n"."风速:".$wind_spd."公里/小时"."\n";
                    echo "<xml>
                              <ToUserName><![CDATA[".$openid."]]></ToUserName>
                              <FromUserName><![CDATA[".$wx_id."]]></FromUserName>
                              <CreateTime>".time()."</CreateTime>
                              <MsgType><![CDATA[text]]></MsgType>
                              <Content><![CDATA[".$str."]]></Content>
                          </xml>";
                }
            }else{
                $sedata=$this->seach($obj);
            }
        }else if($type=='image'){
            $media_id=$obj->MediaId;
        }else if($type='event'){
            $event=$obj->Event;
            $EventKey=$obj->EventKey;
            $goods_id=substr($EventKey,0,1);
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
    //用户搜索商品
    public function seach($obj){
        $where=[
            'goods_name'=>$obj->Content
        ];
        $wx_id=$obj->ToUserName;
        $openid=$obj->FromUserName;

        //将用户搜索商品存入redis
        Redis::set('goods_name',$where['goods_name']);

            $data=Goods::where($where)->first();
        if ($data) {
            Redis::set('goods_name',$data->goods_name);
            echo '<xml>
                      <ToUserName><![CDATA[' . $openid . ']]></ToUserName>
                      <FromUserName><![CDATA[' . $wx_id . ']]></FromUserName>
                      <CreateTime>' . time() . '</CreateTime>
                      <MsgType><![CDATA[news]]></MsgType>
                      <ArticleCount>1</ArticleCount>
                      <Articles>
                        <item>
                          <Title><![CDATA[' . $data->goods_name . ']]></Title>
                          <Description><![CDATA[' . $data->desc . ']]></Description>
                          <PicUrl><![CDATA[http://1809zhanghaibo.comcto.com/' . $data->goods_img . ']]></PicUrl>
                          <Url><![CDATA[http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id=' . $data->goods_id . ']]></Url>
                        </item>
                      </Articles>
                    </xml>';

            $Info=json_encode($data);
            Redis::set('goods_name',$data->goods_name);
        }

    }
    //将用户搜索的商品名存储数据库
    public function savegoodsname(){
        $goods_name=Redis::get('goods_name');
        if($goods_name){
            DB::table('k_goods')->insert(['goods_name'=>$goods_name]);
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
        $data=file_get_contents("https://api.weixin.qq.com/cgi-bin/user/info?access_token=".getAccessToken()."&openid=".$openid."&lang=zh_CN");
        $arr=json_decode($data,true);
        return $arr;
    }

//创建微信菜单
    public function create_menu(){
        $redirect_url=urlencode('http://1809zhanghaibo.comcto.com/web/hd');
        $url='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.$redirect_url.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';
        $redirect_ul=urlencode('http://1809zhanghaibo.comcto.com/weixin/signIn');
        $ul='https://open.weixin.qq.com/connect/oauth2/authorize?appid='.env('APPID').'&redirect_uri='.$redirect_ul.'&response_type=code&scope=snsapi_userinfo&state=STATE#wechat_redirect';

        $arr=[
            'button'=>[
                [
                    'type'=>'view',
                    'name'=>'最新福利',
                    'url'=>$url
                ],
                [
                    'name'=>'菜单',
                    'sub_button'=>[
                        [
                            'name'=>'发送位置',
                            'type'=>'location_select',
                            'key'=>'WZ_SH_00'
                        ]
                    ]
                ],
                [
                    'type'=>'view',
                    'name'=>'签到',
                    'url'=>$ul
                ],
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $ul='https://api.weixin.qq.com/cgi-bin/menu/create?access_token='.getAccessToken();
        $response=$client->request('POST',$ul,[
            'body'=>$str
        ]);
        $res=json_decode($response->getBody(),true);
        if($res['errcode']==0){
            echo "创建成功";
        }
    }
//网页授权回调
    public function hd(){
        $code=$_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('SECRET').'&code='.$code.'&grant_type=authorization_code';
        $arr=json_decode(file_get_contents($url),true);
        //获取用户信息
        $userInfo=json_decode(file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$arr['openid'].'&lang=zh_CN'),true);
        if(isset($userInfo['errcode'])){
            die('未知错误');
        }else {
            echo "欢迎：" . $userInfo['nickname'] . '即将跳转至福利页面';
            header('Refresh:3;url=http://1809zhanghaibo.comcto.com/weixin/detail/?goods_id=10');
        }
    }
//用户签到
    public function signIn(){
        $code=$_GET['code'];
        $url='https://api.weixin.qq.com/sns/oauth2/access_token?appid='.env('APPID').'&secret='.env('SECRET').'&code='.$code.'&grant_type=authorization_code';
        $arr=json_decode(file_get_contents($url),true);
        $openid=$arr['openid'];
        //获取用户信息
        $userInfo=json_decode(file_get_contents('https://api.weixin.qq.com/sns/userinfo?access_token='.$arr['access_token'].'&openid='.$openid.'&lang=zh_CN'),true);
        $res=Signin::where(['openid'=>$openid])->first();
        if($res){
            echo "签到成功";
        }else{

            Signin::insert(['openid'=>$openid]);
            echo "欢迎:".$userInfo['nickname'].'首次签到';
        }
        $signin_key='signin:key:'.$userInfo['openid'];
        $num=Redis::incr($signin_key);
        $time_key='time:'.$userInfo['openid'];
        $date=date('Y-m-d H:i:s');
        $time=Redis::zAdd($time_key,time(),$date);
        $date_time=Redis::zRevRange($time_key,0,10000000000);

        return view('weixin.signin',['num'=>$num,'date_time'=>$date_time]);


    }
    //微信群发消息
    public function send_valid(){
        $url='https://api.weixin.qq.com/cgi-bin/message/mass/send?access_token='.getAccessToken();
        $openid=User::get();
        if($openid){
            $openid=$openid->toArray();
        }
        $openid=array_column($openid,'openid');
        $array=[
            '真理惟一可靠的标准就是永远自相符合。 —— 欧文',
            '土地是以它的肥沃和收获而被估价的；才能也是土地，不过它生产的不是粮食，而是真理。如果只能滋生瞑想和幻想的话，即使再大的才能也只是砂地或盐池，那上面连小草也长不出来的。 —— 别林斯基',
            '我需要三件东西：爱情友谊和图书。然而这三者之间何其相通！炽热的爱情可以充实图书的内容，图书又是人们最忠实的朋友。 —— 蒙田',
            '时间是一切财富中最宝贵的财富。 —— 德奥弗拉斯多',
            '世界上一成不变的东西，只有“任何事物都是在不断变化的”这条真理。 —— 斯里兰卡',
            '过放荡不羁的生活，容易得像顺水推舟，但是要结识良朋益友，却难如登天。 —— 巴尔扎克',
            '这世界要是没有爱情，它在我们心中还会有什么意义！这就如一盏没有亮光的走马灯。 —— 歌德',
            '生活有度，人生添寿。 —— 书摘',
            '相信谎言的人必将在真理之前毁灭。 —— 赫尔巴特',
            '真正的科学家应当是个幻想家；谁不是幻想家，谁就只能把自己称为实践家。 —— 巴尔扎克',
            '爱情原如树叶一样，在人忽视里绿了，在忍耐里露出蓓蕾。 —— 何其芳',
            '一件事实是一条没有性别的真理。 —— 纪伯伦',
            '友谊是一棵可以庇荫的树。 —— 柯尔律治',
            '理想是人生的太阳。 —— 德莱赛',
            '如果你浪费了自己的年龄，那是挺可悲的。因为你的青春只能持续一点儿时间——很短的一点儿时间。 —— 王尔德',
            '我读的书愈多，就愈亲近世界，愈明了生活的意义，愈觉得生活的重要。 —— 高尔基',
            '我从来没有说过这句话。  ——鲁迅',
            '劳于读书，逸于作文。 —— 程端礼',
            '笨蛋自以为聪明，聪明人才知道自己是笨蛋。 —— 莎士比亚',
            '毫无经验的初恋是迷人的，但经得起考验的爱情是无价的。 —— 马尔林斯基',
            '良好的健康状况和高度的身体训练，是有效的脑力劳动的重要条件。 —— 克鲁普斯卡娅'
        ];
        $num=array_rand($array);
        $con=$array[$num].'                     '.date('Y-m-d H:i:s');
        $arr=[
            'touser'=>[
                $openid
            ],
            'msgtype'=>'text',
            'text'=>[
                'content'=>$con
            ]
        ];
        $str=json_encode($arr,JSON_UNESCAPED_UNICODE);
        $client=new Client();
        $response=$client->request('POST',$url,[
            'body'=>$str
        ]);
        if(json_decode($response->getBody(),true)['errcode']==0){
            echo '发送成功';
        }else{
            echo '发送失败';
        }
    }

    //获取微信的素材
    public function fodder(){
       return view('weixin.upload');
    }
}
