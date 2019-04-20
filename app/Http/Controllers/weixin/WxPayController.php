<?php

namespace App\Http\Controllers\weixin;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Controllers\weixin\WXBizDataCryptController;
use Illuminate\Support\Str;
use App\model\Car;
use App\model\Order;
use App\model\Order_tail;
use DB;
class WxPayController extends Controller
{
	public $weixin_unifiedorder_url = 'https://api.mch.weixin.qq.com/pay/unifiedorder';
    public $weixin_notify_url = 'http://1809zhanghaibo.comcto.com/weixin/pay/notify';
    public $values=[];
    //微信下单接口
    public function test($order_sn){

    	$total_fee=1;
    	$order_id=$order_sn;
        $order=Order::where(['order_sn'=>$order_sn,'is_del'=>0])->first();
        if($order){
            // dd($order_id);
        	$info=[
        		'appid'		=>	env('WEIXIN_APPID_0'),
        		'mch_id'	=>	env('WEIXIN_MCH_ID'),
        		'nonce_str'	=>	Str::random(16),
        		'sign_type'	=>	'MD5',
        		'body'		=>'测试订单号：'.$order_id,
        		'out_trade_no'	=>	$order_id,
        		'total_fee'	=>	$total_fee,
        		'spbill_create_ip'	=>	$_SERVER['REMOTE_ADDR'],
        		'notify_url'	=> 	$this->weixin_notify_url,
        		'trade_type'	=> 'NATIVE'
        	];
        	$this->values=$info;
        	$this->SetSign();
        	// dd($this->values);
        	$xml=$this->toxml();
        	$res = $this->postXmlCurl($xml, $this->weixin_unifiedorder_url, $useCert = false, $second = 30);
        	// dd($res);
        	$obj=simplexml_load_string($res);
            
        	$data=[
        		'code_url'=>$obj->code_url,
                'oid'=>$order->toArray()['oid']
        	];
        }else{
            header('Refresh:2;url=/weixin/index');
            die('该订单号不存在,三秒钟后会跳转至主页');
        }
    	return view('weixin.test',$data);
    }
    //回调地址
    public function notify_url(){
    	$data=file_get_contents('php://input');
        $log_str = date('Y-m-d H:i:s') . "\n" . $data . "\n<<<<<<<";

    	file_put_contents('logs/wx_pay.logs',$log_str,FILE_APPEND);
        $xml = simplexml_load_string($data);
        if($xml->result_code=='SUCCESS' && $xml->return_code=='SUCCESS'){      //微信支付成功回调
            //验证签名
            $sign = true;
            if($sign){       //签名验证成功
                //TODO 逻辑处理  订单状态更新
                $out_trade_no=$xml->out_trade_no;
                $res=Order::where(['order_sn'=>$out_trade_no])->update(['is_del'=>1,'pay_status'=>1]);
                $goodsInfo=Order_tail::where(['order_sn'=>$out_trade_no])->get();
                foreach($goodsInfo->toArray() as $k=>$v){
                    $good=DB::table('p_wx_goods')->where(['goods_id'=>$v['goods_id']])->first();
                    DB::table('p_wx_goods')->where(['goods_id'=>$v['goods_id']])->update(['goods_num'=>$good->goods_num-$v['buy_num']]);
                }
            }else{
                //TODO 验签失败
                echo '验签失败，IP: '.$_SERVER['REMOTE_ADDR'];
                // TODO 记录日志
            }
        }
        $response = '<xml><return_code><![CDATA[SUCCESS]]></return_code><return_msg><![CDATA[OK]]></return_msg></xml>';
        echo $response;
    }
    //将数据转换为xml形式
    public function toxml(){
    	if(!is_array($this->values)||count($this->values)<=0){
    		die('数据格式异常');
    	}
    	$xml='<xml>';
    	foreach($this->values as $k=>$v){
    		if(is_numeric($v)){
    			$xml .= '<'.$k.'>'.$v.'</'.$k.'>';
    		}else{
    			$xml .= '<'.$k.'><![CDATA['.$v.']]></'.$k.'>';
    		}
    	}
    	$xml.='</xml>';
    	return $xml;
    }
    private  function postXmlCurl($xml, $url, $useCert = false, $second = 30)
    {
        $ch = curl_init();
        //设置超时
        curl_setopt($ch, CURLOPT_TIMEOUT, $second);
        curl_setopt($ch,CURLOPT_URL, $url);
        curl_setopt($ch,CURLOPT_SSL_VERIFYPEER,TRUE);
        curl_setopt($ch,CURLOPT_SSL_VERIFYHOST,2);//严格校验
        //设置header
        curl_setopt($ch, CURLOPT_HEADER, FALSE);
        //要求结果为字符串且输出到屏幕上
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, TRUE);
//		if($useCert == true){
//			//设置证书
//			//使用证书：cert 与 key 分别属于两个.pem文件
//			curl_setopt($ch,CURLOPT_SSLCERTTYPE,'PEM');
//			curl_setopt($ch,CURLOPT_SSLCERT, WxPayConfig::SSLCERT_PATH);
//			curl_setopt($ch,CURLOPT_SSLKEYTYPE,'PEM');
//			curl_setopt($ch,CURLOPT_SSLKEY, WxPayConfig::SSLKEY_PATH);
//		}
        //post提交方式
        curl_setopt($ch, CURLOPT_POST, TRUE);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $xml);
        //运行curl
        $data = curl_exec($ch);
        //返回结果
        if($data){
            curl_close($ch);
            return $data;
        } else {
            $error = curl_errno($ch);
            curl_close($ch);
            die("curl出错，错误码:$error");
        }
    }
    //生成签名
    public function SetSign(){
    	$sign=$this->makeSign();
    	$this->values['sign']=$sign;
    	return $sign;
    }
    //制作签名
    public function makeSign(){
    	//第一步,排序签名,对参数按照key=value的格式，并按照参数名ASCII字典序排序
    	Ksort($this->values);
    	$str=$this->ToUrlParams();
    	//第二步,拼接API密钥并加密
    	$sign_str=$str.'&key='.env('WEIXIN_MCH_KEY');
    	$sign=MD5($sign_str);
    	//第三步,将所有的字符转换为大写
    	$string=strtoupper($sign);
    	return $string;
    }
    public function ToUrlParams(){
    	$str='';
    	foreach($this->values as $k=>$v){
    		if($k!='sign'&&$v!=''&&!is_array($v)){
    			$str .= $k.'='.$v.'&';
    		}
    	}
    	$str=trim($str,'&');
    	return $str;
    }
    //验证微信支付是否成功
    public function paystatus(){
        $res=Order::where(['oid'=>$_GET['oid'],'is_del'=>0])->first();

        $response=[
            'code'=>2,
        ];
        if($res){
            if($res->pay_status==1){
                $response=[
                    'code'=>1,
                    'font'=>'支付成功'
                ];
            }
        }
        return json_encode($response);
    }
    //支付成功
    public function supay(){
        echo "支付成功";
    }

}
